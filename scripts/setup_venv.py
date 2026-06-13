#!/usr/bin/env python3
"""
Setup Virtual Environment for Nijenhuis Chatbot
Creates and configures a Python virtual environment
"""

import os
import sys
import subprocess
import venv
from pathlib import Path

def run_command(cmd, check=True):
    """Run a shell command"""
    print(f"Running: {' '.join(cmd)}")
    result = subprocess.run(cmd, check=check, capture_output=True, text=True)
    if result.stdout:
        print(result.stdout)
    if result.stderr and result.returncode != 0:
        print(result.stderr, file=sys.stderr)
    return result.returncode == 0

def check_venv_exists():
    """Check if venv already exists"""
    venv_path = Path('venv')
    if venv_path.exists():
        response = input("⚠️  Virtual environment already exists. Recreate it? (y/N): ")
        if response.lower() == 'y':
            import shutil
            shutil.rmtree(venv_path)
            return False
        else:
            print("Using existing virtual environment.")
            return True
    return False

def create_venv():
    """Create virtual environment"""
    print("=" * 60)
    print("Setting up Python Virtual Environment")
    print("=" * 60)
    
    if check_venv_exists():
        print("\nTo activate it, run:")
        print("  source venv/bin/activate")
        return True
    
    print("\nCreating virtual environment...")
    venv.create('venv', with_pip=True)
    
    # Determine pip path
    if sys.platform == 'win32':
        pip_path = Path('venv') / 'Scripts' / 'pip'
        python_path = Path('venv') / 'Scripts' / 'python'
    else:
        pip_path = Path('venv') / 'bin' / 'pip'
        python_path = Path('venv') / 'bin' / 'python'
    
    print("Upgrading pip...")
    run_command([str(python_path), '-m', 'pip', 'install', '--upgrade', 'pip'])
    
    print("\nInstalling basic requirements...")
    if Path('requirements.txt').exists():
        run_command([str(pip_path), 'install', '-r', 'requirements.txt'])
    else:
        print("⚠️  requirements.txt not found, installing basic packages...")
        run_command([str(pip_path), 'install', 'numpy>=1.21.0', 'flask>=2.0.0', 
                    'flask-cors>=3.0.0', 'requests>=2.25.0'])
    
    # Ask about enhanced features
    response = input("\nInstall enhanced NLP features? (y/N): ")
    if response.lower() == 'y':
        print("Installing enhanced requirements...")
        if Path('requirements_enhanced.txt').exists():
            run_command([str(pip_path), 'install', '-r', 'requirements_enhanced.txt'])
        else:
            print("⚠️  requirements_enhanced.txt not found")
        
        # Download SpaCy models
        try:
            result = run_command([str(python_path), '-c', 'import spacy'], check=False)
            if result:
                print("\nDownloading SpaCy language models...")
                run_command([str(python_path), '-m', 'spacy', 'download', 'en_core_web_sm'], check=False)
                run_command([str(python_path), '-m', 'spacy', 'download', 'nl_core_news_sm'], check=False)
        except:
            pass
    
    print("\n" + "=" * 60)
    print("✅ Virtual environment setup complete!")
    print("=" * 60)
    print("\nTo activate the virtual environment, run:")
    if sys.platform == 'win32':
        print("  venv\\Scripts\\activate")
    else:
        print("  source venv/bin/activate")
    print("\nTo verify installation, run:")
    print("  python3 backend/chatbot/scripts/check_dependencies.py")
    
    return True

if __name__ == "__main__":
    try:
        create_venv()
    except KeyboardInterrupt:
        print("\n\nSetup cancelled by user.")
        sys.exit(1)
    except Exception as e:
        print(f"\n❌ Error setting up virtual environment: {e}")
        import traceback
        traceback.print_exc()
        sys.exit(1)

