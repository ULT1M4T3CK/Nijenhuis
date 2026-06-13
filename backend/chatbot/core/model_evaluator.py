#!/usr/bin/env python3
"""
Model Evaluation and Validation Metrics
Provides train/validation/test split and accuracy metrics for chatbot models
"""

import json
import os
import random
from typing import Dict, List, Tuple, Any, Optional
from collections import defaultdict
import numpy as np

class ModelEvaluator:
    """Evaluates chatbot model performance with proper train/validation/test splits"""
    
    def __init__(self, training_data: Dict[str, Any]):
        """
        Initialize model evaluator
        
        Args:
            training_data: Training data dictionary with 'improved_responses' key
        """
        self.training_data = training_data
        self.improved_responses = training_data.get("improved_responses", {})
        
    def split_data(self, train_ratio: float = 0.7, val_ratio: float = 0.15, 
                   test_ratio: float = 0.15, random_seed: int = 42) -> Dict[str, Dict[str, Any]]:
        """
        Split data into train/validation/test sets
        
        Args:
            train_ratio: Proportion for training set (default 0.7)
            val_ratio: Proportion for validation set (default 0.15)
            test_ratio: Proportion for test set (default 0.15)
            random_seed: Random seed for reproducibility
            
        Returns:
            Dictionary with 'train', 'validation', 'test' keys
        """
        if abs(train_ratio + val_ratio + test_ratio - 1.0) > 1e-6:
            raise ValueError("Ratios must sum to 1.0")
        
        # Set random seed for reproducibility
        random.seed(random_seed)
        np.random.seed(random_seed)
        
        # Get all questions and shuffle
        questions = list(self.improved_responses.keys())
        random.shuffle(questions)
        
        total = len(questions)
        train_end = int(total * train_ratio)
        val_end = train_end + int(total * val_ratio)
        
        # Split into sets
        train_questions = questions[:train_end]
        val_questions = questions[train_end:val_end]
        test_questions = questions[val_end:]
        
        # Create split data dictionaries
        splits = {
            'train': {q: self.improved_responses[q] for q in train_questions},
            'validation': {q: self.improved_responses[q] for q in val_questions},
            'test': {q: self.improved_responses[q] for q in test_questions}
        }
        
        return splits
    
    def evaluate_language_detection(self, language_detector, test_data: Dict[str, Any]) -> Dict[str, float]:
        """
        Evaluate language detection accuracy
        
        Args:
            language_detector: Language detector instance with detect_language method
            test_data: Test data dictionary
            
        Returns:
            Dictionary with accuracy metrics
        """
        correct = 0
        total = 0
        confidence_scores = []
        
        for question, response_data in test_data.items():
            expected_language = response_data.get('language', 'nl')
            
            # Detect language
            if hasattr(language_detector, 'detect_language'):
                detected_result = language_detector.detect_language(question)
                
                # Handle tuple (language, confidence) or string
                if isinstance(detected_result, tuple):
                    detected_language, confidence = detected_result
                    confidence_scores.append(confidence)
                else:
                    detected_language = detected_result
                    confidence_scores.append(1.0)
            else:
                detected_language = 'nl'  # Fallback
                confidence_scores.append(0.5)
            
            if detected_language == expected_language:
                correct += 1
            total += 1
        
        accuracy = correct / total if total > 0 else 0.0
        avg_confidence = np.mean(confidence_scores) if confidence_scores else 0.0
        
        return {
            'accuracy': accuracy,
            'correct': correct,
            'total': total,
            'average_confidence': avg_confidence,
            'error_rate': 1.0 - accuracy
        }
    
    def evaluate_intent_classification(self, classifier_func, test_data: Dict[str, Any]) -> Dict[str, float]:
        """
        Evaluate intent classification accuracy
        
        Args:
            classifier_func: Function that takes question and returns predicted intent
            test_data: Test data dictionary
            
        Returns:
            Dictionary with classification metrics
        """
        correct = 0
        total = 0
        confusion_matrix = defaultdict(lambda: defaultdict(int))
        
        for question, response_data in test_data.items():
            expected_intent = response_data.get('response_type', 'general')
            predicted_intent = classifier_func(question)
            
            confusion_matrix[expected_intent][predicted_intent] += 1
            
            if predicted_intent == expected_intent:
                correct += 1
            total += 1
        
        accuracy = correct / total if total > 0 else 0.0
        
        # Calculate per-intent metrics
        per_intent_metrics = {}
        for intent in set(list(confusion_matrix.keys()) + 
                         [k for v in confusion_matrix.values() for k in v.keys()]):
            true_positives = confusion_matrix[intent].get(intent, 0)
            false_positives = sum(confusion_matrix[other][intent] 
                                for other in confusion_matrix.keys() if other != intent)
            false_negatives = sum(confusion_matrix[intent][other] 
                                for other in confusion_matrix[intent].keys() if other != intent)
            
            precision = true_positives / (true_positives + false_positives) if (true_positives + false_positives) > 0 else 0.0
            recall = true_positives / (true_positives + false_negatives) if (true_positives + false_negatives) > 0 else 0.0
            f1_score = 2 * (precision * recall) / (precision + recall) if (precision + recall) > 0 else 0.0
            
            per_intent_metrics[intent] = {
                'precision': precision,
                'recall': recall,
                'f1_score': f1_score,
                'support': true_positives + false_negatives
            }
        
        return {
            'accuracy': accuracy,
            'correct': correct,
            'total': total,
            'per_intent_metrics': per_intent_metrics,
            'confusion_matrix': dict(confusion_matrix)
        }
    
    def evaluate_response_matching(self, matcher_func, test_data: Dict[str, Any], 
                                   similarity_threshold: float = 0.3) -> Dict[str, float]:
        """
        Evaluate response matching accuracy using similarity
        
        Args:
            matcher_func: Function that takes question and returns (response, similarity_score)
            test_data: Test data dictionary
            similarity_threshold: Minimum similarity threshold for match
            
        Returns:
            Dictionary with matching metrics
        """
        correct_matches = 0
        total = 0
        similarity_scores = []
        threshold_matches = 0
        
        for question, response_data in test_data.items():
            expected_response = response_data.get('corrected', response_data.get('original', ''))
            
            # Get matched response
            match_result = matcher_func(question)
            
            if isinstance(match_result, tuple):
                matched_response, similarity = match_result
            else:
                matched_response = match_result
                similarity = 0.5  # Default similarity
            
            similarity_scores.append(similarity)
            
            if similarity >= similarity_threshold:
                threshold_matches += 1
                # Check if response matches (simple string comparison)
                if matched_response == expected_response or matched_response.startswith(expected_response[:50]):
                    correct_matches += 1
            
            total += 1
        
        match_rate = threshold_matches / total if total > 0 else 0.0
        accuracy = correct_matches / threshold_matches if threshold_matches > 0 else 0.0
        avg_similarity = np.mean(similarity_scores) if similarity_scores else 0.0
        
        return {
            'match_rate': match_rate,
            'accuracy': accuracy,
            'correct_matches': correct_matches,
            'threshold_matches': threshold_matches,
            'total': total,
            'average_similarity': avg_similarity,
            'threshold': similarity_threshold
        }
    
    def generate_evaluation_report(self, splits: Dict[str, Dict[str, Any]], 
                                   language_detector=None, intent_classifier=None,
                                   response_matcher=None) -> Dict[str, Any]:
        """
        Generate comprehensive evaluation report
        
        Args:
            splits: Train/validation/test splits
            language_detector: Optional language detector instance
            intent_classifier: Optional intent classifier function
            response_matcher: Optional response matcher function
            
        Returns:
            Comprehensive evaluation report
        """
        report = {
            'data_split': {
                'train_size': len(splits['train']),
                'validation_size': len(splits['validation']),
                'test_size': len(splits['test']),
                'total_size': len(splits['train']) + len(splits['validation']) + len(splits['test'])
            },
            'language_detection': {},
            'intent_classification': {},
            'response_matching': {}
        }
        
        # Evaluate language detection
        if language_detector:
            report['language_detection'] = {
                'validation': self.evaluate_language_detection(language_detector, splits['validation']),
                'test': self.evaluate_language_detection(language_detector, splits['test'])
            }
        
        # Evaluate intent classification
        if intent_classifier:
            report['intent_classification'] = {
                'validation': self.evaluate_intent_classification(intent_classifier, splits['validation']),
                'test': self.evaluate_intent_classification(intent_classifier, splits['test'])
            }
        
        # Evaluate response matching
        if response_matcher:
            report['response_matching'] = {
                'validation': self.evaluate_response_matching(response_matcher, splits['validation']),
                'test': self.evaluate_response_matching(response_matcher, splits['test'])
            }
        
        return report
    
    def save_evaluation_report(self, report: Dict[str, Any], output_file: str):
        """Save evaluation report to JSON file"""
        # Convert numpy types to native Python types for JSON serialization
        def convert_numpy(obj):
            if isinstance(obj, np.integer):
                return int(obj)
            elif isinstance(obj, np.floating):
                return float(obj)
            elif isinstance(obj, np.ndarray):
                return obj.tolist()
            elif isinstance(obj, dict):
                return {k: convert_numpy(v) for k, v in obj.items()}
            elif isinstance(obj, list):
                return [convert_numpy(item) for item in obj]
            return obj
        
        report_serializable = convert_numpy(report)
        
        with open(output_file, 'w', encoding='utf-8') as f:
            json.dump(report_serializable, f, indent=2, ensure_ascii=False)
        
        print(f"✅ Evaluation report saved to {output_file}")

