#!/usr/bin/env python3
"""
Verify External Dataset Integration
Script to verify that external datasets are properly integrated and being used
"""

import json
import os
import sys
from pathlib import Path

# Add project root to path
project_root = Path(__file__).parent.parent.parent.parent
sys.path.insert(0, str(project_root))

def verify_dataset_files():
    """Verify that external dataset files exist"""
    print("=" * 60)
    print("Dataset Integration Verification")
    print("=" * 60)
    
    results = {
        'bitext_dataset': False,
        'multilingual_dataset': False,
        'enhanced_training_data': False,
        'dataset_stats': {}
    }
    
    # Check Bitext dataset
    bitext_file = project_root / 'backend' / 'chatbot' / 'training' / 'external_datasets' / 'bitext_customer_support.json'
    if bitext_file.exists():
        try:
            with open(bitext_file, 'r', encoding='utf-8') as f:
                bitext_data = json.load(f)
                total_samples = bitext_data.get('total_samples', 0)
                actual_samples = len(bitext_data.get('samples', []))
                
                # Count non-empty samples
                non_empty = sum(1 for s in bitext_data.get('samples', []) 
                              if s.get('question', '').strip() and s.get('answer', '').strip())
                
                results['bitext_dataset'] = True
                results['dataset_stats']['bitext'] = {
                    'total_samples': total_samples,
                    'actual_samples': actual_samples,
                    'non_empty_samples': non_empty,
                    'file_size_mb': bitext_file.stat().st_size / (1024 * 1024)
                }
                print(f"✅ Bitext dataset found: {total_samples} claimed, {actual_samples} actual, {non_empty} non-empty")
        except Exception as e:
            print(f"❌ Error reading Bitext dataset: {e}")
    else:
        print("❌ Bitext dataset file not found")
    
    # Check Multilingual dataset
    multilingual_file = project_root / 'backend' / 'chatbot' / 'training' / 'external_datasets' / 'multilingual_customer_support.json'
    if multilingual_file.exists():
        try:
            with open(multilingual_file, 'r', encoding='utf-8') as f:
                multilingual_data = json.load(f)
                total_samples = multilingual_data.get('total_samples', 0)
                actual_samples = len(multilingual_data.get('samples', []))
                
                results['multilingual_dataset'] = True
                results['dataset_stats']['multilingual'] = {
                    'total_samples': total_samples,
                    'actual_samples': actual_samples,
                    'file_size_mb': multilingual_file.stat().st_size / (1024 * 1024)
                }
                print(f"✅ Multilingual dataset found: {total_samples} samples")
        except Exception as e:
            print(f"❌ Error reading Multilingual dataset: {e}")
    else:
        print("❌ Multilingual dataset file not found")
    
    # Check Enhanced training data
    enhanced_file = project_root / 'backend' / 'chatbot' / 'training' / 'enhanced_training_data.json'
    if enhanced_file.exists():
        try:
            with open(enhanced_file, 'r', encoding='utf-8') as f:
                enhanced_data = json.load(f)
                
                # Check if it contains external dataset references
                metadata = enhanced_data.get('metadata', {})
                external_datasets = metadata.get('external_datasets', [])
                
                # Count improved responses
                training_data = enhanced_data.get('training_data', {})
                improved_responses = training_data.get('improved_responses', {})
                
                # Count responses from external sources
                external_responses = sum(1 for r in improved_responses.values() 
                                       if r.get('source', '').startswith('external_'))
                
                results['enhanced_training_data'] = True
                results['dataset_stats']['enhanced_training'] = {
                    'total_responses': len(improved_responses),
                    'external_responses': external_responses,
                    'external_datasets_used': external_datasets,
                    'file_size_mb': enhanced_file.stat().st_size / (1024 * 1024)
                }
                print(f"✅ Enhanced training data found:")
                print(f"   - Total responses: {len(improved_responses)}")
                print(f"   - External responses: {external_responses}")
                print(f"   - External datasets: {', '.join(external_datasets)}")
        except Exception as e:
            print(f"❌ Error reading Enhanced training data: {e}")
    else:
        print("❌ Enhanced training data file not found")
    
    return results

def verify_chatbot_usage():
    """Verify that chatbot is using enhanced training data"""
    print("\n" + "=" * 60)
    print("Chatbot Usage Verification")
    print("=" * 60)
    
    try:
        from backend.chatbot.core.chatbot import Chatbot
        
        # Initialize chatbot
        chatbot = Chatbot()
        
        # Check training data source
        training_file = chatbot.training_data_file
        improved_responses = chatbot.improved_responses
        
        print(f"✅ Chatbot initialized successfully")
        print(f"   - Training data file: {training_file}")
        print(f"   - Training samples loaded: {len(improved_responses)}")
        
        # Check if using enhanced data
        if 'enhanced_training_data.json' in training_file:
            print(f"   ✅ Using enhanced training data")
        else:
            print(f"   ⚠️ Using basic training data (enhanced data not found)")
        
        # Count external responses
        external_count = sum(1 for r in improved_responses.values() 
                           if r.get('source', '').startswith('external_'))
        print(f"   - External dataset responses: {external_count}")
        
        return {
            'success': True,
            'training_file': training_file,
            'samples_loaded': len(improved_responses),
            'external_responses': external_count,
            'using_enhanced': 'enhanced_training_data.json' in training_file
        }
    except Exception as e:
        print(f"❌ Error verifying chatbot usage: {e}")
        import traceback
        traceback.print_exc()
        return {'success': False, 'error': str(e)}

def main():
    """Main verification function"""
    print("\n🔍 Verifying External Dataset Integration\n")
    
    # Verify dataset files
    dataset_results = verify_dataset_files()
    
    # Verify chatbot usage
    chatbot_results = verify_chatbot_usage()
    
    # Summary
    print("\n" + "=" * 60)
    print("Verification Summary")
    print("=" * 60)
    
    all_good = (
        dataset_results['bitext_dataset'] and
        dataset_results['multilingual_dataset'] and
        dataset_results['enhanced_training_data'] and
        chatbot_results.get('success', False) and
        chatbot_results.get('using_enhanced', False)
    )
    
    if all_good:
        print("✅ All verifications passed!")
        print("   - External datasets exist and are integrated")
        print("   - Chatbot is using enhanced training data")
        print("   - External responses are available in chatbot")
    else:
        print("⚠️ Some verifications failed:")
        if not dataset_results['bitext_dataset']:
            print("   - Bitext dataset not found")
        if not dataset_results['multilingual_dataset']:
            print("   - Multilingual dataset not found")
        if not dataset_results['enhanced_training_data']:
            print("   - Enhanced training data not found")
        if not chatbot_results.get('success', False):
            print("   - Chatbot initialization failed")
        if not chatbot_results.get('using_enhanced', False):
            print("   - Chatbot not using enhanced training data")
    
    # Save results
    results = {
        'dataset_verification': dataset_results,
        'chatbot_verification': chatbot_results,
        'all_passed': all_good
    }
    
    results_file = project_root / 'backend' / 'chatbot' / 'training' / 'dataset_verification_results.json'
    with open(results_file, 'w', encoding='utf-8') as f:
        json.dump(results, f, indent=2, ensure_ascii=False)
    
    print(f"\n📊 Results saved to: {results_file}")
    
    return 0 if all_good else 1

if __name__ == "__main__":
    sys.exit(main())

