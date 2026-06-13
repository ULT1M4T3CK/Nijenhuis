# Mollie Payment Integration

## Overview

Payments are processed through Mollie, a European payment service provider supporting iDEAL, credit cards, and other payment methods.

## Payment Flow

1. **Booking Submitted** → Payment created via Mollie API
2. **Customer Redirected** → Mollie hosted payment page
3. **Payment Completed** → Webhook updates booking status
4. **Success/Failure** → Customer redirected to result page

## Components

| Component | Location | Purpose |
|-----------|----------|---------|
| Payment Creation | `js/mollie-payment.js` | Creates Mollie payments |
| Webhook Handler | `backend/webhooks/mollie/` | Receives payment status updates |
| Success Page | `pages/payment-success.php` | Confirms successful payment |
| Failure Page | `pages/payment-failure.php` | Handles failed payments |

## Status Mapping

| Mollie Status | Booking Status |
|---------------|----------------|
| `paid` | `confirmed-paid` |
| `pending` | `confirmed-not-paid` |
| `failed` | `payment-rejected` |
| `expired` | `payment-rejected` |
| `canceled` | `payment-rejected` |

## Configuration

Set the Mollie API key in environment:
```
MOLLIE_API_KEY=live_xxxxxxxxxx
```

## Webhook

The webhook endpoint receives payment status updates from Mollie:
- **Python**: `backend/webhooks/mollie/webhook_handler_production.py`
- **PHP**: `backend/webhooks/mollie/webhook_handler_plesk.php`

Webhook URL format: `https://yourdomain.com/webhook/mollie`
