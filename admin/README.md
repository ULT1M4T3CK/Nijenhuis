# Admin System - Nijenhuis Botenverhuur

## Overview
This directory contains the admin system for managing bookings and website content.

## Files Structure

### 🎯 **Primary Admin System (Recommended)**
- **`admin-static.html`** - Main admin dashboard (GitHub Pages compatible)
- **`../pages/admin-login.html`** - Admin login page

### 🔧 **Development/Server Files**
- **`booking-handler.php`** - PHP backend for server deployments
- **`booking-handler.py`** - Python backend alternative
- **`test-login.php`** - Debug tool for login issues

### 📁 **Legacy/Unused Files**
- **`admin-simple.html`** - Old admin system (removed - use admin-static.html)
- **`boat-management.html`** - Boat management interface
- **`index.html`** - Admin index page
- **`index-local.html`** - Local development admin

## 🚀 **How to Access Admin**

### For GitHub Pages (Static Hosting):
1. Go to: `https://ult1m4t3ck.github.io/Nijenhuis/pages/admin-login.html`
2. Login with: `admin` / `nijenhuis2025`
3. Access: `admin-static.html` dashboard

### For Server Deployment (PHP/Python):
1. Go to: `your-domain.com/pages/admin-login.html`
2. Login with: `admin` / `nijenhuis2025`
3. Access: `admin-simple.html` dashboard (if using PHP backend)

## 🔐 **Login Credentials**
- **Username**: `admin`
- **Password**: `nijenhuis2025`

## 📋 **Admin Features**
- ✅ View all bookings
- ✅ Update booking status
- ✅ Delete bookings
- ✅ Export bookings to CSV
- ✅ Real-time statistics
- ✅ 24-hour session management

## 🛠 **Development Notes**
- `admin-static.html` works entirely with localStorage (no server needed)
- `booking-handler.php` requires PHP server for full functionality
- All admin systems use the same login credentials for consistency
