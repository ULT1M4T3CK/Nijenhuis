#!/usr/bin/env python3
"""
Generate Baseline Performance Metrics
Evaluates chatbot model and generates baseline performance report
"""

import json
import os
import sys
from pathlib import Path
from datetime import datetime

# Add project root to path
project_root = Path(__file__).parent.parent.parent.parent
sys.path.insert(0, str(project_root))

def check_dependencies():
    """Check if required dependencies are installed"""
    try:
        import numpy
        return True
    except ImportError:
        print("❌ NumPy is required but not installed.")
        print("\nTo install dependencies, run:")
        print("   pip install numpy>=1.21.0")
        print("\nOr install all requirements:")
        print("   pip install -r requirements.txt")
        return False

def generate_baseline_metrics(output_dir: str = None):
    """
    Generate baseline performance metrics for the chatbot
    
    Args:
        output_dir: Directory to save evaluation reports
    """
    if output_dir is None:
        output_dir = project_root / 'backend' / 'chatbot' / 'training' / 'evaluations'
    
    os.makedirs(output_dir, exist_ok=True)
    
    print("=" * 60)
    print("Generating Baseline Performance Metrics")
    print("=" * 60)
    
    # Check dependencies first
    if not check_dependencies():
        return None
    
    try:
        from backend.chatbot.core.chatbot import Chatbot
        
        print("\n📥 Initializing chatbot...")
        chatbot = Chatbot(use_advanced_nlp=True)
        
        print(f"   ✅ Loaded {len(chatbot.improved_responses)} training samples")
        
        # Generate evaluation report
        print("\n🔍 Evaluating model performance...")
        timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
        output_file = os.path.join(output_dir, f"baseline_metrics_{timestamp}.json")
        
        report = chatbot.evaluate_model(output_file=output_file)
        
        # Print summary
        print("\n" + "=" * 60)
        print("Evaluation Results Summary")
        print("=" * 60)
        
        # Data split
        data_split = report.get('data_split', {})
        print(f"\n📊 Data Split:")
        print(f"   Train: {data_split.get('train_size', 0)} samples")
        print(f"   Validation: {data_split.get('validation_size', 0)} samples")
        print(f"   Test: {data_split.get('test_size', 0)} samples")
        print(f"   Total: {data_split.get('total_size', 0)} samples")
        
        # Language detection
        lang_detection = report.get('language_detection', {})
        if lang_detection:
            test_lang = lang_detection.get('test', {})
            print(f"\n🌍 Language Detection (Test Set):")
            print(f"   Accuracy: {test_lang.get('accuracy', 0):.2%}")
            print(f"   Correct: {test_lang.get('correct', 0)} / {test_lang.get('total', 0)}")
            print(f"   Average Confidence: {test_lang.get('average_confidence', 0):.3f}")
        
        # Intent classification
        intent_class = report.get('intent_classification', {})
        if intent_class:
            test_intent = intent_class.get('test', {})
            print(f"\n🎯 Intent Classification (Test Set):")
            print(f"   Accuracy: {test_intent.get('accuracy', 0):.2%}")
            print(f"   Correct: {test_intent.get('correct', 0)} / {test_intent.get('total', 0)}")
            
            per_intent = test_intent.get('per_intent_metrics', {})
            if per_intent:
                print(f"\n   Per-Intent Metrics:")
                for intent, metrics in sorted(per_intent.items(), 
                                             key=lambda x: x[1].get('support', 0), 
                                             reverse=True)[:5]:
                    precision = metrics.get('precision', 0)
                    recall = metrics.get('recall', 0)
                    f1 = metrics.get('f1_score', 0)
                    support = metrics.get('support', 0)
                    print(f"      {intent}:")
                    print(f"         Precision: {precision:.2%}")
                    print(f"         Recall: {recall:.2%}")
                    print(f"         F1-Score: {f1:.2%}")
                    print(f"         Support: {support}")
        
        # Response matching
        response_match = report.get('response_matching', {})
        if response_match:
            test_match = response_match.get('test', {})
            print(f"\n💬 Response Matching (Test Set):")
            print(f"   Match Rate: {test_match.get('match_rate', 0):.2%}")
            print(f"   Accuracy: {test_match.get('accuracy', 0):.2%}")
            print(f"   Matched: {test_match.get('threshold_matches', 0)} / {test_match.get('total', 0)}")
            print(f"   Average Similarity: {test_match.get('average_similarity', 0):.3f}")
            print(f"   Threshold: {test_match.get('threshold', 0.3)}")
        
        # Save summary report
        summary_file = os.path.join(output_dir, f"baseline_summary_{timestamp}.json")
        summary = {
            'timestamp': datetime.now().isoformat(),
            'training_samples': len(chatbot.improved_responses),
            'data_split': data_split,
            'language_detection': {
                'test_accuracy': lang_detection.get('test', {}).get('accuracy', 0),
                'test_confidence': lang_detection.get('test', {}).get('average_confidence', 0)
            },
            'intent_classification': {
                'test_accuracy': intent_class.get('test', {}).get('accuracy', 0),
                'top_intents': {
                    intent: {
                        'f1': metrics.get('f1_score', 0),
                        'support': metrics.get('support', 0)
                    }
                    for intent, metrics in sorted(
                        intent_class.get('test', {}).get('per_intent_metrics', {}).items(),
                        key=lambda x: x[1].get('support', 0),
                        reverse=True
                    )[:5]
                }
            },
            'response_matching': {
                'test_match_rate': response_match.get('test', {}).get('match_rate', 0),
                'test_accuracy': response_match.get('test', {}).get('accuracy', 0),
                'test_avg_similarity': response_match.get('test', {}).get('average_similarity', 0)
            }
        }
        
        with open(summary_file, 'w', encoding='utf-8') as f:
            json.dump(summary, f, indent=2, ensure_ascii=False)
        
        print(f"\n💾 Reports saved:")
        print(f"   Full report: {output_file}")
        print(f"   Summary: {summary_file}")
        
        print("\n" + "=" * 60)
        print("✅ Baseline metrics generated successfully!")
        print("=" * 60)
        
        return summary
        
    except Exception as e:
        print(f"\n❌ Error generating metrics: {e}")
        import traceback
        traceback.print_exc()
        return None

def main():
    """Main function"""
    summary = generate_baseline_metrics()
    
    if summary:
        print("\n📈 Baseline Performance Metrics:")
        print(f"   Language Detection Accuracy: {summary['language_detection']['test_accuracy']:.2%}")
        print(f"   Intent Classification Accuracy: {summary['intent_classification']['test_accuracy']:.2%}")
        print(f"   Response Match Rate: {summary['response_matching']['test_match_rate']:.2%}")
        return 0
    else:
        return 1

if __name__ == "__main__":
    sys.exit(main())

