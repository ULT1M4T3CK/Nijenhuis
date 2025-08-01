# Booking System Guide - No Server Required

## 🎯 Simple Setup (Recommended for Local Testing)

This booking system works entirely in your browser using local storage. **No server setup required!**

### How to Use:

1. **Open the main website:**
   - Open `pages/index.html` in your browser
   - Or use any local web server (like Live Server in VS Code)

2. **Test the booking form:**
   - Fill out the booking form on the main page
   - Click "Beschikbaarheid controleren" (Check Availability)
   - Fill in customer details in the popup
   - Submit the booking

3. **Access admin area:**
   - Click the "Admin" link in the footer of any page
   - Login with credentials:
     - Username: `admin` / Password: `nijenhuis2025`
     - Username: `manager` / Password: `boats2025`
   - All bookings will be displayed with management options
   - You can update status, delete bookings, and export data

### Features:

✅ **No server required** - Works entirely in browser  
✅ **Local storage** - Bookings saved in your browser  
✅ **Availability checking** - Prevents double bookings  
✅ **Admin management** - View, update, and delete bookings  
✅ **Data export** - Export bookings as JSON file  
✅ **Status management** - Track booking states  
✅ **Filtering** - Filter by boat type, status, and date  

### File Structure:

```
📁 Your Project
├── 📄 pages/index.html (Main website with booking form)
├── 📄 pages/admin-login.html (Admin login page)
├── 📄 js/booking-system-simple.js (Simple booking system)
├── 📄 admin/admin-simple.html (Admin dashboard)
└── 📄 js/booking-system.css (Modal styling)
```

### Booking Statuses:

- 🔄 **Not Confirmed** - New booking submitted
- ✅ **Confirmed, Not Paid** - Booking confirmed but payment pending
- 💰 **Confirmed & Paid** - Booking confirmed and paid
- ❌ **Payment Rejected** - Payment was rejected

### Data Management:

- **Export**: Click "Export Bookings" to download JSON file
- **Clear All**: Click "Clear All Bookings" to reset
- **Refresh**: Click "Refresh" to reload data

---

## 🚀 Advanced Setup (With Server)

If you want to use the full server-based system:

### Python Server (Local Development):
```bash
python3 start-booking-server.py
```

### PHP Server (Production):
- Requires PHP installed
- Use `admin/booking-handler.php`
- Use `admin/index.html` for admin area

---

## 🔧 Troubleshooting

### Booking form not working?
- Check browser console for errors
- Make sure all required fields are filled
- Try refreshing the page

### Admin area not showing bookings?
- Check if you're using the same browser
- Local storage is browser-specific
- Try the "Refresh" button

### Want to transfer data between browsers?
- Export bookings from one browser
- Import the JSON file in another browser

---

## 📝 Notes

- **Local storage is browser-specific** - Data won't sync between different browsers
- **Data persists** - Bookings remain even after closing browser
- **No internet required** - Works completely offline
- **Perfect for testing** - Great for development and testing

This simple system is perfect for local development and testing. When you're ready for production, you can switch to the server-based version. 