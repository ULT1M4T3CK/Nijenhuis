#!/usr/bin/env python3
"""
Test script for Mollie webhook simulation
This script simulates Mollie webhook calls for local development
"""

import requests
import json
import time
import sys

def simulate_webhook(payment_id, status, webhook_url="http://localhost:8080/webhook/mollie"):
    """Simulate a Mollie webhook call"""
    
    # Simulate webhook payload
    webhook_data = {
        "id": payment_id,
        "status": status,
        "amount": {
            "currency": "EUR",
            "value": "85.00"
        },
        "description": "Test payment",
        "metadata": {
            "booking_id": f"booking_{int(time.time())}",
            "customer_email": "test@example.com"
        },
        "createdAt": "2024-01-15T10:00:00+00:00",
        "updatedAt": "2024-01-15T10:05:00+00:00"
    }
    
    try:
        response = requests.post(
            webhook_url,
            json=webhook_data,
            headers={'Content-Type': 'application/json'}
        )
        
        print(f"Webhook simulation for payment {payment_id} with status '{status}':")
        print(f"Status code: {response.status_code}")
        print(f"Response: {response.text}")
        
        if response.status_code == 200:
            print("✅ Webhook simulation successful!")
        else:
            print("❌ Webhook simulation failed!")
            
    except requests.exceptions.ConnectionError:
        print(f"❌ Could not connect to webhook server at {webhook_url}")
        print("Make sure the webhook server is running with: python webhook_handler.py")
    except Exception as e:
        print(f"❌ Error simulating webhook: {str(e)}")

def test_payment_scenarios():
    """Test different payment scenarios"""
    
    print("🧪 Testing Mollie Webhook Scenarios")
    print("=" * 50)
    
    # Test successful payment
    print("\n1. Testing successful payment...")
    simulate_webhook("tr_test123", "paid")
    
    time.sleep(2)
    
    # Test failed payment
    print("\n2. Testing failed payment...")
    simulate_webhook("tr_test456", "failed")
    
    time.sleep(2)
    
    # Test expired payment
    print("\n3. Testing expired payment...")
    simulate_webhook("tr_test789", "expired")
    
    time.sleep(2)
    
    # Test canceled payment
    print("\n4. Testing canceled payment...")
    simulate_webhook("tr_test101", "canceled")
    
    time.sleep(2)
    
    # Test pending payment
    print("\n5. Testing pending payment...")
    simulate_webhook("tr_test202", "pending")

def test_specific_payment(payment_id, status):
    """Test a specific payment scenario"""
    print(f"🧪 Testing specific payment: {payment_id} with status '{status}'")
    print("=" * 50)
    simulate_webhook(payment_id, status)

if __name__ == '__main__':
    if len(sys.argv) == 3:
        # Test specific payment: python test_mollie_webhook.py payment_id status
        payment_id = sys.argv[1]
        status = sys.argv[2]
        test_specific_payment(payment_id, status)
    else:
        # Run all test scenarios
        test_payment_scenarios()
    
    print("\n" + "=" * 50)
    print("🎯 Webhook testing completed!")
    print("Check the webhook server logs and payment_webhooks.log for results.") 