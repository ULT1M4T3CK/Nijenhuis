# Admin System - Nijenhuis Botenverhuur

## Overview

The admin system provides booking and boat management for Nijenhuis Botenverhuur.

## Pages

| Page | Description |
|------|-------------|
| `admin-static.php` | Dashboard overview |
| `boat-management.php` | Manage boats (add, edit, delete) |
| `boat-edit.php` | Edit individual boat details |
| `booking-management.php` | Calendar view of bookings |
| `booking-history.php` | Historical booking archive |

## Access

Login at: `/pages/admin-login.php`

Credentials are configured via environment variables:
- `ADMIN_USERNAME`
- `ADMIN_PASSWORD`

## Features

- **Boat Management**: Add, edit, delete boats with images and pricing
- **Booking Calendar**: 3-month calendar view with booking indicators
- **Status Management**: Track booking statuses (confirmed, paid, rejected)
- **Session Security**: 24-hour sessions with 15-minute inactivity timeout

## Backend

- `booking-handler.php` - API for bookings and authentication
- `boats.json` - Boat data storage
- `bookings.json` - Booking data storage

## Booking Statuses

| Status | Color | Description |
|--------|-------|-------------|
| `not-confirmed` | Yellow | New booking pending review |
| `confirmed-not-paid` | Orange | Confirmed, awaiting payment |
| `confirmed-paid` | Green | Fully confirmed and paid |
| `payment-rejected` | Red | Payment failed |
| `manual` | Blue | Manually added booking |
