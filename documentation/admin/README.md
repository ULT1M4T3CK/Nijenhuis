# Admin Booking Management System

This admin area provides a comprehensive booking management system for Nijenhuis Botenverhuur, featuring a calendar interface and booking overview.

## Features

- **Password-protected admin access**
- **Interactive calendar view** with booking indicators
- **Booking management** (create, edit, delete bookings)
- **Status tracking** with color-coded indicators:
  - 🟡 **Not Confirmed** (Yellow)
  - 🟠 **Confirmed, Not Paid** (Orange)
  - 🟢 **Confirmed & Paid** (Green)
  - 🔴 **Payment Rejected** (Red)
- **Filtering options** by boat type and status
- **Responsive design** that matches the main website
- **Real-time statistics** (total bookings, pending bookings)

## Access

The admin area is accessible at: `/admin/index.html`

**Default credentials:**
- Username: `admin`
- Password: `nijenhuis2024`

⚠️ **Important:** Change these credentials in production!

## Setup Instructions

### 1. File Structure
Ensure the following files are in place:
```
admin/
├── index.html          # Main admin interface
├── admin-styles.css    # Admin-specific styles
├── admin.js           # Admin functionality
├── booking-handler.php # Backend integration
├── bookings.json      # Booking data storage
└── README.md          # This file
```

### 2. Server Requirements
- PHP 7.4 or higher
- Write permissions for the `admin/` directory
- Email functionality (for booking notifications)

### 3. Configuration

#### Update Admin Credentials
In `admin.js`, change the default credentials:
```javascript
this.adminCredentials = {
    username: 'your-username',
    password: 'your-secure-password'
};
```

In `booking-handler.php`, update the credentials:
```php
$adminCredentials = [
    'username' => 'your-username',
    'password' => 'your-secure-password'
];
```

#### Update Email Settings
In `booking-handler.php`, modify the email notification settings:
```php
$to = 'your-email@domain.com'; // Change to your email
```

### 4. Integration with Main Website

The admin system can integrate with the existing booking form on the main website. To enable this:

1. **Update the booking form** to submit to the admin system
2. **Add customer information fields** to the booking form
3. **Configure the form action** to point to `admin/booking-handler.php`

## Usage Guide

### Login
1. Navigate to `/admin/index.html`
2. Enter your admin credentials
3. Click "Login"

### Calendar View
- **Navigate months** using the arrow buttons
- **View bookings** as colored indicators on calendar days
- **Click on a day** to create a new booking
- **Click on a booking indicator** to edit that booking
- **Filter bookings** using the dropdown menus

### Booking Management
- **Add new booking**: Click "Add New Booking" or click on any calendar day
- **Edit booking**: Click on any booking in the calendar or booking list
- **Update status**: Use the status dropdown in the booking form
- **Add notes**: Use the notes field for additional information

### Booking Statuses
- **Not Confirmed**: New bookings awaiting review
- **Confirmed, Not Paid**: Bookings confirmed but payment pending
- **Confirmed & Paid**: Fully confirmed and paid bookings
- **Payment Rejected**: Bookings with failed payments

## Data Storage

Bookings are stored in `bookings.json` with the following structure:
```json
{
  "id": "unique-booking-id",
  "date": "2024-01-15",
  "boatType": "classic-tender-720",
  "customerName": "John Smith",
  "customerEmail": "john@example.com",
  "customerPhone": "+31 6 12345678",
  "status": "confirmed-paid",
  "notes": "Additional information",
  "createdAt": "2024-01-10T10:00:00Z",
  "updatedAt": "2024-01-10T10:00:00Z"
}
```

## Security Considerations

1. **Change default credentials** immediately after setup
2. **Use HTTPS** in production
3. **Restrict access** to the admin directory
4. **Regular backups** of `bookings.json`
5. **Server-side validation** for all booking data

## Troubleshooting

### Common Issues

**Login not working:**
- Check credentials in both `admin.js` and `booking-handler.php`
- Ensure JavaScript is enabled
- Check browser console for errors

**Bookings not saving:**
- Verify write permissions on the `admin/` directory
- Check PHP error logs
- Ensure `bookings.json` is writable

**Calendar not displaying:**
- Check browser console for JavaScript errors
- Verify all CSS and JS files are loading
- Ensure proper file paths

**Email notifications not working:**
- Check server mail configuration
- Verify email address in `booking-handler.php`
- Check server error logs

### File Permissions
```bash
chmod 755 admin/
chmod 644 admin/*.html
chmod 644 admin/*.css
chmod 644 admin/*.js
chmod 644 admin/*.php
chmod 666 admin/bookings.json
```

## Support

For technical support or questions about the admin system, please contact your web developer or system administrator.

## Updates

This admin system is designed to be easily maintainable and extensible. Future updates may include:
- Export functionality (PDF, Excel)
- Advanced reporting
- Customer management
- Payment integration
- Multi-user support 