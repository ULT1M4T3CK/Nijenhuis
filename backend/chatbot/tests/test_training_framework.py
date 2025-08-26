#!/usr/bin/env python3
"""
Test script for the Nijenhuis Chatbot Training Framework
Demonstrates the training and improvement functionality
"""

import json
import os
from backend.chatbot.core.enhanced_chatbot import EnhancedChatbot

def create_sample_training_data():
    """Create sample training data to demonstrate the framework"""
    
    sample_data = {
        "training_sessions": [
            {
                "question": "Wat kost de Tender 720?",
                "original_response": "De Tender 720 kost €230 per dag en is geschikt voor maximaal 12 personen.",
                "corrected_response": "De Tender 720 kost €230 per dag en is geschikt voor maximaal 12 personen. Deze elektrische boot is ideaal voor grotere groepen en heeft een comfortabele uitrusting met overkapping en verwarming.",
                "detected_language": "nl",
                "response_type": "pricing",
                "timestamp": "2025-07-30T10:30:00",
                "status": "Corrected"
            },
            {
                "question": "How much does the Tender 570 cost?",
                "original_response": "The Tender 570 costs €200 per day and is suitable for up to 8 people.",
                "corrected_response": "The Tender 570 costs €200 per day and is suitable for up to 8 people. This electric boat is perfect for families and includes comfortable seating, canopy, and safety equipment.",
                "detected_language": "en",
                "response_type": "pricing",
                "timestamp": "2025-07-30T10:35:00",
                "status": "Corrected"
            },
            {
                "question": "Was sind die Öffnungszeiten?",
                "original_response": "Wir sind täglich von 09:00 bis 18:00 Uhr geöffnet.",
                "corrected_response": "Wir sind täglich von 09:00 bis 18:00 Uhr vom 1. April bis 1. November geöffnet. Außerhalb der Saison sind wir nach Vereinbarung erreichbar. Wir befinden uns in der Belterwijde 1, 8355 AA Giethoorn.",
                "detected_language": "de",
                "response_type": "opening_hours",
                "timestamp": "2025-07-30T10:40:00",
                "status": "Corrected"
            }
        ],
        "improved_responses": {
            "wat kost de tender 720?": {
                "original": "De Tender 720 kost €230 per dag en is geschikt voor maximaal 12 personen.",
                "corrected": "De Tender 720 kost €230 per dag en is geschikt voor maximaal 12 personen. Deze elektrische boot is ideaal voor grotere groepen en heeft een comfortabele uitrusting met overkapping en verwarming.",
                "language": "nl",
                "response_type": "pricing",
                "timestamp": "2025-07-30T10:30:00"
            },
            "how much does the tender 570 cost?": {
                "original": "The Tender 570 costs €200 per day and is suitable for up to 8 people.",
                "corrected": "The Tender 570 costs €200 per day and is suitable for up to 8 people. This electric boat is perfect for families and includes comfortable seating, canopy, and safety equipment.",
                "language": "en",
                "response_type": "pricing",
                "timestamp": "2025-07-30T10:35:00"
            },
            "was sind die öffnungszeiten?": {
                "original": "Wir sind täglich von 09:00 bis 18:00 Uhr geöffnet.",
                "corrected": "Wir sind täglich von 09:00 bis 18:00 Uhr vom 1. April bis 1. November geöffnet. Außerhalb der Saison sind wir nach Vereinbarung erreichbar. Wir befinden uns in der Belterwijde 1, 8355 AA Giethorn.",
                "language": "de",
                "response_type": "opening_hours",
                "timestamp": "2025-07-30T10:40:00"
            }
        },
        "statistics": {
            "total_tests": 3,
            "improved_responses": 3,
            "accuracy_score": 100.0
        }
    }
    
    # Save sample training data
    with open("training_data.json", "w", encoding="utf-8") as f:
        json.dump(sample_data, f, indent=2, ensure_ascii=False)
    
    print("✅ Sample training data created successfully!")

def test_enhanced_chatbot():
    """Test the enhanced chatbot with training data"""
    
    print("\n" + "="*60)
    print("TESTING ENHANCED CHATBOT WITH TRAINING DATA")
    print("="*60)
    
    # Initialize enhanced chatbot
    chatbot = EnhancedChatbot()
    
    # Test questions that should use training data
    test_questions = [
        "Wat kost de Tender 720?",
        "How much does the Tender 570 cost?",
        "Was sind die Öffnungszeiten?",
        "Wat kost de Tender 720 per dag?",  # Similar question
        "How much is the Tender 570?",      # Similar question
        "Wann haben Sie geöffnet?"          # Similar question
    ]
    
    print("\nTesting Enhanced Responses:")
    print("-" * 50)
    
    for question in test_questions:
        print(f"\n🔍 Question: {question}")
        result = chatbot.process_query(question)
        
        print(f"🌍 Language: {result['detected_language']}")
        print(f"📝 Type: {result['response_type']}")
        print(f"🎯 Training Improved: {result.get('training_improved', False)}")
        
        if result.get('training_improved'):
            print(f"📄 Original: {result.get('original_response', 'N/A')}")
            print(f"✨ Improved: {result['response']}")
        else:
            print(f"💬 Response: {result['response']}")
        
        print("-" * 50)

def demonstrate_similarity_matching():
    """Demonstrate similarity matching functionality"""
    
    print("\n" + "="*60)
    print("DEMONSTRATING SIMILARITY MATCHING")
    print("="*60)
    
    chatbot = EnhancedChatbot()
    
    # Test similar questions
    similar_questions = [
        ("Wat kost de Tender 720?", "Original training question"),
        ("Hoeveel kost de Tender 720?", "Similar question with different wording"),
        ("Wat is de prijs van de Tender 720?", "Another variation"),
        ("Tender 720 prijs", "Short version"),
        ("How much is the Tender 720?", "English version")
    ]
    
    print("\nTesting Similarity Matching:")
    print("-" * 50)
    
    for question, description in similar_questions:
        print(f"\n🔍 {description}: '{question}'")
        result = chatbot.process_query(question)
        
        if result.get('training_improved'):
            print(f"✅ MATCHED - Using training data")
            print(f"✨ Response: {result['response'][:100]}...")
        else:
            print(f"❌ NO MATCH - Using original chatbot")
            print(f"💬 Response: {result['response'][:100]}...")

def main():
    """Main test function"""
    print("🤖 Nijenhuis Chatbot Training Framework Test")
    print("=" * 50)
    
    # Create sample training data
    create_sample_training_data()
    
    # Test enhanced chatbot
    test_enhanced_chatbot()
    
    # Demonstrate similarity matching
    demonstrate_similarity_matching()
    
    print("\n" + "="*60)
    print("✅ TRAINING FRAMEWORK TEST COMPLETED")
    print("="*60)
    print("\n📋 Summary:")
    print("• Sample training data created")
    print("• Enhanced chatbot tested with training data")
    print("• Similarity matching demonstrated")
    print("• Training framework is ready to use")
    print("\n🚀 Next steps:")
    print("1. Start the chatbot server: python3 chatbot_backend.py")
    print("2. Launch training framework: python3 start_training.py")
    print("3. Test and improve responses through the UI")

if __name__ == "__main__":
    main() 