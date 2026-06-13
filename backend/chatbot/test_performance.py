#!/usr/bin/env python3
"""
Performance test for AlBot optimizations
Target: <2 second response time
"""

import sys
import os
import time

# Add paths
sys.path.insert(0, os.path.join(os.path.dirname(__file__), '..', '..'))

def test_chatbot_performance():
    """Test chatbot initialization and response times"""
    print("=" * 60)
    print("AlBot Performance Test")
    print("=" * 60)
    
    # Test initialization time with detailed breakdown
    print("\n1. Testing Chatbot Initialization...")
    
    # Time the import phase
    import_start = time.time()
    from backend.chatbot.core.chatbot import Chatbot
    import_time = time.time() - import_start
    print(f"   📦 Import time: {import_time:.2f}s")
    
    # Time the instantiation phase
    init_start = time.time()
    chatbot = Chatbot(use_advanced_nlp=False)
    init_time = time.time() - init_start
    print(f"   🔧 Instantiation time: {init_time:.2f}s")
    
    total_time = import_time + init_time
    print(f"   ✅ Total initialization time: {total_time:.2f}s")
    
    # Test queries
    test_queries = [
        ("Hallo", "nl"),
        ("What are your prices?", "en"),
        ("Wat kost een electrosloep?", "nl"),
        ("Opening hours please", "en"),
        ("Hoeveel personen passen er op de tender 720?", "nl"),
        ("Kann ich einen Kajak mieten?", "de"),
        ("Waar zijn jullie gevestigd?", "nl"),
        ("Do you allow pets on boats?", "en"),
    ]
    
    print(f"\n2. Testing Query Response Times ({len(test_queries)} queries)...")
    total_time = 0
    slow_queries = []
    
    for query, expected_lang in test_queries:
        start = time.time()
        result = chatbot.process_query(query, use_token_prediction=False)
        query_time = time.time() - start
        total_time += query_time
        
        status = "✅" if query_time < 2.0 else "⚠️"
        if query_time >= 2.0:
            slow_queries.append((query, query_time))
        
        print(f"   {status} '{query[:40]}...' → {query_time*1000:.1f}ms (lang: {result['detected_language']})")
    
    avg_time = total_time / len(test_queries)
    
    print("\n" + "=" * 60)
    print("Summary:")
    print(f"   📊 Average response time: {avg_time*1000:.1f}ms")
    print(f"   📊 Startup time (one-time): {total_time:.2f}s")
    print(f"   📊 Total queries: {len(test_queries)}")
    print(f"   📊 Slow queries (>2s): {len(slow_queries)}")
    
    if avg_time < 0.5:
        print(f"\n   ✅ EXCELLENT! Average response time under 500ms")
        print(f"   💡 Target of <2s response time achieved (actual: {avg_time*1000:.1f}ms)")
    elif avg_time < 1.0:
        print(f"\n   ✅ GOOD! Average response time under 1 second")
    elif avg_time < 2.0:
        print(f"\n   ✅ OK! Average response time under 2 seconds")
    else:
        print(f"\n   ⚠️ NEEDS IMPROVEMENT! Average response time exceeds 2 seconds")
        for query, time_taken in slow_queries:
            print(f"      - '{query}': {time_taken:.2f}s")
    
    print("=" * 60)
    
    return avg_time < 2.0

if __name__ == "__main__":
    success = test_chatbot_performance()
    sys.exit(0 if success else 1)

