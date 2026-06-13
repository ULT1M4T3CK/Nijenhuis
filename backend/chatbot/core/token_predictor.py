#!/usr/bin/env python3
"""
Token Prediction Model
High-precision token prediction using conversation context
"""

import os
import json
import re
from typing import List, Dict, Optional, Tuple, Any
import numpy as np

# Try to import advanced libraries
try:
    from transformers import AutoTokenizer, AutoModelForCausalLM, pipeline
    from transformers import GPT2LMHeadModel, GPT2Tokenizer
    TRANSFORMERS_AVAILABLE = True
except ImportError:
    TRANSFORMERS_AVAILABLE = False

try:
    from sentence_transformers import SentenceTransformer
    SENTENCE_TRANSFORMERS_AVAILABLE = True
except ImportError:
    SENTENCE_TRANSFORMERS_AVAILABLE = False


class TokenPredictor:
    """High-precision token prediction model with full conversation context awareness"""
    
    def __init__(self, model_name: Optional[str] = None, use_transformer: bool = True):
        """
        Initialize token predictor
        
        Args:
            model_name: Name of transformer model to use (if available)
            use_transformer: Whether to use transformer models if available
        """
        self.use_transformer = use_transformer and TRANSFORMERS_AVAILABLE
        self.model = None
        self.tokenizer = None
        self.generator = None
        self.embedder = None
        
        # Vocabulary and n-gram statistics for fallback
        self.vocabulary = {}
        self.ngram_stats = {}  # For n-gram based prediction
        self.context_weights = {}  # Context-aware word probabilities
        
        if self.use_transformer:
            self._initialize_transformer_model(model_name)
        else:
            print("⚠️ Transformers not available, using statistical fallback")
            self._initialize_statistical_model()
    
    def _initialize_transformer_model(self, model_name: Optional[str] = None):
        """Initialize transformer-based model"""
        try:
            # Use a lightweight model that can run on CPU
            if model_name is None:
                # Try to use a Dutch-friendly model, fallback to GPT-2
                try:
                    model_name = "GroNLP/gpt2-small-dutch"
                    print(f"🔄 Loading Dutch GPT-2 model: {model_name}")
                    self.tokenizer = AutoTokenizer.from_pretrained(model_name)
                    self.model = AutoModelForCausalLM.from_pretrained(model_name)
                except Exception:
                    # Fallback to English GPT-2
                    model_name = "gpt2"
                    print(f"🔄 Loading GPT-2 model: {model_name}")
                    self.tokenizer = GPT2Tokenizer.from_pretrained(model_name)
                    self.model = GPT2LMHeadModel.from_pretrained(model_name)
            
            # Set padding token if not present
            if self.tokenizer.pad_token is None:
                self.tokenizer.pad_token = self.tokenizer.eos_token
            
            # Create text generation pipeline
            self.generator = pipeline(
                "text-generation",
                model=self.model,
                tokenizer=self.tokenizer,
                device=-1  # Use CPU
            )
            
            print("✅ Transformer model initialized successfully")
            
        except Exception as e:
            print(f"⚠️ Could not initialize transformer model: {e}")
            print("   Falling back to statistical model")
            self.use_transformer = False
            self._initialize_statistical_model()
    
    def _initialize_statistical_model(self):
        """Initialize statistical n-gram based model"""
        print("📊 Initializing statistical token prediction model...")
        # This will be populated with training data
        self.vocabulary = {}
        self.ngram_stats = {
            'unigrams': {},
            'bigrams': {},
            'trigrams': {}
        }
        print("✅ Statistical model initialized")
    
    def update_with_training_data(self, conversations: List[List[Dict[str, str]]]):
        """
        Update model with training conversations
        
        Args:
            conversations: List of conversations, each conversation is a list of messages
        """
        if self.use_transformer:
            # For transformer models, we can fine-tune or just use them as-is
            # Fine-tuning would require more setup, so we'll use them pre-trained
            print("✅ Transformer model ready (pre-trained)")
            return
        
        # Update statistical model
        print(f"📚 Updating statistical model with {len(conversations)} conversations...")
        
        for conversation in conversations:
            # Combine conversation into context
            context = self._format_conversation_context(conversation)
            words = self._tokenize(context)
            
            # Update n-gram statistics
            for i in range(len(words)):
                word = words[i].lower()
                self.vocabulary[word] = self.vocabulary.get(word, 0) + 1
                
                # Bigrams
                if i > 0:
                    bigram = (words[i-1].lower(), word)
                    self.ngram_stats['bigrams'][bigram] = self.ngram_stats['bigrams'].get(bigram, 0) + 1
                
                # Trigrams
                if i > 1:
                    trigram = (words[i-2].lower(), words[i-1].lower(), word)
                    self.ngram_stats['trigrams'][trigram] = self.ngram_stats['trigrams'].get(trigram, 0) + 1
        
        print(f"✅ Updated vocabulary with {len(self.vocabulary)} unique words")
    
    def predict_next_tokens(
        self,
        conversation_history: List[Dict[str, str]],
        max_tokens: int = 50,
        temperature: float = 0.7,
        top_p: float = 0.9
    ) -> str:
        """
        Predict next tokens based on full conversation context
        
        Args:
            conversation_history: Full conversation history with 'role' and 'content'
            max_tokens: Maximum number of tokens to generate
            temperature: Sampling temperature (lower = more deterministic)
            top_p: Nucleus sampling parameter
            
        Returns:
            Generated text continuation
        """
        # Format conversation context
        context = self._format_conversation_context(conversation_history)
        
        if self.use_transformer and self.generator:
            return self._predict_with_transformer(context, max_tokens, temperature, top_p)
        else:
            return self._predict_with_statistics(context, max_tokens)
    
    def _predict_with_transformer(
        self,
        context: str,
        max_tokens: int,
        temperature: float,
        top_p: float
    ) -> str:
        """Predict using transformer model"""
        try:
            # Limit context length for transformer (most models have 512-1024 token limit)
            max_context_length = 400  # Leave room for generation
            if len(context) > max_context_length:
                context = context[-max_context_length:]
            
            # Generate continuation
            result = self.generator(
                context,
                max_length=len(context.split()) + max_tokens,
                max_new_tokens=max_tokens,
                temperature=temperature,
                top_p=top_p,
                do_sample=True,
                num_return_sequences=1,
                pad_token_id=self.tokenizer.eos_token_id,
                eos_token_id=self.tokenizer.eos_token_id
            )
            
            generated_text = result[0]['generated_text']
            
            # Remove the context part, return only the generated continuation
            if generated_text.startswith(context):
                return generated_text[len(context):].strip()
            else:
                return generated_text.strip()
                
        except Exception as e:
            print(f"⚠️ Transformer prediction failed: {e}")
            return self._predict_with_statistics(context, max_tokens)
    
    def _predict_with_statistics(self, context: str, max_tokens: int) -> str:
        """Predict using statistical n-gram model"""
        words = self._tokenize(context)
        if not words:
            return ""
        
        generated_words = []
        
        # Use last 2-3 words as context for prediction
        context_window = words[-3:] if len(words) >= 3 else words
        
        for _ in range(max_tokens):
            # Try trigram first
            if len(context_window) >= 2:
                trigram_key = (context_window[-2].lower(), context_window[-1].lower())
                candidates = [
                    (w, count) for (w1, w2, w), count in self.ngram_stats['trigrams'].items()
                    if (w1, w2) == trigram_key
                ]
                if candidates:
                    # Select most likely next word
                    next_word = max(candidates, key=lambda x: x[1])[0]
                    generated_words.append(next_word)
                    context_window = context_window[-2:] + [next_word]
                    continue
            
            # Try bigram
            if len(context_window) >= 1:
                bigram_key = (context_window[-1].lower(),)
                candidates = [
                    (w, count) for (w1, w), count in self.ngram_stats['bigrams'].items()
                    if w1 == bigram_key[0]
                ]
                if candidates:
                    next_word = max(candidates, key=lambda x: x[1])[0]
                    generated_words.append(next_word)
                    context_window = context_window[-1:] + [next_word]
                    continue
            
            # Fallback to unigram (most common words)
            if self.vocabulary:
                next_word = max(self.vocabulary.items(), key=lambda x: x[1])[0]
                generated_words.append(next_word)
                context_window = context_window[-1:] + [next_word]
            else:
                break
        
        return " ".join(generated_words)
    
    def _format_conversation_context(self, conversation_history: List[Dict[str, str]]) -> str:
        """Format conversation history into a single context string"""
        context_parts = []
        
        for msg in conversation_history:
            role = msg.get('role', 'user')
            content = msg.get('content', '')
            
            if role == 'user':
                context_parts.append(f"User: {content}")
            elif role == 'assistant':
                context_parts.append(f"Assistant: {content}")
        
        return "\n".join(context_parts)
    
    def _tokenize(self, text: str) -> List[str]:
        """Simple tokenization (can be improved)"""
        # Remove punctuation and split
        text = re.sub(r'[^\w\s]', ' ', text)
        words = text.split()
        return [w.lower() for w in words if w.strip()]
    
    def get_context_embedding(self, conversation_history: List[Dict[str, str]]) -> np.ndarray:
        """
        Get embedding representation of conversation context
        
        Args:
            conversation_history: Conversation history
            
        Returns:
            Context embedding vector
        """
        context = self._format_conversation_context(conversation_history)
        
        if SENTENCE_TRANSFORMERS_AVAILABLE and self.embedder is None:
            try:
                # Use a multilingual model
                self.embedder = SentenceTransformer('paraphrase-multilingual-MiniLM-L12-v2')
            except Exception:
                pass
        
        if self.embedder:
            return self.embedder.encode(context)
        else:
            # Fallback: simple bag-of-words representation
            words = self._tokenize(context)
            embedding = np.zeros(len(self.vocabulary) if self.vocabulary else 100)
            
            for word in words:
                if word in self.vocabulary:
                    idx = list(self.vocabulary.keys()).index(word)
                    if idx < len(embedding):
                        embedding[idx] += 1
            
            # Normalize
            norm = np.linalg.norm(embedding)
            if norm > 0:
                embedding = embedding / norm
            
            return embedding
    
    def predict_response_with_context(
        self,
        conversation_history: List[Dict[str, str]],
        query: str,
        max_tokens: int = 100,
        temperature: float = 0.7
    ) -> str:
        """
        Predict a full response considering conversation context
        
        Args:
            conversation_history: Previous conversation messages
            query: Current user query
            max_tokens: Maximum tokens to generate
            temperature: Sampling temperature
            
        Returns:
            Predicted response
        """
        # Add current query to history for prediction
        full_history = conversation_history + [{'role': 'user', 'content': query}]
        
        # Get assistant's previous response if exists (for continuation)
        assistant_responses = [msg for msg in conversation_history if msg.get('role') == 'assistant']
        if assistant_responses:
            # Continue from last assistant response
            last_response = assistant_responses[-1]['content']
            context = self._format_conversation_context(conversation_history)
            continuation = self.predict_next_tokens(full_history, max_tokens, temperature)
            return continuation
        else:
            # Generate new response
            context = self._format_conversation_context(full_history)
            return self.predict_next_tokens(full_history, max_tokens, temperature)

