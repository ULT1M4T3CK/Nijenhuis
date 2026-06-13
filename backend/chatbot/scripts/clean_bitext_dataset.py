#!/usr/bin/env python3
"""
Clean Bitext Dataset
Filters empty entries and validates data quality
"""

import json
import os
import sys
from pathlib import Path
from typing import Dict, List, Any

# Add project root to path
project_root = Path(__file__).parent.parent.parent.parent
sys.path.insert(0, str(project_root))

def clean_bitext_dataset(input_file: str = None, output_file: str = None) -> Dict[str, Any]:
    """
    Clean Bitext dataset by removing empty entries and validating data
    
    Args:
        input_file: Path to input Bitext dataset JSON
        output_file: Path to output cleaned dataset JSON
        
    Returns:
        Statistics dictionary
    """
    if input_file is None:
        input_file = project_root / 'backend' / 'chatbot' / 'training' / 'external_datasets' / 'bitext_customer_support.json'
    
    if output_file is None:
        output_file = project_root / 'backend' / 'chatbot' / 'training' / 'external_datasets' / 'bitext_customer_support_cleaned.json'
    
    print("=" * 60)
    print("Cleaning Bitext Dataset")
    print("=" * 60)
    print(f"Input: {input_file}")
    print(f"Output: {output_file}")
    print()
    
    # Load dataset
    print("📥 Loading dataset...")
    with open(input_file, 'r', encoding='utf-8') as f:
        dataset = json.load(f)
    
    total_samples = len(dataset.get('samples', []))
    print(f"   Total samples: {total_samples}")
    
    # Filter criteria
    print("\n🔍 Filtering samples...")
    cleaned_samples = []
    stats = {
        'total': total_samples,
        'empty_question': 0,
        'empty_answer': 0,
        'too_short_question': 0,
        'too_short_answer': 0,
        'too_long_question': 0,
        'too_long_answer': 0,
        'valid': 0
    }
    
    for sample in dataset.get('samples', []):
        question = sample.get('question', '').strip()
        answer = sample.get('answer', '').strip()
        
        # Check empty
        if not question:
            stats['empty_question'] += 1
            continue
        
        if not answer:
            stats['empty_answer'] += 1
            continue
        
        # Check length (too short)
        if len(question) < 5:
            stats['too_short_question'] += 1
            continue
        
        if len(answer) < 10:
            stats['too_short_answer'] += 1
            continue
        
        # Check length (too long - might be corrupted)
        if len(question) > 500:
            stats['too_long_question'] += 1
            continue
        
        if len(answer) > 2000:
            stats['too_long_answer'] += 1
            continue
        
        # Valid sample
        cleaned_samples.append(sample)
        stats['valid'] += 1
    
    # Print statistics
    print(f"   Empty questions: {stats['empty_question']}")
    print(f"   Empty answers: {stats['empty_answer']}")
    print(f"   Too short questions: {stats['too_short_question']}")
    print(f"   Too short answers: {stats['too_short_answer']}")
    print(f"   Too long questions: {stats['too_long_question']}")
    print(f"   Too long answers: {stats['too_long_answer']}")
    print(f"   ✅ Valid samples: {stats['valid']}")
    print(f"   📊 Retention rate: {stats['valid'] / total_samples * 100:.1f}%")
    
    # Create cleaned dataset
    cleaned_dataset = {
        'name': dataset.get('name', 'Bitext Customer Support'),
        'source': dataset.get('source', 'Hugging Face'),
        'total_samples': stats['valid'],
        'original_samples': total_samples,
        'cleaning_stats': stats,
        'samples': cleaned_samples
    }
    
    # Save cleaned dataset
    print(f"\n💾 Saving cleaned dataset...")
    with open(output_file, 'w', encoding='utf-8') as f:
        json.dump(cleaned_dataset, f, indent=2, ensure_ascii=False)
    
    file_size_mb = os.path.getsize(output_file) / (1024 * 1024)
    print(f"   ✅ Saved to {output_file}")
    print(f"   📦 File size: {file_size_mb:.2f} MB")
    
    return {
        'stats': stats,
        'output_file': str(output_file),
        'file_size_mb': file_size_mb
    }

def analyze_dataset_quality(dataset_file: str) -> Dict[str, Any]:
    """Analyze dataset quality metrics"""
    print("\n" + "=" * 60)
    print("Dataset Quality Analysis")
    print("=" * 60)
    
    with open(dataset_file, 'r', encoding='utf-8') as f:
        dataset = json.load(f)
    
    samples = dataset.get('samples', [])
    
    # Language distribution
    languages = {}
    intents = {}
    question_lengths = []
    answer_lengths = []
    
    for sample in samples:
        lang = sample.get('language', 'unknown')
        intent = sample.get('intent', 'unknown')
        question = sample.get('question', '')
        answer = sample.get('answer', '')
        
        languages[lang] = languages.get(lang, 0) + 1
        intents[intent] = intents.get(intent, 0) + 1
        question_lengths.append(len(question))
        answer_lengths.append(len(answer))
    
    print(f"\n📊 Language Distribution:")
    for lang, count in sorted(languages.items(), key=lambda x: -x[1]):
        print(f"   {lang}: {count} ({count/len(samples)*100:.1f}%)")
    
    print(f"\n📊 Intent Distribution:")
    for intent, count in sorted(intents.items(), key=lambda x: -x[1])[:10]:
        print(f"   {intent}: {count} ({count/len(samples)*100:.1f}%)")
    
    if question_lengths:
        print(f"\n📏 Question Length Statistics:")
        print(f"   Average: {sum(question_lengths)/len(question_lengths):.1f} chars")
        print(f"   Min: {min(question_lengths)} chars")
        print(f"   Max: {max(question_lengths)} chars")
    
    if answer_lengths:
        print(f"\n📏 Answer Length Statistics:")
        print(f"   Average: {sum(answer_lengths)/len(answer_lengths):.1f} chars")
        print(f"   Min: {min(answer_lengths)} chars")
        print(f"   Max: {max(answer_lengths)} chars")
    
    return {
        'languages': languages,
        'intents': intents,
        'question_lengths': question_lengths,
        'answer_lengths': answer_lengths
    }

def main():
    """Main function"""
    # Clean dataset
    result = clean_bitext_dataset()
    
    # Analyze cleaned dataset
    analyze_dataset_quality(result['output_file'])
    
    print("\n" + "=" * 60)
    print("✅ Dataset cleaning completed!")
    print("=" * 60)
    
    return 0

if __name__ == "__main__":
    sys.exit(main())

