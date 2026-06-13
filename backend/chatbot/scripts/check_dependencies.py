#!/usr/bin/env python3
"""
Check Required Dependencies
Verifies that all required Python packages are installed
"""

import sys
from typing import Dict, List, Tuple

def check_package(package_name: str, import_name: str = None, min_version: str = None) -> Tuple[bool, str]:
    """
    Check if a package is installed
    
    Args:
        package_name: Package name (for pip)
        import_name: Import name (if different from package name)
        min_version: Minimum version requirement
        
    Returns:
        Tuple of (is_installed, version_or_error)
    """
    if import_name is None:
        import_name = package_name
    
    try:
        module = __import__(import_name)
        version = getattr(module, '__version__', 'unknown')
        
        if min_version and version != 'unknown':
            # Simple version comparison (basic check)
            try:
                from packaging import version
                if version.parse(version) < version.parse(min_version):
                    return False, f"installed (v{version}), but need >= {min_version}"
            except:
                pass  # If packaging not available, skip version check
        
        return True, version
    except ImportError:
        return False, "not installed"

def check_all_dependencies() -> Dict[str, Tuple[bool, str]]:
    """Check all required dependencies"""
    dependencies = {
        'numpy': ('numpy', 'numpy', '1.21.0'),
        'scikit-learn': ('scikit-learn', 'sklearn', '1.0.0'),
        'transformers': ('transformers', 'transformers', None),
        'sentence-transformers': ('sentence-transformers', 'sentence_transformers', None),
        'torch': ('torch', 'torch', None),
        'spacy': ('spacy', 'spacy', None),
        'flask': ('flask', 'flask', '2.0.0'),
        'flask-cors': ('flask-cors', 'flask_cors', '3.0.0'),
        'requests': ('requests', 'requests', '2.25.0'),
    }
    
    results = {}
    for package_name, (pip_name, import_name, min_version) in dependencies.items():
        results[package_name] = check_package(pip_name, import_name, min_version)
    
    return results

def print_dependency_report(results: Dict[str, Tuple[bool, str]]):
    """Print dependency check report"""
    print("=" * 60)
    print("Dependency Check Report")
    print("=" * 60)
    
    required = ['numpy', 'flask', 'flask-cors', 'requests']
    optional = ['scikit-learn', 'transformers', 'sentence-transformers', 'torch', 'spacy']
    
    print("\n📦 Required Dependencies:")
    all_required_ok = True
    for package in required:
        is_installed, version = results.get(package, (False, "not checked"))
        status = "✅" if is_installed else "❌"
        print(f"   {status} {package}: {version}")
        if not is_installed:
            all_required_ok = False
    
    print("\n📦 Optional Dependencies (for advanced features):")
    for package in optional:
        is_installed, version = results.get(package, (False, "not checked"))
        status = "✅" if is_installed else "⚠️"
        print(f"   {status} {package}: {version}")
    
    return all_required_ok

def get_installation_command(missing_packages: List[str], use_enhanced: bool = False) -> str:
    """Generate installation command for missing packages"""
    if use_enhanced:
        return "pip install -r requirements_enhanced.txt"
    else:
        base_packages = ['numpy', 'flask', 'flask-cors', 'requests']
        missing_base = [p for p in missing_packages if p in base_packages]
        if missing_base:
            return f"pip install {' '.join(missing_base)}"
        return "pip install -r requirements.txt"

def main():
    """Main function"""
    print("\n🔍 Checking dependencies...\n")
    
    results = check_all_dependencies()
    all_required_ok = print_dependency_report(results)
    
    # Find missing packages
    missing_required = [
        package for package, (is_installed, _) in results.items()
        if not is_installed and package in ['numpy', 'flask', 'flask-cors', 'requests']
    ]
    
    if missing_required:
        print("\n" + "=" * 60)
        print("❌ Missing Required Dependencies")
        print("=" * 60)
        print("\nTo install missing packages, run:")
        print(f"   {get_installation_command(missing_required)}")
        print("\nOr for all enhanced features:")
        print("   pip install -r requirements_enhanced.txt")
        print("\nOr install individually:")
        for package in missing_required:
            print(f"   pip install {package}")
        return 1
    
    # Check optional packages
    missing_optional = [
        package for package, (is_installed, _) in results.items()
        if not is_installed and package in ['scikit-learn', 'transformers', 'sentence-transformers', 'torch', 'spacy']
    ]
    
    if missing_optional:
        print("\n" + "=" * 60)
        print("⚠️ Missing Optional Dependencies")
        print("=" * 60)
        print("\nSome advanced features may not be available.")
        print("To install all optional dependencies:")
        print("   pip install -r requirements_enhanced.txt")
    else:
        print("\n" + "=" * 60)
        print("✅ All dependencies installed!")
        print("=" * 60)
    
    return 0 if all_required_ok else 1

if __name__ == "__main__":
    sys.exit(main())

