#!/usr/bin/env python3
"""
Consolidated Chatbot Implementation
Combines features from EnhancedChatbot and EnhancedChatbotV2 with proper training data integration
"""

import json
import os
import time
import re
from typing import Dict, Any, Optional, Tuple, List

# Handle both relative and absolute imports
try:
    from .unsupervised_learning import UnsupervisedLearning
    from .neural_network import ChatbotNeuralNetwork
    from .model_evaluator import ModelEvaluator
    from .token_predictor import TokenPredictor
    from .conversation_context import ConversationContextManager
    from .knowledge_base import get_knowledge_base, KnowledgeBase
except ImportError:
    from backend.chatbot.core.unsupervised_learning import UnsupervisedLearning
    from backend.chatbot.core.neural_network import ChatbotNeuralNetwork
    from backend.chatbot.core.model_evaluator import ModelEvaluator
    from backend.chatbot.core.token_predictor import TokenPredictor
    from backend.chatbot.core.conversation_context import ConversationContextManager
    from backend.chatbot.core.knowledge_base import get_knowledge_base, KnowledgeBase

# PERFORMANCE: Lazy import of heavy ML libraries
# These will be imported only when advanced NLP is explicitly enabled
TRANSFORMERS_AVAILABLE = None  # Will be set on first access
SKLEARN_AVAILABLE = None  # Will be set on first access
_transformers_pipeline = None
_SentenceTransformer = None
_TfidfVectorizer = None
_cosine_similarity = None


def _check_transformers_available():
    """Lazy check for transformers availability"""
    global TRANSFORMERS_AVAILABLE, _transformers_pipeline, _SentenceTransformer
    if TRANSFORMERS_AVAILABLE is None:
        try:
            from transformers import pipeline
            from sentence_transformers import SentenceTransformer
            _transformers_pipeline = pipeline
            _SentenceTransformer = SentenceTransformer
            TRANSFORMERS_AVAILABLE = True
        except ImportError:
            TRANSFORMERS_AVAILABLE = False
    return TRANSFORMERS_AVAILABLE


def _check_sklearn_available():
    """Lazy check for sklearn availability"""
    global SKLEARN_AVAILABLE, _TfidfVectorizer, _cosine_similarity
    if SKLEARN_AVAILABLE is None:
        try:
            from sklearn.feature_extraction.text import TfidfVectorizer
            from sklearn.metrics.pairwise import cosine_similarity
            _TfidfVectorizer = TfidfVectorizer
            _cosine_similarity = cosine_similarity
            SKLEARN_AVAILABLE = True
        except ImportError:
            SKLEARN_AVAILABLE = False
    return SKLEARN_AVAILABLE


class LanguageDetector:
    """Unified language detector - optimized for speed with fast pattern-based detection"""
    
    def __init__(self, use_advanced: bool = False):  # Default to False for speed
        """
        Initialize language detector
        
        Args:
            use_advanced: Whether to use transformer-based detection (disabled by default for speed)
        """
        # PERFORMANCE: Advanced detection disabled by default - pattern-based is 100x faster
        # Only check transformers availability if advanced mode is requested
        self.use_advanced = use_advanced and _check_transformers_available() if use_advanced else False
        
        # Extended patterns for accurate fast detection
        self.language_patterns = {
            'nl': ['de', 'het', 'een', 'en', 'van', 'in', 'op', 'voor', 'met', 'aan', 'bij', 'door', 
                   'over', 'onder', 'tussen', 'na', 'tot', 'uit', 'zonder', 'tegen', 'langs', 'rond', 
                   'om', 'doorheen', 'kunnen', 'hebben', 'zijn', 'worden', 'wat', 'hoe', 'waar', 'welke',
                   'onze', 'ons', 'uw', 'jullie', 'prijs', 'kost', 'boot', 'boten', 'huur', 'huren',
                   'hoeveel', 'wanneer', 'graag', 'bedankt', 'dank', 'hallo', 'dag', 'goedemorgen'],
            'en': ['the', 'and', 'or', 'but', 'in', 'on', 'at', 'to', 'for', 'of', 'with', 'by', 
                   'can', 'have', 'be', 'will', 'do', 'say', 'get', 'make', 'go', 'know', 'what',
                   'how', 'where', 'which', 'your', 'our', 'price', 'cost', 'boat', 'boats', 'rent',
                   'much', 'when', 'please', 'thanks', 'thank', 'hello', 'hi'],
            'de': ['der', 'die', 'das', 'und', 'oder', 'aber', 'in', 'mit', 'für', 'von', 'zu', 
                   'können', 'haben', 'sein', 'werden', 'machen', 'gehen', 'kommen', 'was', 'wie',
                   'wo', 'welche', 'ihre', 'unser', 'preis', 'kosten', 'boot', 'boote', 'mieten',
                   'wieviel', 'wann', 'bitte', 'danke', 'hallo', 'guten']
        }
        
        # Lazy-load transformer classifier only when explicitly needed
        self._language_classifier = None
    
    @property
    def language_classifier(self):
        """Lazy-load language classifier only when needed"""
        if self._language_classifier is None and self.use_advanced:
            try:
                if _transformers_pipeline:
                    self._language_classifier = _transformers_pipeline("text-classification", 
                                                       model="papluca/xlm-roberta-base-language-detection",
                                                       return_all_scores=True)
            except Exception as e:
                print(f"⚠️ Could not load advanced language classifier: {e}")
                self.use_advanced = False
        return self._language_classifier
    
    def detect_language(self, text: str) -> Tuple[str, float]:
        """
        Detect language with confidence score - FAST pattern-based by default
        
        Returns:
            Tuple of (language_code, confidence_score)
        """
        # FAST PATH: Always use pattern-based detection first (sub-millisecond)
        lang, confidence = self._pattern_based_detection(text)
        
        # Only use slow transformer if pattern detection has very low confidence
        # AND advanced mode is explicitly enabled
        if confidence < 0.3 and self.use_advanced and self.language_classifier:
            try:
                results = self.language_classifier(text[:512])
                lang_map = {'nl': 'nl', 'en': 'en', 'de': 'de', 'nld': 'nl', 'eng': 'en', 'deu': 'de'}
                
                for result in results[0]:
                    lang_code = result['label'].lower()
                    if lang_code in lang_map:
                        return lang_map[lang_code], result['score']
            except Exception as e:
                print(f"⚠️ Advanced language detection failed: {e}")
        
        return lang, confidence
    
    def _pattern_based_detection(self, text: str) -> Tuple[str, float]:
        """Fast pattern-based language detection with improved accuracy"""
        text_lower = text.lower()
        words_in_text = set(text_lower.split())
        scores = {}
        
        for lang, patterns in self.language_patterns.items():
            # Count both word matches and substring matches for short queries
            word_matches = sum(1 for word in patterns if word in words_in_text)
            # Also check substrings for compound words and inflections
            substring_matches = sum(0.5 for word in patterns if word in text_lower and word not in words_in_text)
            scores[lang] = word_matches + substring_matches
        
        if scores:
            best_lang = max(scores, key=scores.get)
            max_score = scores[best_lang]
            
            # Better confidence calculation based on word count and match ratio
            total_words = len(words_in_text)
            if total_words == 0:
                return 'nl', 0.5
            
            # Higher confidence for more matches relative to query length
            confidence = min((max_score * 1.5) / max(total_words, 1), 1.0)
            
            # Boost confidence if there's a clear winner
            sorted_scores = sorted(scores.values(), reverse=True)
            if len(sorted_scores) > 1 and sorted_scores[0] > sorted_scores[1] * 1.5:
                confidence = min(confidence + 0.2, 1.0)
            
            return best_lang, max(confidence, 0.4)  # Minimum 0.4 confidence
        
        return 'nl', 0.5  # Default to Dutch


class SimilarityMatcher:
    """Unified similarity matcher - optimized for speed with lazy-loading"""
    
    def __init__(self, use_advanced: bool = False, cache_size: int = 10000):  # Default False for speed
        """
        Initialize similarity matcher
        
        Args:
            use_advanced: Whether to use sentence transformers (disabled by default for speed)
            cache_size: Maximum size of embedding cache (LRU)
        """
        # PERFORMANCE: Advanced features disabled by default - TF-IDF/word overlap is much faster
        # Only check availability if advanced mode is requested
        self.use_advanced = use_advanced and _check_transformers_available() if use_advanced else False
        self._sentence_model = None  # Lazy-loaded
        self._sentence_model_loaded = False
        self.tfidf_vectorizer = None
        
        # Initialize TF-IDF vectorizer only if sklearn is available (checked lazily)
        if _check_sklearn_available() and _TfidfVectorizer:
            self.tfidf_vectorizer = _TfidfVectorizer(
                max_features=500,  # Reduced for speed
                stop_words=None,
                ngram_range=(1, 2)
            )
        
        # LRU cache for embeddings (prevents unbounded growth)
        self._embedding_cache = {}
        self._cache_size = cache_size
    
    @property
    def sentence_model(self):
        """Lazy-load sentence transformer only when actually needed"""
        if not self._sentence_model_loaded and self.use_advanced:
            self._sentence_model_loaded = True
            try:
                if _SentenceTransformer:
                    self._sentence_model = _SentenceTransformer('paraphrase-multilingual-MiniLM-L12-v2')
                    print("✅ Loaded multilingual sentence transformer (lazy)")
            except Exception as e:
                print(f"⚠️ Could not load sentence transformer: {e}")
                self.use_advanced = False
        return self._sentence_model
    
    def get_embedding(self, text: str) -> tuple:
        """
        Get embedding for text (cached)
        
        Returns:
            Tuple representation of embedding (for caching)
        """
        # Check cache first
        if text in self._embedding_cache:
            return self._embedding_cache[text]
        
        embedding = None
        
        if self.use_advanced and self.sentence_model:
            try:
                import numpy as np
                embedding_array = self.sentence_model.encode([text])[0]
                embedding = tuple(embedding_array.tolist())  # Convert to tuple for caching
            except Exception as e:
                print(f"⚠️ Error generating embedding: {e}")
        
        if embedding is None:
            # Fallback to simple feature vector
            embedding = tuple(self._simple_feature_vector(text))
        
        # Add to cache (with size limit)
        if len(self._embedding_cache) >= self._cache_size:
            # Remove oldest entry (simple FIFO)
            oldest_key = next(iter(self._embedding_cache))
            del self._embedding_cache[oldest_key]
        
        self._embedding_cache[text] = embedding
        return embedding
    
    def get_embeddings_batch(self, texts: List[str], batch_size: int = 32) -> List[tuple]:
        """
        Get embeddings for multiple texts in batches (more efficient)
        
        Args:
            texts: List of texts to embed
            batch_size: Number of texts to process at once
            
        Returns:
            List of embedding tuples
        """
        if not texts:
            return []
        
        # Check cache first
        cached_embeddings = {}
        texts_to_process = []
        indices_to_process = []
        
        for i, text in enumerate(texts):
            if text in self._embedding_cache:
                cached_embeddings[i] = self._embedding_cache[text]
            else:
                texts_to_process.append(text)
                indices_to_process.append(i)
        
        # Process uncached texts in batches
        if texts_to_process and self.use_advanced and self.sentence_model:
            try:
                import numpy as np
                all_embeddings = []
                
                # Process in batches
                for batch_start in range(0, len(texts_to_process), batch_size):
                    batch_texts = texts_to_process[batch_start:batch_start + batch_size]
                    batch_embeddings = self.sentence_model.encode(batch_texts, show_progress_bar=False)
                    
                    for emb in batch_embeddings:
                        embedding_tuple = tuple(emb.tolist())
                        all_embeddings.append(embedding_tuple)
                
                # Cache and store results
                for idx, emb_tuple in zip(indices_to_process, all_embeddings):
                    # Add to cache (with size limit)
                    if len(self._embedding_cache) >= self._cache_size:
                        oldest_key = next(iter(self._embedding_cache))
                        del self._embedding_cache[oldest_key]
                    
                    self._embedding_cache[texts[idx]] = emb_tuple
                    cached_embeddings[idx] = emb_tuple
                
            except Exception as e:
                print(f"⚠️ Error in batch embedding generation: {e}")
                # Fallback to individual processing
                for idx in indices_to_process:
                    cached_embeddings[idx] = self.get_embedding(texts[idx])
        else:
            # Fallback to individual processing
            for idx in indices_to_process:
                cached_embeddings[idx] = self.get_embedding(texts[idx])
        
        # Return in original order
        return [cached_embeddings[i] for i in range(len(texts))]
    
    def _simple_feature_vector(self, text: str) -> List[float]:
        """Create simple feature vector as fallback"""
        import numpy as np
        words = text.lower().split()
        features = np.zeros(100)
        
        boat_words = [
            'boot', 'boat', 'tender', 'electrosloep', 'zeilboot', 'kano', 'kajak', 'sup',
            'prijs', 'price', 'kost', 'cost', 'reserveren', 'book', 'boeken', 'booking',
            'opening', 'hours', 'uren', 'contact', 'locatie', 'location', 'waar', 'where'
        ]
        
        for i, word in enumerate(boat_words[:100]):
            if word in text.lower():
                features[i] = 1.0
        
        return features.tolist()
    
    def calculate_similarity(self, text1: str, text2: str) -> float:
        """Calculate semantic similarity between two texts"""
        import numpy as np
        
        if self.use_advanced and self.sentence_model:
            try:
                emb1 = np.array(self.get_embedding(text1))
                emb2 = np.array(self.get_embedding(text2))
                
                # Calculate cosine similarity
                similarity = np.dot(emb1, emb2) / (
                    np.linalg.norm(emb1) * np.linalg.norm(emb2)
                )
                return float(similarity)
            except Exception as e:
                print(f"⚠️ Error calculating semantic similarity: {e}")
        
        # Fallback to TF-IDF similarity
        if self.tfidf_vectorizer and _check_sklearn_available() and _cosine_similarity:
            try:
                tfidf_matrix = self.tfidf_vectorizer.fit_transform([text1, text2])
                similarity = _cosine_similarity(tfidf_matrix[0:1], tfidf_matrix[1:2])[0][0]
                return float(similarity)
            except Exception as e:
                print(f"⚠️ Error calculating TF-IDF similarity: {e}")
        
        # Final fallback to word overlap
        return self._word_overlap_similarity(text1, text2)
    
    def _word_overlap_similarity(self, text1: str, text2: str) -> float:
        """Calculate simple word overlap similarity"""
        words1 = set(text1.lower().split())
        words2 = set(text2.lower().split())
        
        if not words1 or not words2:
            return 0.0
        
        intersection = words1.intersection(words2)
        union = words1.union(words2)
        
        return len(intersection) / len(union) if union else 0.0


class Chatbot:
    """
    Consolidated Chatbot Implementation
    Optimized for sub-2-second response times using knowledge base fast-path
    """
    
    def __init__(self, training_data_file: str = None, use_advanced_nlp: bool = False):
        """
        Initialize chatbot - optimized for fast responses
        
        Args:
            training_data_file: Path to training data file (defaults to enhanced_training_data.json with fallback)
            use_advanced_nlp: Whether to use advanced NLP features (disabled by default for speed)
        """
        import time
        start_time = time.time()
        
        # PERFORMANCE: Disable advanced NLP by default for sub-2s responses
        self.use_advanced_nlp = use_advanced_nlp
        
        # Initialize fast components (pattern-based, no ML models)
        self.language_detector = LanguageDetector(use_advanced=False)  # Always fast
        self.similarity_matcher = SimilarityMatcher(use_advanced=use_advanced_nlp)  # Lazy-loaded
        self.learning_system = UnsupervisedLearning()
        
        # Load training data (try enhanced first, then fallback)
        if training_data_file is None:
            # Try enhanced_training_data.json first
            enhanced_file = os.path.join(
                os.path.dirname(__file__), '..', 'training', 'enhanced_training_data.json'
            )
            if os.path.exists(enhanced_file):
                # Extract improved_responses from enhanced format
                try:
                    with open(enhanced_file, 'r', encoding='utf-8') as f:
                        enhanced_data = json.load(f)
                        if 'training_data' in enhanced_data:
                            self.training_data = enhanced_data['training_data']
                        else:
                            self.training_data = enhanced_data
                    self.training_data_file = enhanced_file
                    print(f"✅ Loaded enhanced training data from {enhanced_file}")
                except Exception as e:
                    print(f"⚠️ Could not load enhanced training data: {e}")
                    training_data_file = self._get_default_training_file()
            else:
                training_data_file = self._get_default_training_file()
        
        if training_data_file:
            self.training_data_file = training_data_file
            self.training_data = self._load_training_data()
        else:
            self.training_data = {"improved_responses": {}}
        
        self.improved_responses = self.training_data.get("improved_responses", {})
        
        # Initialize knowledge base (primary response source)
        try:
            self.knowledge_base = get_knowledge_base()
            print("✅ Knowledge base initialized")
        except Exception as e:
            print(f"⚠️ Could not initialize knowledge base: {e}")
            self.knowledge_base = None
        
        # PERFORMANCE: Neural network is lazy-initialized and trained only when needed
        # Since we use knowledge base as primary, neural network is backup only
        self._neural_network = None
        self._nn_config = {
            'input_size': 200 if use_advanced_nlp else 100,
            'hidden_sizes': [128, 64] if use_advanced_nlp else [64, 32],
            'output_size': 8 if use_advanced_nlp else 5
        }
        
        # PERFORMANCE: Skip advanced features by default - lazy-load when needed
        self._advanced_features_initialized = False
        self.question_embeddings = {}  # Will be populated on demand
        
        # Initialize model evaluator
        self.evaluator = ModelEvaluator(self.training_data)
        
        # PERFORMANCE: Token predictor is lazy-loaded only when use_token_prediction=True
        self._token_predictor = None
        self._token_predictor_use_transformer = use_advanced_nlp
        
        # Initialize conversation context manager
        context_storage_dir = os.path.join(
            os.path.dirname(__file__), '..', '..', 'data', 'conversations'
        )
        os.makedirs(context_storage_dir, exist_ok=True)
        self.context_manager = ConversationContextManager(
            storage_dir=context_storage_dir,
            session_timeout=3600  # 1 hour
        )
        
        init_time = time.time() - start_time
        print(f"✅ Chatbot initialized in {init_time:.2f}s with {len(self.improved_responses)} training samples")
        print(f"   🚀 Fast mode: Knowledge base + pattern-based detection (sub-2s responses)")
    
    @property
    def neural_network(self):
        """Lazy-load neural network only when needed"""
        if self._neural_network is None:
            print("🧠 Lazy-loading neural network...")
            self._neural_network = ChatbotNeuralNetwork(
                input_size=self._nn_config['input_size'],
                hidden_sizes=self._nn_config['hidden_sizes'],
                output_size=self._nn_config['output_size']
            )
            # Quick training with fewer epochs for faster startup
            self._train_neural_network(epochs=10)
            print("✅ Neural network initialized")
        return self._neural_network
    
    @property
    def token_predictor(self):
        """Lazy-load token predictor only when explicitly requested"""
        if self._token_predictor is None:
            print("🧠 Lazy-loading token prediction model...")
            try:
                self._token_predictor = TokenPredictor(use_transformer=self._token_predictor_use_transformer)
                self._update_token_predictor_with_training_data()
                print("✅ Token predictor initialized")
            except Exception as e:
                print(f"⚠️ Could not initialize token predictor: {e}")
                return None
        return self._token_predictor
    
    def _get_default_training_file(self) -> str:
        """Get default training data file path"""
        return os.path.abspath(os.path.join(
            os.path.dirname(__file__), '..', 'training', 'data', 'training_data.json'
        ))
    
    def _load_training_data(self) -> Dict[str, Any]:
        """Load training data from file"""
        if os.path.exists(self.training_data_file):
            try:
                with open(self.training_data_file, 'r', encoding='utf-8') as f:
                    return json.load(f)
            except Exception as e:
                print(f"⚠️ Could not load training data: {e}")
        
        return {"improved_responses": {}}
    
    def _initialize_advanced_features(self):
        """Initialize advanced features for better matching"""
        if not self.use_advanced_nlp:
            return
        
        print("🧠 Initializing advanced features...")
        
        # Pre-compute embeddings for all training questions (with limit)
        max_embeddings = 1000  # Limit to prevent memory issues
        questions = list(self.improved_responses.keys())[:max_embeddings]
        
        # Use batch processing for efficiency
        if questions and hasattr(self.similarity_matcher, 'get_embeddings_batch'):
            print(f"   Processing {len(questions)} questions in batches...")
            embedding_tuples = self.similarity_matcher.get_embeddings_batch(questions, batch_size=64)
            self.question_embeddings = {
                question: emb_tuple 
                for question, emb_tuple in zip(questions, embedding_tuples)
            }
        else:
            # Fallback to individual processing
            self.question_embeddings = {}
            for question in questions:
                self.question_embeddings[question] = self.similarity_matcher.get_embedding(question)
        
        print(f"✅ Initialized embeddings for {len(self.question_embeddings)} questions")
    
    def _train_neural_network(self, epochs: int = 10):
        """Train neural network on training data (fast training with fewer epochs)"""
        if not self.improved_responses:
            print("⚠️ No training data available for neural network")
            return
        
        # Prepare training data
        chatbot_training_data = []
        for question, response_data in self.improved_responses.items():
            response_type = response_data.get('response_type', 'general')
            chatbot_training_data.append((question, response_type))
        
        if chatbot_training_data:
            # Use fewer epochs for faster training
            self._neural_network.train_on_chatbot_data(chatbot_training_data, epochs=epochs)
    
    def _update_token_predictor_with_training_data(self):
        """Update token predictor with training conversations"""
        if not self.token_predictor:
            return
        
        try:
            # Convert training data to conversation format
            conversations = []
            for question, response_data in self.improved_responses.items():
                conversation = [
                    {'role': 'user', 'content': question},
                    {'role': 'assistant', 'content': response_data.get('corrected', '')}
                ]
                conversations.append(conversation)
            
            self.token_predictor.update_with_training_data(conversations)
        except Exception as e:
            print(f"⚠️ Could not update token predictor: {e}")
    
    def process_query(
        self,
        query: str,
        website_content: str = None,
        conversation_history: List[Dict[str, str]] = None,
        session_id: str = None,
        use_token_prediction: bool = False  # Disabled by default for speed
    ) -> Dict[str, Any]:
        """
        Process a user query and return enhanced response with full conversation context
        OPTIMIZED: Uses knowledge base fast-path for sub-2-second responses
        
        Args:
            query: User's query
            website_content: Website content for fallback responses
            conversation_history: Previous conversation messages (list of dicts with 'role' and 'content')
            session_id: Optional session ID for context tracking
            use_token_prediction: Whether to use token prediction (disabled by default for speed)
            
        Returns:
            Enhanced response dictionary
        """
        start_time = time.time()
        
        # Get or create conversation context (fast - just dictionary lookup)
        if session_id:
            context = self.context_manager.get_or_create_context(session_id)
            context.add_message('user', query)
            conversation_history = context.get_conversation_history()
        elif conversation_history is None:
            conversation_history = []
        
        # FAST PATH: Pattern-based language detection (sub-millisecond)
        detected_language, language_confidence = self.language_detector.detect_language(query)
        
        # Initialize variables
        best_match = None
        
        # PRIMARY FAST PATH: Use knowledge base for accurate responses
        # Knowledge base uses simple keyword matching - very fast
        if self.knowledge_base:
            try:
                kb_result = self.knowledge_base.answer_query(query, detected_language)
                base_response = kb_result['response']
                response_type = kb_result['response_type']
                confidence = kb_result['confidence']
                training_improved = True
                
                # Only log in debug mode to save time
                if os.environ.get('CHATBOT_DEBUG'):
                    print(f"📚 KB: Intent={kb_result['intent']}, Conf={confidence:.2f}, Boat={kb_result.get('boat_detected')}")
            except Exception as e:
                print(f"⚠️ Knowledge base error: {e}")
                # Fallback to simpler method (skip heavy NLP)
                base_response = self._generate_fallback_response(query, detected_language, website_content)
                response_type = 'fallback'
                confidence = 0.3
                training_improved = False
        else:
            # Fallback: Simple response generation (no heavy NLP)
            base_response = self._generate_fallback_response(query, detected_language, website_content)
            response_type = 'fallback'
            confidence = 0.3
            training_improved = False
        
        # FAST PATH: Skip token prediction by default (it's very slow on CPU)
        final_response = base_response
        token_prediction_used = False
        
        # Only use token prediction if explicitly requested AND we have context
        if use_token_prediction and conversation_history and self.token_predictor is not None:
            try:
                # Use token predictor to enhance response based on conversation context
                predicted_tokens = self.token_predictor.predict_response_with_context(
                    conversation_history=conversation_history,
                    query=query,
                    max_tokens=50,
                    temperature=0.7
                )
                
                # Combine base response with predicted tokens
                # Use prediction to refine/extend the response
                if predicted_tokens and len(predicted_tokens.strip()) > 10:
                    # If prediction is substantial, use it to enhance the response
                    # Otherwise, keep the base response
                    if len(predicted_tokens) > len(base_response) * 0.3:
                        # Prediction adds significant content
                        final_response = self._merge_response_with_prediction(
                            base_response,
                            predicted_tokens,
                            conversation_history
                        )
                        token_prediction_used = True
                        confidence = min(confidence + 0.1, 0.95)  # Boost confidence
            except Exception as e:
                print(f"⚠️ Token prediction failed: {e}")
        
        # Translate boat names if available
        try:
            from .boat_translations import translate_boat_names
            final_response = translate_boat_names(final_response, detected_language)
        except ImportError:
            try:
                from backend.chatbot.core.boat_translations import translate_boat_names
                final_response = translate_boat_names(final_response, detected_language)
            except ImportError:
                pass
        
        # Prepare result
        result = {
            'response': final_response,
            'detected_language': detected_language,
            'language_confidence': language_confidence,
            'response_type': response_type,
            'confidence': confidence,
            'processing_time': time.time() - start_time,
            'training_improved': training_improved,
            'semantic_match': self.use_advanced_nlp,
            'token_prediction_used': token_prediction_used,
            'context_aware': len(conversation_history) > 0
        }
        
        if best_match:
            result['original_response'] = best_match['response_data'].get('original', '')
        
        if not training_improved:
            result['used_website_content'] = website_content is not None and len(website_content) > 0
        
        # Add assistant response to context
        if session_id:
            context = self.context_manager.get_context(session_id)
            if context:
                context.add_message('assistant', final_response, {
                    'confidence': confidence,
                    'response_type': response_type
                })
        
        # Record interaction
        self.learning_system.record_interaction(
            query,
            final_response,
            training_improved,
            result['processing_time']
        )
        
        return result
    
    def _merge_response_with_prediction(
        self,
        base_response: str,
        predicted_tokens: str,
        conversation_history: List[Dict[str, str]]
    ) -> str:
        """
        Intelligently merge base response with token prediction
        
        Args:
            base_response: Base response from matching/template
            predicted_tokens: Predicted token continuation
            conversation_history: Conversation context
            
        Returns:
            Merged response
        """
        # Simple merging strategy: append predicted tokens if they add value
        # More sophisticated: check if prediction continues naturally
        
        # Check if prediction is a natural continuation
        base_lower = base_response.lower().strip()
        predicted_lower = predicted_tokens.lower().strip()
        
        # If prediction doesn't overlap significantly with base, append it
        words_base = set(base_lower.split())
        words_predicted = set(predicted_lower.split())
        overlap = len(words_base & words_predicted) / max(len(words_predicted), 1)
        
        if overlap < 0.3:  # Low overlap, prediction adds new information
            # Append prediction as continuation
            if base_response.endswith(('.', '!', '?')):
                return f"{base_response} {predicted_tokens}"
            else:
                return f"{base_response}. {predicted_tokens}"
        else:
            # High overlap, prediction might be redundant or refining
            # Use prediction if it's more detailed
            if len(predicted_tokens) > len(base_response) * 1.2:
                return predicted_tokens
            else:
                return base_response
    
    def _find_best_match_with_context(
        self,
        query: str,
        language: str,
        conversation_history: List[Dict[str, str]]
    ) -> Optional[Dict[str, Any]]:
        """
        Find best matching response considering conversation context
        
        Args:
            query: Current query
            language: Detected language
            conversation_history: Previous conversation messages
            
        Returns:
            Best match dictionary or None
        """
        # First try without context (original method)
        best_match = self._find_best_match(query, language)
        
        # If we have context, refine the match
        if conversation_history and best_match:
            # Consider recent conversation to refine the response
            recent_context = self._extract_recent_context_keywords(conversation_history[-3:])
            
            # Boost confidence if context supports the match
            if recent_context:
                # Check if context keywords align with the match
                response_content = best_match['response_data'].get('corrected', '').lower()
                context_alignment = sum(
                    1 for keyword in recent_context
                    if keyword in response_content
                )
                
                if context_alignment > 0:
                    # Boost similarity score
                    best_match['similarity'] = min(
                        best_match.get('similarity', 0.5) + 0.1 * context_alignment,
                        0.95
                    )
        
        return best_match
    
    def _extract_recent_context_keywords(self, recent_messages: List[Dict[str, str]]) -> List[str]:
        """Extract keywords from recent conversation messages"""
        keywords = []
        for msg in recent_messages:
            content = msg.get('content', '').lower()
            # Extract important words (nouns, key terms)
            words = content.split()
            # Filter out common words
            stop_words = {'de', 'het', 'een', 'een', 'is', 'zijn', 'was', 'waren', 'the', 'a', 'an', 'is', 'are', 'was', 'were'}
            keywords.extend([w for w in words if len(w) > 3 and w not in stop_words])
        return keywords[:10]  # Return top 10 keywords
    
    def _find_best_match(self, query: str, language: str) -> Optional[Dict[str, Any]]:
        """Find the best matching response using semantic similarity"""
        query_lower = query.lower()
        
        # Try exact match first
        if query_lower in self.improved_responses:
            return {
                'question': query_lower,
                'response_data': self.improved_responses[query_lower],
                'similarity': 1.0
            }
        
        # Use pre-computed embeddings if available
        if self.use_advanced_nlp and hasattr(self, 'question_embeddings'):
            best_match = None
            best_similarity = 0.0
            
            query_embedding = self.similarity_matcher.get_embedding(query)
            
            for question, question_embedding in self.question_embeddings.items():
                # Calculate similarity
                import numpy as np
                emb1 = np.array(query_embedding)
                emb2 = np.array(question_embedding)
                similarity = np.dot(emb1, emb2) / (
                    np.linalg.norm(emb1) * np.linalg.norm(emb2)
                )
                
                # Boost similarity for same language
                response_data = self.improved_responses[question]
                if response_data.get('language', 'nl') == language:
                    similarity *= 1.2
                
                if similarity > best_similarity:
                    best_similarity = similarity
                    best_match = {
                        'question': question,
                        'response_data': response_data,
                        'similarity': min(similarity, 1.0)
                    }
            
            return best_match if best_similarity > 0.2 else None
        
        # Fallback to simple similarity matching
        best_match = None
        best_similarity = 0.0
        
        for question, response_data in self.improved_responses.items():
            similarity = self.similarity_matcher.calculate_similarity(query, question)
            
            # Boost similarity for same language
            if response_data.get('language', 'nl') == language:
                similarity *= 1.2
            
            if similarity > best_similarity:
                best_similarity = similarity
                best_match = {
                    'question': question,
                    'response_data': response_data,
                    'similarity': min(similarity, 1.0)
                }
        
        return best_match if best_similarity > 0.2 else None
    
    def _generate_fallback_response(self, query: str, language: str, website_content: str = None) -> str:
        """Generate fallback response when no match found, using website content if available"""
        # Try to find relevant content from website
        if website_content:
            relevant_content = self._extract_relevant_content(query, website_content, language)
            if relevant_content:
                return relevant_content
        
        # Fallback to predefined responses
        fallback_responses = {
            'nl': {
                'greeting': 'Hallo! Welkom bij Nijenhuis Botenverhuur. Ik help u graag met al uw vragen over botenverhuur in het prachtige Weerribben-Wieden natuurgebied. Wat zou u graag willen weten?',
                'pricing': 'Onze dagprijzen: Zeilboot €70-85, Kano €25, Kajak €25, SUP board €35, Electrosloep 8 pers. €175, Electrosloep 10 pers. €200, Tender 570 €200, Tender 720 €230. Voor meerdere dagen: vermenigvuldig de dagprijs. Bel 0522 281 528 voor beschikbaarheid.',
                'booking': 'U kunt eenvoudig reserveren door ons te bellen op 0522 281 528. We raden aan om vooral in het hoogseizoen (juli en augustus) ruim van tevoren te reserveren. Betaling kan contant of per pin.',
                'opening_hours': 'We zijn dagelijks geopend van 09:00 tot 18:00 uur van 1 april tot 1 november. Buiten het seizoen zijn we op afspraak bereikbaar. Onze locatie is aan de Belterwijde 1, 8355 AA Giethoorn.',
                'contact': 'U kunt ons bereiken op telefoonnummer 0522 281 528 of bezoek onze locatie aan de Belterwijde 1, 8355 AA Giethoorn. We helpen u graag verder!',
                'boats': 'We hebben een uitgebreide vloot: Tender 720 (12 personen), Tender 570 (8 personen), Electrosloep 10 en 8, zeilboten, kano\'s, kajaks en SUP boards. Alle boten zijn perfect voor het verkennen van het Weerribben-Wieden gebied.',
                'location': 'We bevinden ons aan de Belterwijde 1, 8355 AA Giethoorn, in het hart van het prachtige Weerribben-Wieden natuurgebied. Perfect gelegen voor boottochten door de unieke waterwegen en natuur.',
                'fallback': 'Ik help u graag verder! Voor specifieke vragen over botenverhuur, prijzen, reserveringen of onze locatie kunt u ons bellen op 0522 281 528. We zijn er om u de beste bootervaring te bieden in het Weerribben-Wieden gebied.'
            },
            'en': {
                'greeting': 'Hello! Welcome to Nijenhuis Boat Rental. I\'d be happy to help you with any questions about boat rental in the beautiful Weerribben-Wieden nature reserve. What would you like to know?',
                'pricing': 'Our daily rates: Sailboat €70-85, Canoe €25, Kayak €25, SUP board €35, Electric sloop 8 pers. €175, Electric sloop 10 pers. €200, Tender 570 €200, Tender 720 €230. For multiple days: multiply the daily rate. Call 0522 281 528 for availability.',
                'booking': 'You can easily make a reservation by calling us at 0522 281 528. We recommend booking well in advance, especially during high season (July and August). Payment can be made in cash or by card.',
                'opening_hours': 'We are open daily from 09:00 to 18:00 from April 1st to November 1st. Outside the season, we are available by appointment. Our location is at Belterwijde 1, 8355 AA Giethoorn.',
                'contact': 'You can reach us at phone number 0522 281 528 or visit our location at Belterwijde 1, 8355 AA Giethoorn. We\'re happy to help you!',
                'boats': 'We have an extensive fleet: Tender 720 (12 people), Tender 570 (8 people), Electrosloep 10 and 8, sailboats, canoes, kayaks and SUP boards. All boats are perfect for exploring the Weerribben-Wieden area.',
                'location': 'We are located at Belterwijde 1, 8355 AA Giethoorn, in the heart of the beautiful Weerribben-Wieden nature reserve. Perfectly located for boat trips through unique waterways and nature.',
                'fallback': 'I\'d be happy to help you further! For specific questions about boat rental, prices, reservations or our location, please call us at 0522 281 528. We\'re here to provide you with the best boat experience in the Weerribben-Wieden area.'
            },
            'de': {
                'greeting': 'Hallo! Willkommen bei Nijenhuis Bootsverleih. Ich helfe Ihnen gerne bei allen Fragen zum Bootsverleih im wunderschönen Weerribben-Wieden Naturgebiet. Was möchten Sie gerne wissen?',
                'pricing': 'Unsere Tagespreise: Segelboot €70-85, Kanu €25, Kajak €25, SUP-Board €35, Elektrosloep 8 Pers. €175, Elektrosloep 10 Pers. €200, Tender 570 €200, Tender 720 €230. Für mehrere Tage: Tagespreis multiplizieren. Rufen Sie 0522 281 528 für Verfügbarkeit an.',
                'booking': 'Sie können einfach eine Reservierung vornehmen, indem Sie uns unter 0522 281 528 anrufen. Wir empfehlen, besonders in der Hauptsaison (Juli und August) rechtzeitig zu buchen. Zahlung kann bar oder per Karte erfolgen.',
                'opening_hours': 'Wir sind täglich von 09:00 bis 18:00 Uhr vom 1. April bis 1. November geöffnet. Außerhalb der Saison sind wir nach Vereinbarung erreichbar. Unser Standort ist an der Belterwijde 1, 8355 AA Giethoorn.',
                'contact': 'Sie können uns unter der Telefonnummer 0522 281 528 erreichen oder unseren Standort an der Belterwijde 1, 8355 AA Giethoorn besuchen. Wir helfen Ihnen gerne weiter!',
                'boats': 'Wir haben eine umfangreiche Flotte: Tender 720 (12 Personen), Tender 570 (8 Personen), Electrosloep 10 und 8, Segelboote, Kanus, Kajaks und SUP-Boards. Alle Boote sind perfekt zum Erkunden des Weerribben-Wieden Gebiets.',
                'location': 'Wir befinden uns an der Belterwijde 1, 8355 AA Giethoorn, im Herzen des wunderschönen Weerribben-Wieden Naturgebiets. Perfekt gelegen für Bootsfahrten durch einzigartige Wasserwege und Natur.',
                'fallback': 'Ich helfe Ihnen gerne weiter! Für spezifische Fragen zum Bootsverleih, Preisen, Reservierungen oder unserem Standort rufen Sie uns bitte unter 0522 281 528 an. Wir sind da, um Ihnen die beste Bootserfahrung im Weerribben-Wieden Gebiet zu bieten.'
            }
        }
        
        # Classify query
        response_type = self._classify_query(query)
        
        # Get response in detected language
        language_responses = fallback_responses.get(language, fallback_responses['nl'])
        return language_responses.get(response_type, language_responses['fallback'])
    
    def _extract_relevant_content(self, query: str, website_content: str, language: str) -> str:
        """Extract relevant content from website content based on query"""
        if not website_content:
            return None
        
        query_lower = query.lower()
        query_words = set(query_lower.split())
        
        # Extract specific information patterns
        # Check for pricing queries
        if any(word in query_lower for word in ['prijs', 'kost', 'cost', 'price', 'preis', 'hoeveel', 'how much', 'wie viel']):
            # Extract number of days from query (e.g., "3 dagen", "2 days")
            days_match = re.search(r'(\d+)\s*(dag|dagen|day|days|tage|tag)', query_lower)
            num_days = int(days_match.group(1)) if days_match else 1
            
            # Boat type keywords for matching query to boat types
            boat_keywords = {
                'zeilboot': ['zeilboot', 'sailboat', 'zeil', 'segelboot'],
                'zeilpunter': ['zeilpunter', 'punter'],
                'tender 720': ['tender 720', 'tender720', 'grote tender'],
                'tender 570': ['tender 570', 'tender570'],
                'electrosloep 10': ['electrosloep 10', 'sloep 10', 'elektrische sloep 10'],
                'electrosloep 8': ['electrosloep 8', 'sloep 8', 'elektrische sloep 8'],
                'electrosloep 5': ['electrosloep 5', 'sloep 5', 'elektrische sloep 5'],
                'kano': ['kano', 'canoe', 'canadese'],
                'kajak': ['kajak', 'kayak'],
                'sup': ['sup', 'stand up paddle', 'supboard', 'paddleboard']
            }
            
            # Find which boat type is mentioned in the query
            query_boat_type = None
            for boat_type, keywords in boat_keywords.items():
                for keyword in keywords:
                    if keyword in query_lower:
                        query_boat_type = boat_type
                        break
                if query_boat_type:
                    break
            
            # Price patterns with boat type identifier
            price_patterns = [
                (r'(zeilboot|sailboat).*?€(\d+)', 'Zeilboot', 'zeilboot'),
                (r'(zeilpunter).*?€(\d+)', 'Zeilpunter', 'zeilpunter'),
                (r'(classic tender 720|tender 720).*?€(\d+)', 'Tender 720', 'tender 720'),
                (r'(classic tender 570|tender 570).*?€(\d+)', 'Tender 570', 'tender 570'),
                (r'(electrosloep.*?10|electrosloep voor 10).*?€(\d+)', 'Electrosloep voor 10 personen', 'electrosloep 10'),
                (r'(electrosloep.*?8|electrosloep voor 8).*?€(\d+)', 'Electrosloep voor 8 personen', 'electrosloep 8'),
                (r'(electrosloep.*?5|electrosloep voor 5).*?€(\d+)', 'Electrosloep voor 5 personen', 'electrosloep 5'),
                (r'(canadese kano|canoe|kano).*?€(\d+)', 'Kano', 'kano'),
                (r'(kajak|kayak).*?€(\d+)', 'Kajak', 'kajak'),
                (r'(sup|sup board|supboard).*?€(\d+)', 'SUP board', 'sup'),
            ]
            
            # Find all price matches and prioritize exact boat type match
            all_matches = []
            for pattern, boat_name, boat_key in price_patterns:
                for match in re.finditer(pattern, website_content, re.IGNORECASE | re.DOTALL):
                    price = int(match.group(2))
                    # Priority: 2 for exact match, 1 for generic
                    priority = 2 if query_boat_type and boat_key == query_boat_type else 1
                    all_matches.append((priority, boat_name, price, boat_key))
            
            if all_matches:
                # Sort by priority (highest first), then alphabetically
                all_matches.sort(key=lambda x: (-x[0], x[1]))
                best = all_matches[0]
                boat_name, price = best[1], best[2]
                
                # Calculate total for multiple days
                total_price = price * num_days
                
                if language == 'nl':
                    if num_days > 1:
                        return f"De {boat_name} kost €{price} per dag. Voor {num_days} dagen is dat €{total_price} in totaal."
                    else:
                        return f"De {boat_name} kost €{price} per dag."
                elif language == 'en':
                    if num_days > 1:
                        return f"The {boat_name} costs €{price} per day. For {num_days} days that's €{total_price} in total."
                    else:
                        return f"The {boat_name} costs €{price} per day."
                elif language == 'de':
                    if num_days > 1:
                        return f"Die {boat_name} kostet €{price} pro Tag. Für {num_days} Tage sind das €{total_price} insgesamt."
                    else:
                        return f"Die {boat_name} kostet €{price} pro Tag."
        
        # Check for opening hours queries
        if any(word in query_lower for word in ['opening', 'open', 'uren', 'hours', 'öffnung', 'öffnungszeiten', 'geopend']):
            hours_patterns = [
                r'openingstijden[:\s]+([^\n]+)',
                r'geopend[:\s]+([^\n]+)',
                r'open[:\s]+([^\n]+)',
                r'09:00.*?18:00',
                r'9:00.*?18:00',
            ]
            for pattern in hours_patterns:
                match = re.search(pattern, website_content, re.IGNORECASE)
                if match:
                    hours_info = match.group(1) if match.lastindex else match.group(0)
                    hours_info = re.sub(r'\s+', ' ', hours_info).strip()
                    if len(hours_info) > 10:
                        if language == 'nl':
                            return f"Onze openingstijden zijn: {hours_info}"
                        elif language == 'en':
                            return f"Our opening hours are: {hours_info}"
                        elif language == 'de':
                            return f"Unsere Öffnungszeiten sind: {hours_info}"
        
        # Check for contact queries
        if any(word in query_lower for word in ['contact', 'telefoon', 'phone', 'telefon', 'bellen', 'call', 'bereiken']):
            phone_pattern = r'(0\d{3}[-\s]?\d{3}[-\s]?\d{3}|0522[-\s]?281[-\s]?528)'
            phone_match = re.search(phone_pattern, website_content)
            if phone_match:
                phone = phone_match.group(1)
                if language == 'nl':
                    return f"U kunt ons bereiken op telefoonnummer {phone}."
                elif language == 'en':
                    return f"You can reach us at phone number {phone}."
                elif language == 'de':
                    return f"Sie können uns unter der Telefonnummer {phone} erreichen."
        
        # Split content into meaningful chunks (paragraphs, lines)
        content_chunks = []
        current_chunk = []
        
        for line in website_content.split('\n'):
            line = line.strip()
            if not line or len(line) < 10:
                if current_chunk:
                    content_chunks.append(' '.join(current_chunk))
                    current_chunk = []
            else:
                current_chunk.append(line)
        
        if current_chunk:
            content_chunks.append(' '.join(current_chunk))
        
        # Score each chunk based on keyword matches
        scored_chunks = []
        for chunk in content_chunks:
            if len(chunk) < 15:
                continue
            
            chunk_lower = chunk.lower()
            chunk_words = set(chunk_lower.split())
            
            # Calculate relevance score
            common_words = query_words.intersection(chunk_words)
            score = len(common_words)
            
            # Boost score for important keywords
            important_keywords = {
                'nl': ['prijs', 'kost', 'reserveren', 'boeken', 'opening', 'contact', 'telefoon', 'adres', 'boot', 'tender', 'electrosloep', 'zeilboot', 'kano', 'kajak', 'sup', 'capaciteit', 'personen'],
                'en': ['price', 'cost', 'book', 'reserve', 'opening', 'contact', 'phone', 'address', 'boat', 'tender', 'electrosloep', 'sailboat', 'canoe', 'kayak', 'sup', 'capacity', 'people'],
                'de': ['preis', 'kosten', 'buchen', 'reservieren', 'öffnung', 'kontakt', 'telefon', 'adresse', 'boot', 'tender', 'electrosloep', 'segeln', 'kanu', 'kajak', 'kapazität', 'personen']
            }
            
            lang_keywords = important_keywords.get(language, important_keywords['nl'])
            for keyword in lang_keywords:
                if keyword in chunk_lower:
                    score += 3
            
            # Boost score if query words appear together
            query_phrase = ' '.join(sorted(query_words))
            if query_phrase in chunk_lower or any(len(word) > 4 and word in chunk_lower for word in query_words if len(word) > 4):
                score += 5
            
            if score > 0:
                scored_chunks.append((score, chunk))
        
        # Sort by score and get top relevant chunks
        scored_chunks.sort(key=lambda x: x[0], reverse=True)
        
        if scored_chunks:
            # Get top 1-2 most relevant chunks
            top_chunks = [chunk for _, chunk in scored_chunks[:2]]
            
            # Clean and format response
            response = ' '.join(top_chunks)
            
            # Remove duplicates and clean up
            sentences = response.split('.')
            unique_sentences = []
            seen = set()
            for sentence in sentences:
                sentence = sentence.strip()
                if sentence and len(sentence) > 15:
                    sentence_key = sentence[:50].lower()
                    if sentence_key not in seen:
                        seen.add(sentence_key)
                        unique_sentences.append(sentence)
            
            if unique_sentences:
                # Take top 1-2 sentences for cleaner responses
                response = '. '.join(unique_sentences[:2])
                
                # Clean up response - remove section markers and headers
                response = re.sub(r'===.*?===', '', response)
                response = re.sub(r'(TITLE|DESCRIPTION|H[1-6]):\s*', '', response)
                response = re.sub(r'\s+', ' ', response).strip()
                
                if not response.endswith('.') and len(response) > 20:
                    response += '.'
                
                # Only add prefix if response is substantial
                if len(response) > 30:
                    if language == 'nl':
                        return f"Volgens onze website: {response}"
                    elif language == 'en':
                        return f"According to our website: {response}"
                    elif language == 'de':
                        return f"Laut unserer Website: {response}"
                    else:
                        return response
                else:
                    return response
        
        return None
    
    def _classify_query(self, query: str) -> str:
        """Classify query to determine response type"""
        query_lower = query.lower()
        
        if any(word in query_lower for word in ['prijs', 'kost', 'cost', 'price', 'preis', 'kosten', 'pricing', 'hoeveel', 'how much', 'wie viel']):
            return 'pricing'
        elif any(word in query_lower for word in ['reserveren', 'boeken', 'book', 'reserve', 'buchen', 'reservieren', 'booking', 'reservation']):
            return 'booking'
        elif any(word in query_lower for word in ['open', 'opening', 'uren', 'hours', 'öffnungszeiten', 'geopend', 'openingstijden']):
            return 'opening_hours'
        elif any(word in query_lower for word in ['contact', 'bellen', 'call', 'telefoon', 'phone', 'anrufen', 'bereiken']):
            return 'contact'
        elif any(word in query_lower for word in ['boot', 'boat', 'tender', 'electrosloep', 'zeilboot', 'kano', 'kajak', 'sup', 'fleet', 'flotte']):
            return 'boats'
        elif any(word in query_lower for word in ['locatie', 'location', 'waar', 'where', 'wo', 'adres', 'address', 'finden', 'find']):
            return 'location'
        elif any(word in query_lower for word in ['hallo', 'hello', 'hi', 'hey', 'goedemorgen', 'good morning', 'guten tag', 'welcome', 'welkom']):
            return 'greeting'
        else:
            return 'fallback'
    
    def evaluate_model(self, output_file: str = None) -> Dict[str, Any]:
        """
        Evaluate model performance with train/validation/test split
        
        Args:
            output_file: Optional path to save evaluation report
            
        Returns:
            Evaluation report dictionary
        """
        # Create data splits
        splits = self.evaluator.split_data(train_ratio=0.7, val_ratio=0.15, test_ratio=0.15)
        
        # Create evaluation functions
        def intent_classifier(question: str) -> str:
            return self._classify_query(question)
        
        def response_matcher(question: str) -> Tuple[str, float]:
            match = self._find_best_match(question, 'nl')
            if match:
                return match['response_data']['corrected'], match['similarity']
            return '', 0.0
        
        # Generate report
        report = self.evaluator.generate_evaluation_report(
            splits,
            language_detector=self.language_detector,
            intent_classifier=intent_classifier,
            response_matcher=response_matcher
        )
        
        # Save report if requested
        if output_file:
            self.evaluator.save_evaluation_report(report, output_file)
        
        return report
    
    def get_stats(self) -> Dict[str, Any]:
        """Get chatbot statistics"""
        return {
            'total_responses': len(self.improved_responses),
            'languages_supported': ['nl', 'en', 'de'],
            'advanced_features': {
                'semantic_matching': self.use_advanced_nlp,
                'language_detection': self.use_advanced_nlp,
                'neural_network': self._neural_network is not None,
                'embeddings_cache': len(getattr(self.similarity_matcher, '_embedding_cache', {}))
            },
            'nlp_capabilities': {
                'transformers': (TRANSFORMERS_AVAILABLE or False) and self.use_advanced_nlp,
                'sklearn': SKLEARN_AVAILABLE or False
            },
            'learning_stats': self.learning_system.get_stats(),
            'neural_network_info': {
                'input_size': self._nn_config['input_size'],
                'hidden_sizes': self._nn_config['hidden_sizes'],
                'output_size': self._nn_config['output_size'],
                'initialized': self._neural_network is not None,
                'trained': self._neural_network is not None and len(self.improved_responses) > 0
            }
        }


# Backward compatibility aliases
EnhancedChatbot = Chatbot  # For API server compatibility

