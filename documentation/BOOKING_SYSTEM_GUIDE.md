# Booking System

## Overview

The booking system allows customers to reserve boats online with integrated payment processing.

## Booking Flow

1. **Select Boat & Date** - Customer chooses boat type and rental dates
2. **Check Availability** - System verifies boat is available
3. **Enter Details** - Customer provides contact information
4. **Payment** - Redirected to Mollie for secure payment
5. **Confirmation** - Booking confirmed upon successful payment

## Features

- **Real-time Availability** - Prevents double bookings
- **Multi-day Rentals** - Support for 1-7 day rentals
- **Dynamic Pricing** - Per-day or multi-day pricing tiers
- **Deposit Tracking** - Configurable deposit per boat
- **Engine Option** - Optional motor upgrade for sailboats

## Booking Statuses

| Status | Description |
|--------|-------------|
| `not-confirmed` | New booking, awaiting payment |
| `confirmed-not-paid` | Confirmed, payment pending |
| `confirmed-paid` | Fully confirmed and paid |
| `payment-rejected` | Payment failed or canceled |
| `manual` | Manually added by admin |

## Data Storage

Bookings are stored in `admin/bookings.json`:
```json
{
  "id": "booking_123",
  "date": "2025-06-15",
  "boatType": "classic-tender-720",
  "numberOfDays": 2,
  "customerName": "Jan Jansen",
  "customerEmail": "jan@example.nl",
  "customerPhone": "+31612345678",
  "status": "confirmed-paid",
  "createdAt": "2025-01-15T10:00:00Z"
}
```

## Season

Boat rentals are available April 1 - October 31 each year.
