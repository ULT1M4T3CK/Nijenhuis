#!/usr/bin/env python3
"""
Integrate publicly available datasets with existing training data
Creates enhanced training data for AlBot
"""

import os
import json
from typing import Dict, List, Any
from pathlib import Path
from datetime import datetime

from download_public_datasets import PublicDatasetDownloader


class DatasetIntegrator:
    """Integrate public datasets with existing training data"""
    
    def __init__(self, training_data_file: str = None, datasets_dir: str = None):
        """
        Initialize integrator
        
        Args:
            training_data_file: Path to existing training data file
            datasets_dir: Directory containing downloaded datasets
        """
        if training_data_file is None:
            current_dir = os.path.dirname(os.path.abspath(__file__))
            training_data_file = os.path.join(current_dir, 'data', 'training_data.json')
        
        self.training_data_file = training_data_file
        self.existing_data = self._load_existing_data()
        
        if datasets_dir is None:
            current_dir = os.path.dirname(os.path.abspath(__file__))
            datasets_dir = os.path.join(current_dir, 'datasets')
        
        self.datasets_dir = Path(datasets_dir)
    
    def _load_existing_data(self) -> Dict[str, Any]:
        """Load existing training data"""
        if os.path.exists(self.training_data_file):
            try:
                with open(self.training_data_file, 'r', encoding='utf-8') as f:
                    return json.load(f)
            except Exception as e:
                print(f"⚠️ Error loading existing training data: {e}")
        
        return {
            'training_sessions': [],
            'improved_responses': {},
            'statistics': {
                'total_tests': 0,
                'improved_responses': 0,
                'accuracy_score': 0.0
            }
        }
    
    def _convert_to_training_format(self, conversation: Dict[str, Any]) -> Dict[str, Any]:
        """
        Convert public dataset conversation to training data format
        
        Args:
            conversation: Conversation from public dataset
            
        Returns:
            Training data entry
        """
        # Extract question and response
        question = conversation.get('question') or conversation.get('user', '')
        response = conversation.get('response') or conversation.get('assistant', '')
        language = conversation.get('language', 'nl')
        
        if not question or not response:
            return None
        
        # Determine response type from intent or context
        intent = conversation.get('intent', 'general')
        context = conversation.get('context', '')
        
        response_type_map = {
            'pricing': 'pricing',
            'booking': 'booking',
            'availability': 'booking',
            'contact': 'contact',
            'location': 'location',
            'hours': 'opening_hours',
            'cancellation': 'booking',
            'refund': 'booking',
            'general': 'general',
            'greeting': 'greeting',
            'question': 'general',
            'thanks': 'general',
            'farewell': 'general'
        }
        
        response_type = response_type_map.get(intent) or response_type_map.get(context.lower(), 'general')
        
        return {
            'question': question.lower().strip(),
            'response': response.strip(),
            'language': language,
            'response_type': response_type,
            'source': conversation.get('source', 'public_dataset'),
            'timestamp': datetime.now().isoformat()
        }
    
    def integrate_public_datasets(self) -> Dict[str, Any]:
        """
        Integrate all public datasets into training data
        
        Returns:
            Enhanced training data
        """
        print("🔄 Integrating public datasets with existing training data...")
        
        # Download datasets if not already downloaded
        downloader = PublicDatasetDownloader(output_dir=str(self.datasets_dir))
        
        # Check if datasets exist, if not download them
        dataset_files = list(self.datasets_dir.glob('*.json'))
        if not dataset_files:
            print("📥 Datasets not found, downloading...")
            downloader.download_all_datasets()
        
        # Load all datasets
        all_conversations = []
        
        for dataset_file in self.datasets_dir.glob('*.json'):
            try:
                with open(dataset_file, 'r', encoding='utf-8') as f:
                    dataset_data = json.load(f)
                    if 'conversations' in dataset_data:
                        all_conversations.extend(dataset_data['conversations'])
            except Exception as e:
                print(f"⚠️ Error loading {dataset_file}: {e}")
        
        print(f"📊 Found {len(all_conversations)} conversations from public datasets")
        
        # Convert to training format
        new_responses = {}
        added_count = 0
        skipped_count = 0
        
        for conversation in all_conversations:
            training_entry = self._convert_to_training_format(conversation)
            
            if not training_entry:
                skipped_count += 1
                continue
            
            question_key = training_entry['question']
            
            # Skip if already exists (keep existing)
            if question_key in self.existing_data.get('improved_responses', {}):
                skipped_count += 1
                continue
            
            # Add to improved_responses
            if 'improved_responses' not in self.existing_data:
                self.existing_data['improved_responses'] = {}
            
            self.existing_data['improved_responses'][question_key] = {
                'original': training_entry['response'],
                'corrected': training_entry['response'],
                'language': training_entry['language'],
                'response_type': training_entry['response_type'],
                'timestamp': training_entry['timestamp'],
                'source': training_entry['source']
            }
            
            new_responses[question_key] = training_entry
            added_count += 1
        
        print(f"✅ Added {added_count} new responses")
        print(f"⏭️  Skipped {skipped_count} (already exist or invalid)")
        
        # Update statistics
        if 'statistics' not in self.existing_data:
            self.existing_data['statistics'] = {}
        
        total_responses = len(self.existing_data.get('improved_responses', {}))
        self.existing_data['statistics']['total_responses'] = total_responses
        self.existing_data['statistics']['public_dataset_responses'] = added_count
        self.existing_data['statistics']['last_updated'] = datetime.now().isoformat()
        
        return self.existing_data
    
    def save_enhanced_training_data(self, output_file: str = None):
        """
        Save enhanced training data to file
        
        Args:
            output_file: Optional output file path (defaults to enhanced_training_data.json)
        """
        if output_file is None:
            current_dir = os.path.dirname(os.path.abspath(__file__))
            output_file = os.path.join(current_dir, 'data', 'enhanced_training_data.json')
        
        # Ensure directory exists
        os.makedirs(os.path.dirname(output_file), exist_ok=True)
        
        # Save enhanced data
        with open(output_file, 'w', encoding='utf-8') as f:
            json.dump(self.existing_data, f, indent=2, ensure_ascii=False)
        
        print(f"✅ Enhanced training data saved to: {output_file}")
        
        # Also update original training_data.json
        original_file = self.training_data_file
        with open(original_file, 'w', encoding='utf-8') as f:
            json.dump(self.existing_data, f, indent=2, ensure_ascii=False)
        
        print(f"✅ Original training data updated: {original_file}")
    
    def get_statistics(self) -> Dict[str, Any]:
        """Get statistics about integrated data"""
        stats = {
            'total_responses': len(self.existing_data.get('improved_responses', {})),
            'languages': {},
            'response_types': {},
            'sources': {}
        }
        
        for question, response_data in self.existing_data.get('improved_responses', {}).items():
            # Count languages
            lang = response_data.get('language', 'unknown')
            stats['languages'][lang] = stats['languages'].get(lang, 0) + 1
            
            # Count response types
            resp_type = response_data.get('response_type', 'unknown')
            stats['response_types'][resp_type] = stats['response_types'].get(resp_type, 0) + 1
            
            # Count sources
            source = response_data.get('source', 'unknown')
            stats['sources'][source] = stats['sources'].get(source, 0) + 1
        
        return stats


def main():
    """Main function to integrate datasets"""
    print("🚀 Starting dataset integration...")
    print("=" * 60)
    
    integrator = DatasetIntegrator()
    
    # Integrate datasets
    enhanced_data = integrator.integrate_public_datasets()
    
    # Save enhanced data
    integrator.save_enhanced_training_data()
    
    # Print statistics
    stats = integrator.get_statistics()
    print("\n📊 Integration Statistics:")
    print(f"  Total responses: {stats['total_responses']}")
    print(f"  Languages: {stats['languages']}")
    print(f"  Response types: {stats['response_types']}")
    print(f"  Sources: {stats['sources']}")
    
    print("\n✅ Dataset integration complete!")


if __name__ == '__main__':
    main()






