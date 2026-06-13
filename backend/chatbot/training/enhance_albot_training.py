#!/usr/bin/env python3
"""
Main script to enhance AlBot training with publicly available datasets
and improve website understanding

Run this script to:
1. Download publicly available chatbot datasets
2. Integrate them with existing training data
3. Improve website content extraction
4. Create enhanced training data for AlBot
"""

import os
import sys
from pathlib import Path

# Add parent directory to path
current_dir = os.path.dirname(os.path.abspath(__file__))
sys.path.insert(0, current_dir)

from download_public_datasets import PublicDatasetDownloader
from integrate_datasets import DatasetIntegrator


def main():
    """Main function to enhance AlBot training"""
    print("=" * 70)
    print("🤖 AlBot Training Enhancement")
    print("=" * 70)
    print()
    
    # Step 1: Download public datasets
    print("📥 Step 1: Downloading publicly available datasets...")
    print("-" * 70)
    downloader = PublicDatasetDownloader()
    datasets = downloader.download_all_datasets()
    print()
    
    # Step 2: Integrate datasets with existing training data
    print("🔄 Step 2: Integrating datasets with existing training data...")
    print("-" * 70)
    integrator = DatasetIntegrator()
    enhanced_data = integrator.integrate_public_datasets()
    print()
    
    # Step 3: Save enhanced training data
    print("💾 Step 3: Saving enhanced training data...")
    print("-" * 70)
    integrator.save_enhanced_training_data()
    print()
    
    # Step 4: Show statistics
    print("📊 Step 4: Training Data Statistics")
    print("-" * 70)
    stats = integrator.get_statistics()
    
    print(f"Total training responses: {stats['total_responses']}")
    print()
    
    print("Languages:")
    for lang, count in sorted(stats['languages'].items()):
        print(f"  - {lang}: {count} responses")
    print()
    
    print("Response types:")
    for resp_type, count in sorted(stats['response_types'].items()):
        print(f"  - {resp_type}: {count} responses")
    print()
    
    print("Data sources:")
    for source, count in sorted(stats['sources'].items()):
        print(f"  - {source}: {count} responses")
    print()
    
    # Step 5: Test website content extraction
    print("🌐 Step 5: Testing website content extraction...")
    print("-" * 70)
    try:
        # Add project root to path
        project_root = Path(current_dir).parent.parent.parent.parent
        sys.path.insert(0, str(project_root))
        
        from backend.chatbot.core.website_content_extractor import WebsiteContentExtractor
        
        extractor = WebsiteContentExtractor()
        content = extractor.extract_all_content(force_refresh=True)
        
        if content:
            content_length = len(content)
            print(f"✅ Successfully extracted {content_length:,} characters of website content")
            print(f"   Content covers {len(content.split('===')) - 1} pages")
            
            # Show sample
            sample = content[:500].replace('\n', ' ')
            print(f"   Sample: {sample}...")
        else:
            print("⚠️ No website content extracted")
    except Exception as e:
        print(f"⚠️ Error testing website extraction: {e}")
        print("   (This is non-critical - website extraction works at runtime)")
    
    print()
    print("=" * 70)
    print("✅ AlBot training enhancement complete!")
    print("=" * 70)
    print()
    print("Next steps:")
    print("1. Restart the chatbot server to load enhanced training data")
    print("2. Test the chatbot with various questions")
    print("3. Monitor chatbot performance and accuracy")
    print()
    print("To restart the server:")
    print("  cd backend/chatbot/api")
    print("  python server.py")
    print()


if __name__ == '__main__':
    main()

