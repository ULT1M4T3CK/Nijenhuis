#!/usr/bin/env python3
"""
Run Enhanced Training Framework
This script downloads external datasets and enhances the chatbot training data
"""

import os
import sys
import json
from pathlib import Path

# Add the project root to the Python path
project_root = Path(__file__).parent.parent.parent.parent
sys.path.insert(0, str(project_root))

from backend.chatbot.training.enhanced_training_framework import EnhancedTrainingFramework

def main():
    """Run the enhanced training framework"""
    print("🚀 Nijenhuis Enhanced Chatbot Training")
    print("=" * 50)
    
    # Check if we're in the right directory
    current_dir = Path.cwd()
    print(f"📁 Current directory: {current_dir}")
    
    # Initialize the enhanced training framework
    framework = EnhancedTrainingFramework()
    
    print("\n📥 Step 1: Downloading external datasets...")
    external_datasets = framework.download_external_datasets()
    
    if external_datasets:
        print(f"✅ Downloaded {len(external_datasets)} external datasets")
        for name, data in external_datasets.items():
            print(f"   • {name}: {data.get('total_samples', 0)} samples")
    else:
        print("⚠️ No external datasets downloaded (libraries may not be available)")
    
    print("\n🔄 Step 2: Enhancing training data...")
    enhanced_data = framework.enhance_training_data()
    
    print(f"✅ Enhanced training data with {len(enhanced_data.get('improved_responses', {}))} responses")
    
    print("\n🧠 Step 3: Generating advanced features...")
    advanced_features = framework.generate_advanced_features()
    
    print(f"✅ Generated advanced features for {len(advanced_features)} samples")
    
    print("\n💾 Step 4: Creating comprehensive training data...")
    output_file = framework.create_enhanced_training_data()
    
    print(f"✅ Created enhanced training data: {output_file}")
    
    print("\n📊 Step 5: Generating enhancement report...")
    report = framework.get_enhancement_report()
    
    print("\n" + "=" * 50)
    print("📈 ENHANCEMENT REPORT")
    print("=" * 50)
    
    summary = report['enhancement_summary']
    print(f"Original responses: {summary['original_responses']}")
    print(f"Enhanced responses: {summary['enhanced_responses']}")
    print(f"Improvement factor: {summary['improvement_factor']:.1f}x")
    print(f"External datasets used: {summary['external_datasets_used']}")
    print(f"Languages supported: {', '.join(summary['languages_supported'])}")
    
    print("\n🔧 NLP Capabilities:")
    capabilities = summary['nlp_capabilities']
    print(f"  • Advanced features: {'✅' if capabilities['advanced_features'] else '❌'}")
    print(f"  • Semantic analysis: {'✅' if capabilities['semantic_analysis'] else '❌'}")
    print(f"  • Linguistic analysis: {'✅' if capabilities['linguistic_analysis'] else '❌'}")
    print(f"  • External datasets: {'✅' if capabilities['external_datasets'] else '❌'}")
    
    print("\n💡 Recommendations:")
    for i, rec in enumerate(report['recommendations'], 1):
        print(f"  {i}. {rec}")
    
    print("\n" + "=" * 50)
    print("🎯 ENHANCEMENT COMPLETE!")
    print("=" * 50)
    print(f"📁 Enhanced training data saved to: {output_file}")
    print("🤖 Your chatbot is now significantly more capable!")
    print("\nNext steps:")
    print("1. Install recommended libraries for full functionality")
    print("2. Test the enhanced chatbot with the new training data")
    print("3. Consider fine-tuning a multilingual model for even better performance")

if __name__ == "__main__":
    main()
