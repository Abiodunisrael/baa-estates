# BAA Estates Agency

A full-featured real estate listing platform for houses and lands in Nigeria. Buyers and renters can browse properties by location, type, and price range; admins manage listings, images, locations, and customer inquiries through a secure dashboard.

## ✨ Features

### Public Site
- 🏘️ Browse properties with filters (type, listing, location, price range, keyword search)
- 🔍 Sort by newest, oldest, or price (ascending/descending)
- 📄 Detailed property pages with image gallery, map embed, and key facts
- ✉️ Inquiry form on every property page
- 📱 Fully responsive, mobile-first design
- 💬 Floating WhatsApp contact button

### Admin Dashboard
- 🔐 Session-based authentication with hashed passwords
- 📊 Dashboard with live stats (total/available/sold properties, new inquiries)
- 🏠 Full CRUD for properties (with image uploads, primary image selection)
- 📍 Manage locations (add, edit, delete)
- 📬 View and reply to customer inquiries
- ⚙️ Site settings (name, contact info, socials, WhatsApp)
- 🔑 Change password

## 🛠 Tech Stack

- **Backend:** PHP 8+ with PDO (prepared statements throughout)
- **Database:** MySQL 5.7+ / MariaDB
- **Frontend:** Vanilla JavaScript, modern CSS (custom properties, grid, flexbox)
- **Auth:** Session-based, `password_hash()` / `password_verify()`
- **Icons/Fonts:** Font Awesome, Google Fonts (Inter + Playfair Display)

## 📁 Structure
baa_estates/
├── admin/ # Protected admin dashboard
├── assets/ # CSS, JS, images, logo
├── config/ # Database + site configuration
├── database/ # SQL schema + seed data
├── includes/ # Shared header, nav, footer, helpers
├── logs/ # Error logs (git-ignored)
├── pages/ # Public-facing pages
├── uploads/ # User-uploaded property images (git-ignored)
├── index.php # Front controller (routes all requests)
└── .htaccess # URL rewriting + security rules

## 🚀 Local Setup

### Prerequisites
- PHP 8.0+
- MySQL 5.7+ or MariaDB 10.4+
- Apache with `mod_rewrite` enabled (XAMPP, MAMP, or Laragon all work)

### Steps

1. **Clone the repo** into your web root:
   ```bash
   git clone https://github.com/YOUR_USERNAME/baa-estates.git
   cd baa-estates