#!/usr/bin/env python3
"""
Neural Network Architecture for Nijenhuis Chatbot
Advanced neural network with multiple layers and activation functions
"""

import numpy as np
import json
import os
from typing import List, Tuple, Dict, Any, Optional
from datetime import datetime
import pickle

class NeuralNetwork:
    """Advanced Neural Network with multiple layers and activation functions"""
    
    def __init__(self, layer_sizes: List[int], learning_rate: float = 0.01):
        """
        Initialize neural network
        
        Args:
            layer_sizes: List of neurons in each layer [input, hidden1, hidden2, ..., output]
            learning_rate: Learning rate for gradient descent
        """
        self.layer_sizes = layer_sizes
        self.learning_rate = learning_rate
        self.num_layers = len(layer_sizes)
        
        # Initialize weights and biases
        self.weights = []
        self.biases = []
        self.activations = []
        self.z_values = []
        
        # Initialize weights with Xavier/Glorot initialization
        for i in range(self.num_layers - 1):
            # Xavier initialization: sqrt(2 / (input_size + output_size))
            scale = np.sqrt(2.0 / (layer_sizes[i] + layer_sizes[i + 1]))
            weight_matrix = np.random.randn(layer_sizes[i + 1], layer_sizes[i]) * scale
            bias_vector = np.zeros((layer_sizes[i + 1], 1))
            
            self.weights.append(weight_matrix)
            self.biases.append(bias_vector)
    
    def relu(self, x: np.ndarray) -> np.ndarray:
        """ReLU activation function: max(0, x)"""
        return np.maximum(0, x)
    
    def relu_derivative(self, x: np.ndarray) -> np.ndarray:
        """Derivative of ReLU function"""
        return np.where(x > 0, 1, 0)
    
    def sigmoid(self, x: np.ndarray) -> np.ndarray:
        """Sigmoid activation function: 1 / (1 + e^(-x))"""
        # Clip to prevent overflow
        x = np.clip(x, -500, 500)
        return 1 / (1 + np.exp(-x))
    
    def sigmoid_derivative(self, x: np.ndarray) -> np.ndarray:
        """Derivative of sigmoid function"""
        sigmoid_x = self.sigmoid(x)
        return sigmoid_x * (1 - sigmoid_x)
    
    def tanh(self, x: np.ndarray) -> np.ndarray:
        """Hyperbolic tangent activation function"""
        return np.tanh(x)
    
    def tanh_derivative(self, x: np.ndarray) -> np.ndarray:
        """Derivative of tanh function"""
        return 1 - np.tanh(x) ** 2
    
    def softmax(self, x: np.ndarray) -> np.ndarray:
        """Softmax activation function for output layer"""
        # Subtract max for numerical stability
        exp_x = np.exp(x - np.max(x, axis=0, keepdims=True))
        return exp_x / np.sum(exp_x, axis=0, keepdims=True)
    
    def forward_propagation(self, X: np.ndarray, activation_functions: List[str] = None) -> np.ndarray:
        """
        Forward propagation through the network
        
        Args:
            X: Input data (features x samples)
            activation_functions: List of activation functions for each layer
            
        Returns:
            Output of the network
        """
        if activation_functions is None:
            # Default: ReLU for hidden layers, softmax for output
            activation_functions = ['relu'] * (self.num_layers - 2) + ['softmax']
        
        self.activations = [X]
        self.z_values = []
        
        for i in range(self.num_layers - 1):
            # Linear transformation
            z = np.dot(self.weights[i], self.activations[-1]) + self.biases[i]
            self.z_values.append(z)
            
            # Apply activation function
            if i < self.num_layers - 2:  # Hidden layers
                if activation_functions[i] == 'relu':
                    activation = self.relu(z)
                elif activation_functions[i] == 'sigmoid':
                    activation = self.sigmoid(z)
                elif activation_functions[i] == 'tanh':
                    activation = self.tanh(z)
                else:
                    activation = self.relu(z)  # Default to ReLU
            else:  # Output layer
                if activation_functions[-1] == 'softmax':
                    activation = self.softmax(z)
                elif activation_functions[-1] == 'sigmoid':
                    activation = self.sigmoid(z)
                else:
                    activation = self.softmax(z)  # Default to softmax
            
            self.activations.append(activation)
        
        return self.activations[-1]
    
    def predict(self, X: np.ndarray, activation_functions: List[str] = None) -> np.ndarray:
        """Make predictions using the trained network"""
        output = self.forward_propagation(X, activation_functions)
        return output
    
    def backward_propagation(self, X: np.ndarray, Y: np.ndarray, activation_functions: List[str] = None) -> Tuple[List[np.ndarray], List[np.ndarray]]:
        """Backward propagation to compute gradients"""
        if activation_functions is None:
            activation_functions = ['relu'] * (self.num_layers - 2) + ['softmax']
        
        m = X.shape[1]  # Number of samples
        
        # Initialize gradients
        weight_gradients = [np.zeros_like(w) for w in self.weights]
        bias_gradients = [np.zeros_like(b) for b in self.biases]
        
        # Compute error at output layer
        delta = self.activations[-1] - Y
        
        # Backpropagate through layers
        for i in range(self.num_layers - 2, -1, -1):
            # Compute gradients for current layer
            weight_gradients[i] = np.dot(delta, self.activations[i].T) / m
            bias_gradients[i] = np.sum(delta, axis=1, keepdims=True) / m
            
            if i > 0:  # Not the first layer
                # Compute delta for previous layer
                if activation_functions[i-1] == 'relu':
                    delta = np.dot(self.weights[i].T, delta) * self.relu_derivative(self.z_values[i-1])
                elif activation_functions[i-1] == 'sigmoid':
                    delta = np.dot(self.weights[i].T, delta) * self.sigmoid_derivative(self.z_values[i-1])
                elif activation_functions[i-1] == 'tanh':
                    delta = np.dot(self.weights[i].T, delta) * self.tanh_derivative(self.z_values[i-1])
                else:
                    delta = np.dot(self.weights[i].T, delta) * self.relu_derivative(self.z_values[i-1])
        
        return weight_gradients, bias_gradients
    
    def update_parameters(self, weight_gradients: List[np.ndarray], bias_gradients: List[np.ndarray]):
        """Update network parameters using gradient descent"""
        for i in range(len(self.weights)):
            self.weights[i] -= self.learning_rate * weight_gradients[i]
            self.biases[i] -= self.learning_rate * bias_gradients[i]
    
    def train(self, X: np.ndarray, Y: np.ndarray, epochs: int, batch_size: int = 32, 
              activation_functions: List[str] = None, verbose: bool = True) -> List[float]:
        """Train the neural network"""
        losses = []
        
        for epoch in range(epochs):
            # Shuffle data
            indices = np.random.permutation(X.shape[1])
            X_shuffled = X[:, indices]
            Y_shuffled = Y[:, indices]
            
            # Mini-batch training
            for i in range(0, X.shape[1], batch_size):
                X_batch = X_shuffled[:, i:i+batch_size]
                Y_batch = Y_shuffled[:, i:i+batch_size]
                
                # Forward pass
                output = self.forward_propagation(X_batch, activation_functions)
                
                # Compute loss (cross-entropy for classification)
                epsilon = 1e-15  # Prevent log(0)
                loss = -np.mean(np.sum(Y_batch * np.log(output + epsilon), axis=0))
                
                # Backward pass
                weight_gradients, bias_gradients = self.backward_propagation(X_batch, Y_batch, activation_functions)
                
                # Update parameters
                self.update_parameters(weight_gradients, bias_gradients)
            
            losses.append(loss)
            
            if verbose and (epoch + 1) % 10 == 0:
                print(f"Epoch {epoch + 1}/{epochs}, Loss: {loss:.4f}")
        
        return losses

class ChatbotNeuralNetwork:
    """Specialized neural network for chatbot applications"""
    
    def __init__(self, input_size: int = 100, hidden_sizes: List[int] = [128, 64], output_size: int = 10):
        """
        Initialize chatbot neural network
        
        Args:
            input_size: Size of input features (word embeddings, etc.)
            hidden_sizes: List of hidden layer sizes
            output_size: Number of output classes (response types)
        """
        layer_sizes = [input_size] + hidden_sizes + [output_size]
        self.network = NeuralNetwork(layer_sizes, learning_rate=0.001)
        self.input_size = input_size
        self.output_size = output_size
        
        # Vocabulary and word embeddings
        self.vocabulary = {}
        self.word_embeddings = {}
        self.response_types = ['pricing', 'booking', 'opening_hours', 'contact', 'general']
    
    def preprocess_text(self, text: str) -> np.ndarray:
        """Convert text to numerical features"""
        # Simple bag-of-words representation
        words = text.lower().split()
        features = np.zeros(self.input_size)
        
        for i, word in enumerate(words[:self.input_size]):
            if word in self.vocabulary:
                features[self.vocabulary[word]] = 1
            else:
                # Add new word to vocabulary
                if len(self.vocabulary) < self.input_size:
                    self.vocabulary[word] = len(self.vocabulary)
                    features[self.vocabulary[word]] = 1
        
        return features.reshape(-1, 1)
    
    def encode_response_type(self, response_type: str) -> np.ndarray:
        """Convert response type to one-hot encoding"""
        encoding = np.zeros((self.output_size, 1))
        if response_type in self.response_types:
            idx = self.response_types.index(response_type)
            encoding[idx] = 1
        return encoding
    
    def train_on_chatbot_data(self, training_data: List[Tuple[str, str]], epochs: int = 100):
        """Train the network on chatbot conversation data"""
        if not training_data:
            print("No training data provided")
            return
        
        # Prepare training data
        X = []
        Y = []
        
        for question, response_type in training_data:
            features = self.preprocess_text(question)
            target = self.encode_response_type(response_type)
            
            X.append(features.flatten())
            Y.append(target.flatten())
        
        X = np.array(X).T
        Y = np.array(Y).T
        
        # Train the network
        print(f"Training neural network with {len(training_data)} samples...")
        losses = self.network.train(X, Y, epochs=epochs, verbose=True)
        
        print(f"Training completed. Final loss: {losses[-1]:.4f}")
        return losses
    
    def predict_response_type(self, question: str) -> Tuple[str, float]:
        """Predict response type for a given question"""
        features = self.preprocess_text(question)
        output = self.network.predict(features.reshape(-1, 1))
        
        # Get predicted class and confidence
        predicted_idx = np.argmax(output)
        confidence = output[predicted_idx, 0]
        
        response_type = self.response_types[predicted_idx] if predicted_idx < len(self.response_types) else 'general'
        
        return response_type, confidence

def demonstrate_neural_network():
    """Demonstrate the neural network capabilities"""
    
    print("🧠 Neural Network Architecture Demo")
    print("=" * 50)
    
    # Create a simple neural network
    layer_sizes = [10, 8, 6, 3]  # Input: 10, Hidden: 8, 6, Output: 3
    nn = NeuralNetwork(layer_sizes, learning_rate=0.01)
    
    print(f"✅ Created neural network with {len(layer_sizes)} layers:")
    for i, size in enumerate(layer_sizes):
        print(f"   Layer {i}: {size} neurons")
    
    # Generate sample data
    X = np.random.randn(10, 5)  # 10 features, 5 samples
    
    print(f"\n📊 Input data shape: X={X.shape}")
    
    # Test forward propagation
    print("\n🚀 Testing forward propagation...")
    output = nn.forward_propagation(X)
    print(f"Output shape: {output.shape}")
    print(f"Sample output: {output[:, 0]}")
    
    # Test activation functions
    print("\n⚡ Testing activation functions...")
    test_data = np.array([[-2, -1, 0, 1, 2]])
    
    print(f"Input: {test_data}")
    print(f"ReLU: {nn.relu(test_data)}")
    print(f"Sigmoid: {nn.sigmoid(test_data)}")
    print(f"Tanh: {nn.tanh(test_data)}")
    
    # Create chatbot neural network
    print("\n🤖 Creating chatbot neural network...")
    chatbot_nn = ChatbotNeuralNetwork(input_size=50, hidden_sizes=[32, 16], output_size=5)
    
    # Sample training data
    training_data = [
        ("Wat kost een zeilboot?", "pricing"),
        ("Hoe kan ik reserveren?", "booking"),
        ("Wat zijn de openingstijden?", "opening_hours"),
        ("Wat is jullie telefoonnummer?", "contact"),
        ("Heeft u elektrische boten?", "general"),
        ("How much does a canoe cost?", "pricing"),
        ("Can I make a reservation?", "booking"),
        ("What are your opening hours?", "opening_hours"),
        ("Wie viel kostet ein Segelboot?", "pricing"),
        ("Kann ich reservieren?", "booking")
    ]
    
    print(f"\n📚 Training on {len(training_data)} conversation examples...")
    chatbot_nn.train_on_chatbot_data(training_data, epochs=20)
    
    # Test predictions
    test_questions = [
        "Wat kost de Tender 720?",
        "Hoe kan ik boeken?",
        "Wann sind Sie geöffnet?",
        "What is your phone number?"
    ]
    
    print("\n🔍 Testing chatbot predictions:")
    for question in test_questions:
        response_type, confidence = chatbot_nn.predict_response_type(question)
        print(f"   '{question}' → {response_type} (confidence: {confidence:.3f})")
    
    print("\n" + "=" * 50)
    print("✅ Neural Network Demo Complete!")

if __name__ == "__main__":
    demonstrate_neural_network() 