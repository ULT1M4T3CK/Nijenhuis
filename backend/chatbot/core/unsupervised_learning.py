#!/usr/bin/env python3
"""
Unsupervised Learning System for Nijenhuis Chatbot
Automatically improves responses based on user interactions and patterns
"""

import json
import os
import re
from typing import Dict, Any, List, Optional
from datetime import datetime, timedelta
from collections import defaultdict, Counter
import difflib

class UnsupervisedLearning:
    """Unsupervised learning system that automatically improves chatbot responses"""
    
    def __init__(self, data_file: str = None):
        if data_file is None:
            self.data_file = os.path.abspath(os.path.join(os.path.dirname(__file__), '..', 'training', 'data', 'unsupervised_learning_data.json'))
        else:
            self.data_file = data_file
        self.interaction_data = self.load_data()
        self.patterns = self.analyze_patterns()
        
    def load_data(self) -> Dict[str, Any]:
        """Load interaction data from file"""
        if os.path.exists(self.data_file):
            try:
                with open(self.data_file, 'r', encoding='utf-8') as f:
                    return json.load(f)
            except Exception as e:
                print(f"Warning: Could not load unsupervised learning data: {e}")
        
        return {
            "interactions": [],
            "patterns": {},
            "improvements": [],
            "statistics": {
                "total_interactions": 0,
                "successful_responses": 0,
                "failed_responses": 0,
                "common_questions": {},
                "response_quality": {}
            }
        }
    
    def save_data(self):
        """Save interaction data to file"""
        try:
            with open(self.data_file, 'w', encoding='utf-8') as f:
                json.dump(self.interaction_data, f, indent=2, ensure_ascii=False)
        except Exception as e:
            print(f"Warning: Could not save unsupervised learning data: {e}")
    
    def record_interaction(self, question: str, response: str, success: bool = True, 
                          response_time: float = 0.0, user_feedback: Optional[str] = None):
        """Record a user interaction for learning"""
        interaction = {
            "timestamp": datetime.now().isoformat(),
            "question": question.lower().strip(),
            "response": response,
            "success": success,
            "response_time": response_time,
            "user_feedback": user_feedback,
            "question_length": len(question),
            "response_length": len(response),
            "question_words": len(question.split()),
            "response_words": len(response.split())
        }
        
        self.interaction_data["interactions"].append(interaction)
        self.interaction_data["statistics"]["total_interactions"] += 1
        
        if success:
            self.interaction_data["statistics"]["successful_responses"] += 1
        else:
            self.interaction_data["statistics"]["failed_responses"] += 1
        
        # Update common questions
        question_key = question.lower().strip()
        if question_key not in self.interaction_data["statistics"]["common_questions"]:
            self.interaction_data["statistics"]["common_questions"][question_key] = 0
        self.interaction_data["statistics"]["common_questions"][question_key] += 1
        
        # Update response quality metrics
        quality_score = self.calculate_response_quality(interaction)
        if question_key not in self.interaction_data["statistics"]["response_quality"]:
            self.interaction_data["statistics"]["response_quality"][question_key] = []
        self.interaction_data["statistics"]["response_quality"][question_key].append(quality_score)
        
        # Keep only recent interactions (last 1000)
        if len(self.interaction_data["interactions"]) > 1000:
            self.interaction_data["interactions"] = self.interaction_data["interactions"][-1000:]
        
        self.save_data()
        self.analyze_patterns()
    
    def calculate_response_quality(self, interaction: Dict[str, Any]) -> float:
        """Calculate quality score for a response"""
        score = 0.0
        
        # Response length (not too short, not too long)
        response_length = interaction["response_length"]
        if 10 <= response_length <= 200:
            score += 0.3
        elif 5 <= response_length <= 300:
            score += 0.2
        
        # Response time (faster is better)
        response_time = interaction["response_time"]
        if response_time < 1.0:
            score += 0.3
        elif response_time < 3.0:
            score += 0.2
        
        # Success rate
        if interaction["success"]:
            score += 0.4
        
        return min(score, 1.0)
    
    def analyze_patterns(self):
        """Analyze interaction patterns to identify improvements"""
        if not self.interaction_data["interactions"]:
            return
        
        # Analyze question patterns
        question_patterns = defaultdict(list)
        response_patterns = defaultdict(list)
        
        for interaction in self.interaction_data["interactions"]:
            question = interaction["question"]
            response = interaction["response"]
            
            # Extract key words from questions
            words = re.findall(r'\b\w+\b', question.lower())
            for word in words:
                if len(word) > 2:  # Skip short words
                    question_patterns[word].append(interaction)
            
            # Group similar questions
            question_patterns[question].append(interaction)
            response_patterns[response].append(interaction)
        
        # Identify common patterns
        patterns = {}
        
        # Most common question words
        word_frequency = Counter()
        for word, interactions in question_patterns.items():
            if len(word) > 2:  # Skip short words
                word_frequency[word] = len(interactions)
        
        patterns["common_words"] = dict(word_frequency.most_common(20))
        
        # Question-response patterns
        qr_patterns = {}
        for question, interactions in question_patterns.items():
            if len(interactions) > 1:  # Only patterns with multiple occurrences
                responses = [i["response"] for i in interactions]
                success_rate = sum(1 for i in interactions if i["success"]) / len(interactions)
                avg_quality = sum(self.calculate_response_quality(i) for i in interactions) / len(interactions)
                
                qr_patterns[question] = {
                    "responses": responses,
                    "success_rate": success_rate,
                    "avg_quality": avg_quality,
                    "frequency": len(interactions)
                }
        
        patterns["question_response"] = qr_patterns
        
        # Identify potential improvements
        improvements = []
        
        # Find questions with low success rates
        for question, data in qr_patterns.items():
            if data["success_rate"] < 0.7 and data["frequency"] > 2:
                improvements.append({
                    "type": "low_success_rate",
                    "question": question,
                    "success_rate": data["success_rate"],
                    "frequency": data["frequency"],
                    "suggested_action": "Review and improve response for this question"
                })
        
        # Find questions with inconsistent responses
        for question, data in qr_patterns.items():
            if len(set(data["responses"])) > 2 and data["frequency"] > 3:
                improvements.append({
                    "type": "inconsistent_responses",
                    "question": question,
                    "response_variations": len(set(data["responses"])),
                    "frequency": data["frequency"],
                    "suggested_action": "Standardize response for this question"
                })
        
        # Find questions with low quality responses
        for question, data in qr_patterns.items():
            if data["avg_quality"] < 0.6 and data["frequency"] > 2:
                improvements.append({
                    "type": "low_quality",
                    "question": question,
                    "avg_quality": data["avg_quality"],
                    "frequency": data["frequency"],
                    "suggested_action": "Improve response quality for this question"
                })
        
        self.interaction_data["patterns"] = patterns
        self.interaction_data["improvements"] = improvements
        self.patterns = patterns
    
    def get_suggested_improvements(self) -> List[Dict[str, Any]]:
        """Get suggested improvements based on pattern analysis"""
        return self.interaction_data.get("improvements", [])
    
    def find_similar_questions(self, question: str, threshold: float = 0.8) -> List[Dict[str, Any]]:
        """Find similar questions from past interactions"""
        question_lower = question.lower().strip()
        similar_questions = []
        
        for interaction in self.interaction_data["interactions"]:
            similarity = difflib.SequenceMatcher(None, question_lower, interaction["question"]).ratio()
            if similarity >= threshold:
                similar_questions.append({
                    "question": interaction["question"],
                    "response": interaction["response"],
                    "similarity": similarity,
                    "success": interaction["success"],
                    "quality": self.calculate_response_quality(interaction)
                })
        
        # Sort by similarity and quality
        similar_questions.sort(key=lambda x: (x["similarity"], x["quality"]), reverse=True)
        return similar_questions[:5]  # Return top 5
    
    def get_best_response_for_question(self, question: str) -> Optional[str]:
        """Get the best response for a given question based on historical data"""
        similar_questions = self.find_similar_questions(question, threshold=0.7)
        
        if not similar_questions:
            return None
        
        # Find the response with the highest quality among successful responses
        successful_responses = [q for q in similar_questions if q["success"]]
        
        if successful_responses:
            best_response = max(successful_responses, key=lambda x: x["quality"])
            return best_response["response"]
        
        # If no successful responses, return the most similar one
        return similar_questions[0]["response"]
    
    def get_statistics(self) -> Dict[str, Any]:
        """Get learning statistics"""
        stats = self.interaction_data["statistics"].copy()
        
        # Calculate success rate
        total = stats["total_interactions"]
        if total > 0:
            stats["success_rate"] = stats["successful_responses"] / total
        else:
            stats["success_rate"] = 0.0
        
        # Calculate average response quality
        all_qualities = []
        for qualities in stats["response_quality"].values():
            all_qualities.extend(qualities)
        
        if all_qualities:
            stats["avg_response_quality"] = sum(all_qualities) / len(all_qualities)
        else:
            stats["avg_response_quality"] = 0.0
        
        # Most common questions
        stats["top_questions"] = dict(Counter(stats["common_questions"]).most_common(10))
        
        return stats
    
    def auto_improve_responses(self, training_data: Dict[str, Any]) -> Dict[str, Any]:
        """Automatically improve training data based on unsupervised learning"""
        improved_data = training_data.copy()
        
        # Get suggested improvements
        improvements = self.get_suggested_improvements()
        
        for improvement in improvements:
            if improvement["type"] == "low_success_rate":
                question = improvement["question"]
                best_response = self.get_best_response_for_question(question)
                
                if best_response:
                    # Add or update in training data
                    question_key = question.lower()
                    if question_key not in improved_data.get("improved_responses", {}):
                        improved_data.setdefault("improved_responses", {})[question_key] = {
                            "original": "Auto-generated from low success rate",
                            "corrected": best_response,
                            "language": "nl",  # Default language
                            "response_type": "auto_improved",
                            "timestamp": datetime.now().isoformat(),
                            "source": "unsupervised_learning"
                        }
        
        return improved_data

def demonstrate_unsupervised_learning():
    """Demonstrate the unsupervised learning system"""
    
    print("🧠 Unsupervised Learning System Demo")
    print("=" * 50)
    
    # Initialize the learning system
    learning = UnsupervisedLearning()
    
    # Simulate some interactions
    test_interactions = [
        ("Wat kost een zeilboot?", "Onze zeilboten kosten €70-85 per dag.", True, 0.5),
        ("Wat kost een zeilboot?", "Zeilboten kosten tussen €70 en €85 per dag.", True, 0.3),
        ("Wat kost een zeilboot?", "Ik weet het niet.", False, 2.0),
        ("Hoe kan ik reserveren?", "U kunt reserveren via telefoon.", True, 0.4),
        ("Hoe kan ik reserveren?", "Bel ons op 0522 281 528.", True, 0.2),
        ("Wat zijn de openingstijden?", "We zijn open van 9:00 tot 18:00.", True, 0.3),
        ("Wat zijn de openingstijden?", "Dagelijks 09:00-18:00 uur.", True, 0.2),
    ]
    
    print("\n📊 Recording test interactions...")
    for question, response, success, time in test_interactions:
        learning.record_interaction(question, response, success, time)
        print(f"   {question} → {response[:50]}... (Success: {success})")
    
    # Analyze patterns
    print("\n🔍 Analyzing patterns...")
    learning.analyze_patterns()
    
    # Get statistics
    stats = learning.get_statistics()
    print(f"\n📈 Statistics:")
    print(f"   Total interactions: {stats['total_interactions']}")
    print(f"   Success rate: {stats['success_rate']:.2%}")
    print(f"   Average quality: {stats['avg_response_quality']:.2f}")
    
    # Get improvements
    improvements = learning.get_suggested_improvements()
    print(f"\n🚀 Suggested improvements:")
    for improvement in improvements:
        print(f"   - {improvement['suggested_action']}")
    
    # Test similar question finding
    print(f"\n🔍 Testing similar question finding:")
    similar = learning.find_similar_questions("Wat kost een zeilboot?")
    print(f"   Found {len(similar)} similar questions")
    
    # Test best response finding
    best_response = learning.get_best_response_for_question("Wat kost een zeilboot?")
    print(f"\n💡 Best response for 'Wat kost een zeilboot?':")
    print(f"   {best_response}")
    
    print("\n" + "=" * 50)
    print("✅ Unsupervised Learning Demo Complete!")

if __name__ == "__main__":
    demonstrate_unsupervised_learning() 