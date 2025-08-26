# Mollie Payment Integration Guide

## Overview

This guide explains how the Mollie payment system is integrated into the Nijenhuis Botenverhuur booking system. The integration automatically updates booking statuses when payments are processed or declined.

## 🏗️ Architecture

### Components

1. **Frontend Payment System** (`js/mollie-payment.js`)
   - Handles payment creation and redirection
   - Manages payment status updates
   - Integrates with booking system

2. **Booking System Integration** (`js/booking-system-simple.js`)
   - Creates payments when bookings are submitted
   - Shows payment processing states
   - Handles payment errors

3. **Admin System Integration** (`admin/admin-simple.html`)
   - Listens for payment status updates
   - Automatically updates booking statuses
   - Shows payment information in admin dashboard

4. **Webhook Handler** (`backend/webhooks/mollie/webhook_handler.py`)
   - Receives payment status updates from Mollie
   - Updates booking statuses automatically
   - Logs all payment events

5. **Payment Success Page** (`pages/payment-success.html`)
   - Confirms successful payments
   - Shows booking details
   - Updates booking status

## 🔧 Setup Instructions

### 1. API Key Configuration

The system uses the test API key: `test_sHQfqTngBbCpEfMyMCPGH92gnm8P7m`

**For Production:**
- Replace the API key in `js/mollie-payment.js`
- Update webhook URLs to your production domain
- Configure proper SSL certificates

### 2. Webhook Server Setup

**Start the webhook server:**
```bash
python backend/webhooks/mollie/webhook_handler.py
```

**Default webhook URL:** `http://localhost:8080/webhook/mollie`

### 3. Testing the Integration

**Run test scenarios:**
```bash
python test_mollie_webhook.py
```

**Test specific payment:**
```bash
python test_mollie_webhook.py tr_test123 paid
```

## 💳 Payment Flow

### 1. Booking Submission
1. User fills out booking form
2. System checks boat availability
3. Booking is saved with status "not-confirmed"
4. Mollie payment is created
5. User is redirected to Mollie payment page

### 2. Payment Processing
1. User completes payment on Mollie
2. Mollie sends webhook to our server
3. Webhook handler updates booking status
4. Admin system is notified of status change
5. User is redirected to success page

### 3. Status Updates
- **paid** → `confirmed-paid` (Green)
- **failed/expired/canceled** → `payment-rejected` (Red)
- **pending** → `confirmed-not-paid` (Orange)

## 🎯 Testing Scenarios

### Local Development Testing

1. **Start webhook server:**
   ```bash
   python backend/webhooks/mollie/webhook_handler.py
   ```

2. **Make a test booking:**
   - Go to the booking form
   - Fill out details
   - Submit booking
   - Check payment processing screen

3. **Simulate webhook:**
   ```bash
   python test_mollie_webhook.py tr_test123 paid
   ```

4. **Check admin dashboard:**
   - Verify booking status updated
   - Check payment information
   - Confirm boat availability updated

### Payment Status Testing

| Status | Expected Result | Admin Status |
|--------|----------------|--------------|
| `paid` | ✅ Success | `confirmed-paid` |
| `failed` | ❌ Failed | `payment-rejected` |
| `expired` | ❌ Expired | `payment-rejected` |
| `canceled` | ❌ Canceled | `payment-rejected` |
| `pending` | ⏳ Pending | `confirmed-not-paid` |

## 🔍 Monitoring & Logging

### Log Files

- **`payment_webhooks.log`** - All webhook events
- **`local_bookings.json`** - Local booking data (for testing)

### Console Logs

The system logs all payment events to the console:
- Payment creation
- Webhook reception
- Status updates
- Error messages

## 🚨 Troubleshooting

### Common Issues

1. **Webhook server not running:**
   ```
   Error: Could not connect to webhook server
   Solution: Start backend/webhooks/mollie/webhook_handler.py
   ```

2. **Payment creation fails:**
   ```
   Error: HTTP error! status: 401
   Solution: Check API key configuration
   ```

3. **Booking status not updating:**
   ```
   Error: Booking not found
   Solution: Check payment ID mapping
   ```

4. **CORS issues:**
   ```
   Error: CORS policy violation
   Solution: Configure proper CORS headers
   ```

### Debug Mode

Enable debug logging by adding to browser console:
```javascript
localStorage.setItem('debug_mollie', 'true');
```

## 🔒 Security Considerations

### Production Checklist

- [ ] Use production API key
- [ ] Enable SSL/TLS
- [ ] Validate webhook signatures
- [ ] Implement rate limiting
- [ ] Add error monitoring
- [ ] Secure webhook endpoint

### Webhook Security

The webhook handler should validate:
- Request origin (Mollie IPs)
- Webhook signature
- Request timestamp
- Payment ID format

## 📊 Integration Points

### Frontend Integration

- **Booking Form** → Creates payment
- **Admin Dashboard** → Shows payment status
- **Success Page** → Confirms payment
- **Error Handling** → Shows payment errors

### Backend Integration

- **Webhook Handler** → Receives status updates
- **Local Storage** → Stores booking data
- **Logging** → Tracks all events
- **Status Mapping** → Converts Mollie statuses

## 🚀 Deployment

### Local Development

1. Start webhook server
2. Open website in browser
3. Test booking flow
4. Monitor logs

### Production Deployment

1. Update API keys
2. Configure webhook URLs
3. Set up SSL certificates
4. Deploy webhook handler
5. Test payment flow
6. Monitor webhook logs

## 📝 API Reference

### Mollie API Endpoints

- **Create Payment:** `POST /v2/payments`
- **Get Payment:** `GET /v2/payments/{id}`
- **Webhook:** `POST /webhook/mollie`

### Webhook Payload

```json
{
  "id": "tr_test123",
  "status": "paid",
  "amount": {
    "currency": "EUR",
    "value": "85.00"
  },
  "metadata": {
    "booking_id": "booking_123",
    "customer_email": "user@example.com"
  }
}
```

## 🤝 Support

For issues with the Mollie integration:

1. Check the logs (`payment_webhooks.log`)
2. Verify API key configuration
3. Test webhook connectivity
4. Review payment flow
5. Check admin dashboard status

---

**Last Updated:** January 2024
**Version:** 1.0.0 