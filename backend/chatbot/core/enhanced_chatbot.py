import json
import os
import time
from typing import Dict, Any, Optional
from backend.chatbot.core.simple_chatbot import SimpleChatbot
from backend.chatbot.core.unsupervised_learning import UnsupervisedLearning
from backend.chatbot.core.neural_network import ChatbotNeuralNetwork

class EnhancedChatbot(SimpleChatbot):
    """Enhanced chatbot that uses training data to improve responses"""
    
    def __init__(self, training_data_file: str = None):
        super().__init__()
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
    
    def find_similar_question(self, query: str) -> Optional[Dict[str, Any]]:
        """Find a similar question in the training data"""
        query_lower = query.lower().strip()
        
        # Direct match
        if query_lower in self.improved_responses:
            return self.improved_responses[query_lower]
        
        # Partial match (check if query contains key words from training data)
        for trained_question, response_data in self.improved_responses.items():
            # Split both questions into words
            query_words = set(query_lower.split())
            trained_words = set(trained_question.split())
            
            # Calculate similarity (Jaccard similarity)
            if len(query_words) > 0 and len(trained_words) > 0:
                intersection = query_words.intersection(trained_words)
                union = query_words.union(trained_words)
                similarity = len(intersection) / len(union)
                
                # If similarity is high enough (more than 50% common words)
                if similarity > 0.5:
                    return response_data
        
        return None
    
    def train_neural_network(self):
        """Train the neural network on existing training data"""
        if not self.improved_responses:
            return
        
        # Prepare training data for neural network
        training_data = []
        
        for question, data in self.improved_responses.items():
            response_type = data.get('response_type', 'general')
            training_data.append((question, response_type))
        
        if training_data:
            print(f"🧠 Training neural network on {len(training_data)} examples...")
            try:
                self.neural_network.train_on_chatbot_data(training_data, epochs=50)
                print("✅ Neural network training completed")
            except Exception as e:
                print(f"⚠️  Neural network training failed: {e}")
    
    def predict_with_neural_network(self, query: str) -> Optional[Dict[str, Any]]:
        """Use neural network to predict response type and improve responses"""
        try:
            response_type, confidence = self.neural_network.predict_response_type(query)
            
            if confidence > 0.3:  # Only use if confidence is reasonable
                return {
                    'response_type': response_type,
                    'confidence': confidence,
                    'neural_improved': True
                }
        except Exception as e:
            print(f"⚠️  Neural network prediction failed: {e}")
        
        return None
    
    def process_query(self, query: str, website_content: str = None) -> Dict[str, Any]:
        """Process a customer query with enhanced training-based improvements and unsupervised learning"""
        
        start_time = time.time()
        
        # First, check if we have a similar question in training data
        similar_response = self.find_similar_question(query)
        
        if similar_response:
            # Use the corrected response from training data
            result = {
                'query': query,
                'detected_language': similar_response.get('language', 'nl'),
                'response_type': similar_response.get('response_type', 'fallback'),
                'response': similar_response['corrected'],
                'website_analysis': None,
                'training_improved': True,
                'original_response': similar_response['original']
            }
            
            # Import the translation function from boat_translations
            try:
                from backend.chatbot.core.boat_translations import translate_boat_names
                # Translate boat names in the response
                result['response'] = translate_boat_names(result['response'], result['detected_language'])
            except ImportError:
                # If import fails, return without translation
                pass
            
            # Record interaction for unsupervised learning
            response_time = time.time() - start_time
            self.learning_system.record_interaction(query, result['response'], True, response_time)
            
            return result
        
        # If no training data found, use the original chatbot
        result = super().process_query(query, website_content)
        result['training_improved'] = False
        
        # Try neural network prediction for better response type classification
        neural_prediction = self.predict_with_neural_network(query)
        if neural_prediction and neural_prediction['confidence'] > 0.5:
            result['response_type'] = neural_prediction['response_type']
            result['neural_improved'] = True
            result['neural_confidence'] = neural_prediction['confidence']
        
        # Check if unsupervised learning can provide a better response
        best_response = self.learning_system.get_best_response_for_question(query)
        if best_response and len(best_response) > len(result['response']):
            result['response'] = best_response
            result['unsupervised_improved'] = True
        
        # Record interaction for unsupervised learning
        response_time = time.time() - start_time
        self.learning_system.record_interaction(query, result['response'], True, response_time)
        
        return result

def demonstrate_enhanced_chatbot():
    """Demonstrate the enhanced chatbot functionality"""
    
    print("=" * 60)
    print("ENHANCED CHATBOT WITH TRAINING DATA")
    print("=" * 60)
    
    # Initialize enhanced chatbot
    chatbot = EnhancedChatbot()
    
    # Sample test queries
    test_queries = [
        "Wat kost de Tender 720?",
        "How much does the Tender 570 cost?",
        "Was sind die Öffnungszeiten?",
        "Waar zijn jullie gevestigd?",
        "Hoe kan ik reserveren?"
    ]
    
    print("\nTesting Enhanced Chatbot:")
    print("-" * 40)
    
    for query in test_queries:
        print(f"\nQuery: {query}")
        result = chatbot.process_query(query)
        
        print(f"Language: {result['detected_language']}")
        print(f"Response Type: {result['response_type']}")
        print(f"Training Improved: {result.get('training_improved', False)}")
        
        if result.get('training_improved'):
            print(f"Original Response: {result.get('original_response', 'N/A')}")
            print(f"Improved Response: {result['response']}")
        else:
            print(f"Response: {result['response']}")
        
        print("-" * 40)

if __name__ == "__main__":
    demonstrate_enhanced_chatbot() 