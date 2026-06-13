#!/usr/bin/env python3
"""
Run All P1 Fixes
Master script to execute all P1 priority fixes in sequence
"""

import sys
import subprocess
from pathlib import Path

# Add project root to path
project_root = Path(__file__).parent.parent.parent.parent
sys.path.insert(0, str(project_root))

def run_script(script_path: str, description: str) -> bool:
    """Run a Python script and return success status"""
    print("\n" + "=" * 60)
    print(f"Running: {description}")
    print("=" * 60)
    
    try:
        result = subprocess.run(
            [sys.executable, str(script_path)],
            cwd=str(project_root),
            check=True,
            capture_output=False
        )
        print(f"✅ {description} completed successfully")
        return True
    except subprocess.CalledProcessError as e:
        print(f"❌ {description} failed with exit code {e.returncode}")
        return False
    except Exception as e:
        print(f"❌ Error running {description}: {e}")
        return False

def check_dependencies():
    """Check if required dependencies are installed"""
    try:
        import numpy
        return True
    except ImportError:
        print("\n" + "=" * 60)
        print("❌ Missing Required Dependencies")
        print("=" * 60)
        print("\nNumPy is required but not installed.")
        print("\nTo install dependencies, run:")
        print("   pip install numpy>=1.21.0")
        print("\nOr install all requirements:")
        print("   pip install -r requirements.txt")
        print("\nFor enhanced features:")
        print("   pip install -r requirements_enhanced.txt")
        print("\n" + "=" * 60)
        return False

def main():
    """Run all P1 fixes in sequence"""
    print("=" * 60)
    print("P1 Priority Fixes - Master Script")
    print("=" * 60)
    
    # Check dependencies first
    if not check_dependencies():
        print("\n⚠️ Please install required dependencies before running P1 fixes.")
        print("   Run: python3 backend/chatbot/scripts/check_dependencies.py")
        return 1
    
    scripts = [
        (
            project_root / 'backend' / 'chatbot' / 'scripts' / 'check_dependencies.py',
            "Check Dependencies"
        ),
        (
            project_root / 'backend' / 'chatbot' / 'scripts' / 'clean_bitext_dataset.py',
            "Clean Bitext Dataset (P1-1)"
        ),
        (
            project_root / 'backend' / 'chatbot' / 'scripts' / 'integrate_external_datasets.py',
            "Integrate External Datasets (P1-3)"
        ),
        (
            project_root / 'backend' / 'chatbot' / 'scripts' / 'verify_dataset_integration.py',
            "Verify Dataset Integration"
        ),
        (
            project_root / 'backend' / 'chatbot' / 'scripts' / 'generate_baseline_metrics.py',
            "Generate Baseline Metrics (P1-4)"
        )
    ]
    
    results = {}
    
    for script_path, description in scripts:
        if not script_path.exists():
            print(f"⚠️ Script not found: {script_path}")
            results[description] = False
            continue
        
        success = run_script(script_path, description)
        results[description] = success
        
        if not success:
            print(f"\n⚠️ Warning: {description} failed. Continuing with next step...")
    
    # Summary
    print("\n" + "=" * 60)
    print("P1 Fixes Summary")
    print("=" * 60)
    
    all_passed = all(results.values())
    
    for description, success in results.items():
        status = "✅" if success else "❌"
        print(f"{status} {description}")
    
    if all_passed:
        print("\n✅ All P1 fixes completed successfully!")
        print("\n📝 Next Steps:")
        print("   1. Review baseline metrics in backend/chatbot/training/evaluations/")
        print("   2. Expand training data if needed (target: 500+ samples)")
        print("   3. Run chatbot and verify improved performance")
        return 0
    else:
        print("\n⚠️ Some fixes failed. Please review errors above.")
        return 1

if __name__ == "__main__":
    sys.exit(main())

