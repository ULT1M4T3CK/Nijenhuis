import json
import os
import time
import re
from typing import Dict, Any, Optional

# Handle both relative and absolute imports
try:
    from .unsupervised_learning import UnsupervisedLearning
    from .neural_network import ChatbotNeuralNetwork
except ImportError:
    from backend.chatbot.core.unsupervised_learning import UnsupervisedLearning
    from backend.chatbot.core.neural_network import ChatbotNeuralNetwork

class SimpleLanguageDetector:
    """Simple language detection using common words"""
    
    def __init__(self):
        self.language_patterns = {
            'nl': ['de', 'het', 'een', 'en', 'van', 'in', 'op', 'voor', 'met', 'aan', 'bij', 'door', 'over', 'onder', 'tussen', 'na', 'tot', 'uit', 'zonder', 'tegen', 'langs', 'rond', 'om', 'doorheen'],
            'en': ['the', 'and', 'or', 'but', 'in', 'on', 'at', 'to', 'for', 'of', 'with', 'by'],
            'de': ['der', 'die', 'das', 'und', 'oder', 'aber', 'in', 'mit', 'für', 'von', 'zu'],
            'es': ['el', 'la', 'los', 'las', 'y', 'o', 'pero', 'en', 'con', 'por', 'para', 'de'],
            'fr': ['le', 'la', 'les', 'et', 'ou', 'mais', 'dans', 'avec', 'pour', 'de', 'du'],
            'it': ['il', 'la', 'gli', 'le', 'e', 'o', 'ma', 'in', 'con', 'per', 'di', 'da']
        }
    
    def detect_language(self, text: str) -> str:
        """Detect language based on common words"""
        text_lower = text.lower()
        scores = {}
        
        for lang, words in self.language_patterns.items():
            score = sum(1 for word in words if word in text_lower)
            scores[lang] = score
        
        if scores:
            return max(scores, key=scores.get)
        return 'nl'  # Default to Dutch for Nijenhuis

class EnhancedChatbot:
    """Enhanced chatbot that uses training data to improve responses"""
    
    def __init__(self, training_data_file: str = None):
        # Initialize language detector
        self.language_detector = SimpleLanguageDetector()
        
        # Default to packaged data path if not provided
        if training_data_file is None:
            self.training_data_file = os.path.abspath(os.path.join(os.path.dirname(__file__), '..', 'training', 'data', 'training_data.json'))
        else:
            self.training_data_file = training_data_file
        self.training_data = self.load_training_data()
        self.improved_responses = self.training_data.get("improved_responses", {})
        
        # Initialize unsupervised learning system
        self.learning_system = UnsupervisedLearning()
        
        # Auto-improve training data based on unsupervised learning
        self.training_data = self.learning_system.auto_improve_responses(self.training_data)
        self.improved_responses = self.training_data.get("improved_responses", {})
        
        # Initialize neural network for advanced pattern recognition
        self.neural_network = ChatbotNeuralNetwork(input_size=100, hidden_sizes=[64, 32], output_size=5)
        
        # Train neural network on existing training data
        self.train_neural_network()
    
    def load_training_data(self) -> Dict[str, Any]:
        """Load training data from file"""
        if os.path.exists(self.training_data_file):
            try:
                with open(self.training_data_file, 'r', encoding='utf-8') as f:
                    return json.load(f)
            except Exception as e:
                print(f"Warning: Could not load training data: {e}")
        return {"improved_responses": {}}
    
    def train_neural_network(self):
        """Train the neural network on training data"""
        if not self.improved_responses:
            print("No training data available for neural network")
            return
        
        print("🧠 Training neural network on {} examples...".format(len(self.improved_responses)))
        
        # Prepare training data
        training_samples = []
        for key, response_data in self.improved_responses.items():
            # Use the key as the question if no question field exists
            question = response_data.get('question', key).lower()
            features = self._text_to_features(question)
            # Use response type as target (simplified)
            target = self._get_response_type_index(response_data.get('response_type', 'general'))
            training_samples.append((features, target))
        
        if training_samples:
            # Convert to the format expected by train_on_chatbot_data
            chatbot_training_data = []
            for features, target in training_samples:
                # Convert target index back to response type
                response_type = self._get_response_type_from_index(target)
                # Create a simple question from features (this is a simplified approach)
                question = f"question_{target}"  # Simplified for now
                chatbot_training_data.append((question, response_type))
            
            self.neural_network.train_on_chatbot_data(chatbot_training_data, epochs=50)
            print("✅ Neural network training completed")
        else:
            print("⚠️ No valid training samples found")
    
    def _text_to_features(self, text: str) -> list:
        """Convert text to feature vector"""
        # Simple feature extraction - word presence
        features = [0.0] * 100
        
        # Common words for boat rental context
        boat_words = ['boot', 'boat', 'tender', 'electrosloep', 'zeilboot', 'kano', 'kajak', 'sup', 'prijs', 'price', 'kost', 'cost', 'reserveren', 'book', 'boeken', 'opening', 'hours', 'uren', 'contact', 'locatie', 'location']
        
        text_lower = text.lower()
        for i, word in enumerate(boat_words[:100]):
            if word in text_lower:
                features[i] = 1.0
        
        return features
    
    def _get_response_type_index(self, response_type: str) -> int:
        """Get index for response type"""
        type_mapping = {
            'pricing': 0,
            'booking': 1,
            'opening_hours': 2,
            'contact': 3,
            'general': 4
        }
        return type_mapping.get(response_type, 4)
    
    def _get_response_type_from_index(self, index: int) -> str:
        """Get response type from index"""
        type_mapping = {
            0: 'pricing',
            1: 'booking',
            2: 'opening_hours',
            3: 'contact',
            4: 'general'
        }
        return type_mapping.get(index, 'general')
    
    def process_query(self, query: str) -> Dict[str, Any]:
        """Process a user query and return enhanced response"""
        start_time = time.time()
        
        # Detect language
        detected_language = self.language_detector.detect_language(query)
        
        # Find most similar response from training data
        similar_response = self._find_similar_response(query, detected_language)
        
        if similar_response:
            # Use the corrected response from training data
            result = {
                'response': similar_response['corrected'],
                'detected_language': detected_language,
                'response_type': similar_response.get('type', 'general'),
                'confidence': similar_response.get('confidence', 0.8),
                'processing_time': time.time() - start_time,
                'website_analysis': None,
                'training_improved': True,
                'original_response': similar_response['original']
            }
            
            # Import the translation function from boat_translations
            try:
                from .boat_translations import translate_boat_names
                # Translate boat names in the response
                result['response'] = translate_boat_names(result['response'], result['detected_language'])
            except ImportError:
                try:
                    from backend.chatbot.core.boat_translations import translate_boat_names
                    result['response'] = translate_boat_names(result['response'], result['detected_language'])
                except ImportError:
                    # If import fails, return without translation
                    pass
            
            # Record interaction for unsupervised learning
            response_time = time.time() - start_time
            self.learning_system.record_interaction(query, result['response'], True, response_time)
            
            return result
        else:
            # Fallback to basic response
            fallback_response = self._generate_fallback_response(query, detected_language)
            
            result = {
                'response': fallback_response,
                'detected_language': detected_language,
                'response_type': 'fallback',
                'confidence': 0.3,
                'processing_time': time.time() - start_time,
                'website_analysis': None,
                'training_improved': False
            }
            
            # Record interaction for unsupervised learning
            response_time = time.time() - start_time
            self.learning_system.record_interaction(query, result['response'], False, response_time)
            
            return result
    
    def _find_similar_response(self, query: str, language: str) -> Optional[Dict[str, Any]]:
        """Find the most similar response from training data"""
        query_lower = query.lower()
        best_match = None
        best_score = 0
        
        # First try to find exact matches in the improved_responses keys
        if query_lower in self.improved_responses:
            return self.improved_responses[query_lower]
        
        # Then try to find similar responses
        for key, response_data in self.improved_responses.items():
            # Use the key as the question if no question field exists
            question = response_data.get('question', key).lower()
            similarity = self._calculate_similarity(query_lower, question)
            
            if similarity > best_score and similarity > 0.2:  # Lower threshold for better matching
                best_score = similarity
                best_match = response_data
        
        return best_match
    
    def _calculate_similarity(self, query: str, question: str) -> float:
        """Calculate similarity between query and question"""
        # Simple word-based similarity
        query_words = set(query.split())
        question_words = set(question.split())
        
        if not query_words or not question_words:
            return 0.0
        
        intersection = query_words.intersection(question_words)
        union = query_words.union(question_words)
        
        return len(intersection) / len(union) if union else 0.0
    
    def _generate_fallback_response(self, query: str, language: str) -> str:
        """Generate a fallback response when no training data matches"""
        # Nijenhuis-specific fallback responses
        fallback_responses = {
            'nl': {
                'greeting': 'Hallo! Welkom bij Nijenhuis Botenverhuur. Hoe kan ik u helpen met botenverhuur in het Weerribben-Wieden gebied?',
                'pricing': 'Voor actuele prijzen kunt u ons bellen op 0522 281 528 of kijk op onze website. We hebben verschillende boten beschikbaar voor alle groepsgroottes.',
                'booking': 'U kunt reserveren door ons te bellen op 0522 281 528. We raden aan om vooral in het hoogseizoen op tijd te reserveren.',
                'opening_hours': 'We zijn dagelijks geopend van 09:00 tot 18:00 uur van 1 april tot 1 november. Buiten het seizoen zijn we op afspraak bereikbaar.',
                'contact': 'U kunt ons bereiken op 0522 281 528 of bezoek onze locatie aan de Belterwijde 1, 8355 AA Giethoorn.',
                'boats': 'We hebben verschillende boten: Tender 720/570, Electrosloep 8/10, zeilboten, kano\'s, kajaks en SUP boards.',
                'location': 'We bevinden ons aan de Belterwijde 1, 8355 AA Giethoorn, in het prachtige Weerribben-Wieden natuurgebied.',
                'fallback': 'Ik help u graag verder! Voor specifieke vragen kunt u ons bellen op 0522 281 528 of kijk op onze website voor meer informatie.'
            },
            'en': {
                'greeting': 'Hello! Welcome to Nijenhuis Boat Rental. How can I help you with boat rental in the Weerribben-Wieden area?',
                'pricing': 'For current prices, please call us at 0522 281 528 or check our website. We have various boats available for all group sizes.',
                'booking': 'You can make a reservation by calling us at 0522 281 528. We recommend booking in advance, especially during high season.',
                'opening_hours': 'We are open daily from 09:00 to 18:00 from April 1st to November 1st. Outside the season, we are available by appointment.',
                'contact': 'You can reach us at 0522 281 528 or visit our location at Belterwijde 1, 8355 AA Giethoorn.',
                'boats': 'We have various boats: Tender 720/570, Electrosloep 8/10, sailboats, canoes, kayaks and SUP boards.',
                'location': 'We are located at Belterwijde 1, 8355 AA Giethoorn, in the beautiful Weerribben-Wieden nature reserve.',
                'fallback': 'I\'d be happy to help you further! For specific questions, please call us at 0522 281 528 or check our website for more information.'
            },
            'de': {
                'greeting': 'Hallo! Willkommen bei Nijenhuis Bootsverleih. Wie kann ich Ihnen beim Bootsverleih im Weerribben-Wieden Gebiet helfen?',
                'pricing': 'Für aktuelle Preise rufen Sie uns bitte unter 0522 281 528 an oder schauen Sie auf unsere Website. Wir haben verschiedene Boote für alle Gruppengrößen.',
                'booking': 'Sie können eine Reservierung vornehmen, indem Sie uns unter 0522 281 528 anrufen. Wir empfehlen, besonders in der Hauptsaison rechtzeitig zu buchen.',
                'opening_hours': 'Wir sind täglich von 09:00 bis 18:00 Uhr vom 1. April bis 1. November geöffnet. Außerhalb der Saison sind wir nach Vereinbarung erreichbar.',
                'contact': 'Sie können uns unter 0522 281 528 erreichen oder unseren Standort an der Belterwijde 1, 8355 AA Giethoorn besuchen.',
                'boats': 'Wir haben verschiedene Boote: Tender 720/570, Electrosloep 8/10, Segelboote, Kanus, Kajaks und SUP-Boards.',
                'location': 'Wir befinden uns an der Belterwijde 1, 8355 AA Giethoorn, im wunderschönen Weerribben-Wieden Naturgebiet.',
                'fallback': 'Ich helfe Ihnen gerne weiter! Für spezifische Fragen rufen Sie uns bitte unter 0522 281 528 an oder schauen Sie auf unsere Website für weitere Informationen.'
            }
        }
        
        # Classify the query to get appropriate response type
        response_type = self._classify_query(query)
        
        # Get response in detected language
        language_responses = fallback_responses.get(language, fallback_responses['nl'])
        return language_responses.get(response_type, language_responses['fallback'])
    
    def _classify_query(self, query: str) -> str:
        """Classify query to determine response type"""
        query_lower = query.lower()
        
        if any(word in query_lower for word in ['prijs', 'kost', 'cost', 'price', 'preis', 'kosten', 'pricing']):
            return 'pricing'
        elif any(word in query_lower for word in ['reserveren', 'boeken', 'book', 'reserve', 'buchen', 'reservieren']):
            return 'booking'
        elif any(word in query_lower for word in ['open', 'opening', 'uren', 'hours', 'öffnungszeiten', 'geopend']):
            return 'opening_hours'
        elif any(word in query_lower for word in ['contact', 'bellen', 'call', 'telefoon', 'phone', 'anrufen']):
            return 'contact'
        elif any(word in query_lower for word in ['boot', 'boat', 'tender', 'electrosloep', 'zeilboot', 'kano', 'kajak', 'sup']):
            return 'boats'
        elif any(word in query_lower for word in ['locatie', 'location', 'waar', 'where', 'wo', 'adres', 'address']):
            return 'location'
        elif any(word in query_lower for word in ['hallo', 'hello', 'hi', 'hey', 'goedemorgen', 'good morning', 'guten tag']):
            return 'greeting'
        else:
            return 'fallback'
    
    def get_learning_stats(self) -> Dict[str, Any]:
        """Get learning system statistics"""
        return self.learning_system.get_stats()
    
    def get_neural_network_info(self) -> Dict[str, Any]:
        """Get neural network information"""
        return {
            'input_size': self.neural_network.input_size,
            'hidden_sizes': self.neural_network.hidden_sizes,
            'output_size': self.neural_network.output_size,
            'trained': len(self.improved_responses) > 0
        }