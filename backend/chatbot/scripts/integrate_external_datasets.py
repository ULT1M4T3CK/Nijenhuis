#!/usr/bin/env python3
"""
Integrate External Datasets
Properly integrates cleaned external datasets into training data
"""

import json
import os
import sys
from pathlib import Path
from typing import Dict, Any, List
from datetime import datetime

# Add project root to path
project_root = Path(__file__).parent.parent.parent.parent
sys.path.insert(0, str(project_root))

def load_cleaned_bitext_dataset(dataset_file: str = None) -> Dict[str, Any]:
    """Load cleaned Bitext dataset"""
    if dataset_file is None:
        dataset_file = project_root / 'backend' / 'chatbot' / 'training' / 'external_datasets' / 'bitext_customer_support_cleaned.json'
    
    if not os.path.exists(dataset_file):
        print(f"⚠️ Cleaned Bitext dataset not found: {dataset_file}")
        print("   Run clean_bitext_dataset.py first")
        return {}
    
    with open(dataset_file, 'r', encoding='utf-8') as f:
        return json.load(f)

def load_multilingual_dataset(dataset_file: str = None) -> Dict[str, Any]:
    """Load multilingual dataset"""
    if dataset_file is None:
        dataset_file = project_root / 'backend' / 'chatbot' / 'training' / 'external_datasets' / 'multilingual_customer_support.json'
    
    if not os.path.exists(dataset_file):
        print(f"⚠️ Multilingual dataset not found: {dataset_file}")
        return {}
    
    with open(dataset_file, 'r', encoding='utf-8') as f:
        return json.load(f)

def load_existing_training_data(training_file: str = None) -> Dict[str, Any]:
    """Load existing training data"""
    if training_file is None:
        training_file = project_root / 'backend' / 'chatbot' / 'training' / 'data' / 'training_data.json'
    
    if not os.path.exists(training_file):
        return {
            "training_sessions": [],
            "improved_responses": {},
            "statistics": {
                "total_tests": 0,
                "improved_responses": 0,
                "accuracy_score": 0.0
            }
        }
    
    with open(training_file, 'r', encoding='utf-8') as f:
        return json.load(f)

def map_intent_to_response_type(intent: str) -> str:
    """Map external dataset intent to our response types"""
    intent_mapping = {
        'cancel_order': 'booking',
        'cancellation': 'booking',
        'shipping': 'general',
        'payment': 'contact',
        'warranty': 'general',
        'returns': 'booking',
        'general': 'general',
        'pricing': 'pricing',
        'booking': 'booking',
        'opening_hours': 'opening_hours',
        'contact': 'contact',
        'boats': 'boats',
        'location': 'location'
    }
    return intent_mapping.get(intent.lower(), 'general')

def integrate_datasets(
    existing_data: Dict[str, Any],
    bitext_data: Dict[str, Any] = None,
    multilingual_data: Dict[str, Any] = None,
    max_samples_per_dataset: int = 1000,
    domain_filter: bool = True
) -> Dict[str, Any]:
    """
    Integrate external datasets into training data
    
    Args:
        existing_data: Existing training data
        bitext_data: Bitext dataset
        multilingual_data: Multilingual dataset
        max_samples_per_dataset: Maximum samples to take from each dataset
        domain_filter: Whether to filter for boat rental domain relevance
        
    Returns:
        Enhanced training data
    """
    print("=" * 60)
    print("Integrating External Datasets")
    print("=" * 60)
    
    enhanced_responses = existing_data.get("improved_responses", {}).copy()
    original_count = len(enhanced_responses)
    
    # Domain-relevant keywords for filtering
    domain_keywords = [
        'boat', 'boot', 'rental', 'verhuur', 'reservation', 'reserveren',
        'price', 'prijs', 'cost', 'kost', 'booking', 'boeken',
        'hour', 'uren', 'time', 'tijd', 'open', 'opening',
        'contact', 'phone', 'telefoon', 'location', 'locatie',
        'canoe', 'kano', 'kayak', 'kajak', 'sail', 'zeil'
    ]
    
    def is_domain_relevant(text: str) -> bool:
        """Check if text is relevant to boat rental domain"""
        if not domain_filter:
            return True
        text_lower = text.lower()
        return any(keyword in text_lower for keyword in domain_keywords)
    
    # Integrate Bitext dataset
    if bitext_data:
        print(f"\n📊 Integrating Bitext dataset...")
        samples = bitext_data.get('samples', [])[:max_samples_per_dataset]
        integrated = 0
        
        for sample in samples:
            question = sample.get('question', '').strip().lower()
            answer = sample.get('answer', '').strip()
            intent = sample.get('intent', 'general')
            language = sample.get('language', 'en')
            
            if question and answer and is_domain_relevant(question + ' ' + answer):
                response_type = map_intent_to_response_type(intent)
                
                # Only add if not already exists
                if question not in enhanced_responses:
                    enhanced_responses[question] = {
                        "original": answer,
                        "corrected": answer,
                        "language": language,
                        "response_type": response_type,
                        "timestamp": datetime.now().isoformat(),
                        "source": "external_bitext",
                        "confidence": 0.8  # Lower confidence for generic customer support data
                    }
                    integrated += 1
        
        print(f"   ✅ Integrated {integrated} samples from Bitext")
    
    # Integrate Multilingual dataset
    if multilingual_data:
        print(f"\n📊 Integrating Multilingual dataset...")
        samples = multilingual_data.get('samples', [])
        integrated = 0
        
        for sample in samples:
            question = sample.get('question', '').strip().lower()
            answer = sample.get('answer', '').strip()
            intent = sample.get('intent', 'general')
            language = sample.get('language', 'en')
            
            if question and answer:
                response_type = map_intent_to_response_type(intent)
                
                # Only add if not already exists
                if question not in enhanced_responses:
                    enhanced_responses[question] = {
                        "original": answer,
                        "corrected": answer,
                        "language": language,
                        "response_type": response_type,
                        "timestamp": datetime.now().isoformat(),
                        "source": "external_multilingual",
                        "confidence": 0.9  # Higher confidence for curated multilingual data
                    }
                    integrated += 1
        
        print(f"   ✅ Integrated {integrated} samples from Multilingual")
    
    # Update training data
    existing_data["improved_responses"] = enhanced_responses
    
    # Update statistics
    total_responses = len(enhanced_responses)
    new_responses = total_responses - original_count
    
    existing_data["statistics"]["total_tests"] = total_responses
    existing_data["statistics"]["improved_responses"] = total_responses
    existing_data["statistics"]["external_integration"] = {
        "original_count": original_count,
        "new_responses": new_responses,
        "total_responses": total_responses,
        "integration_date": datetime.now().isoformat()
    }
    
    print(f"\n✅ Integration complete!")
    print(f"   Original responses: {original_count}")
    print(f"   New responses: {new_responses}")
    print(f"   Total responses: {total_responses}")
    
    return existing_data

def save_enhanced_training_data(data: Dict[str, Any], output_file: str = None):
    """Save enhanced training data"""
    if output_file is None:
        output_file = project_root / 'backend' / 'chatbot' / 'training' / 'enhanced_training_data.json'
    
    # Create comprehensive format
    enhanced_data = {
        "metadata": {
            "created": datetime.now().isoformat(),
            "version": "2.1",
            "enhanced": True,
            "external_datasets": ["bitext", "multilingual"],
            "integration_date": datetime.now().isoformat()
        },
        "training_data": data,
        "statistics": {
            "total_responses": len(data.get("improved_responses", {})),
            "languages": list(set(
                r.get('language', 'nl') 
                for r in data.get("improved_responses", {}).values()
            )),
            "response_types": list(set(
                r.get('response_type', 'general') 
                for r in data.get("improved_responses", {}).values()
            ))
        }
    }
    
    with open(output_file, 'w', encoding='utf-8') as f:
        json.dump(enhanced_data, f, indent=2, ensure_ascii=False)
    
    file_size_mb = os.path.getsize(output_file) / (1024 * 1024)
    print(f"\n💾 Saved enhanced training data to {output_file}")
    print(f"   📦 File size: {file_size_mb:.2f} MB")
    
    return output_file

def main():
    """Main integration function"""
    print("\n🚀 External Dataset Integration\n")
    
    # Load datasets
    print("📥 Loading datasets...")
    existing_data = load_existing_training_data()
    bitext_data = load_cleaned_bitext_dataset()
    multilingual_data = load_multilingual_dataset()
    
    if not bitext_data and not multilingual_data:
        print("❌ No external datasets found. Please run clean_bitext_dataset.py first.")
        return 1
    
    # Integrate
    enhanced_data = integrate_datasets(
        existing_data,
        bitext_data=bitext_data if bitext_data else None,
        multilingual_data=multilingual_data if multilingual_data else None,
        max_samples_per_dataset=1000,
        domain_filter=True
    )
    
    # Save
    output_file = save_enhanced_training_data(enhanced_data)
    
    print("\n" + "=" * 60)
    print("✅ Integration completed successfully!")
    print("=" * 60)
    print(f"\n📝 Next steps:")
    print(f"   1. Verify integration: python3 backend/chatbot/scripts/verify_dataset_integration.py")
    print(f"   2. Evaluate model: Use chatbot.evaluate_model() to get baseline metrics")
    
    return 0

if __name__ == "__main__":
    sys.exit(main())

