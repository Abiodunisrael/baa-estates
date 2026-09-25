# BAA ESTATES AGENCY

Real estate agency website — houses and lands for sale or lease.

## Stack
- PHP 8+ (PDO, MySQL)
- MySQL 5.7+ / MariaDB
- Vanilla JS, mobile-first CSS

## Local setup
1. Place this folder in `C:\xampp\htdocs\baa_estates\`
2. Start Apache + MySQL in XAMPP
3. Open phpMyAdmin → Import → `database/schema.sql`
4. Edit `config/config.php` if your MySQL root password isn't empty
5. Visit `http://localhost/baa_estates/`

## Default admin
- URL: `/admin/login.php`
- Email: `admin@baaestates.com`
- Password: `Admin@123` — **change this immediately**

## Folder map
- `config/`  – DB + site constants
- `includes/` – shared header/nav/footer/helpers
- `pages/`   – public-facing pages
- `admin/`   – protected admin area
- `assets/`  – css, js, images
- `uploads/` – property images (write-only)
- `database/` – SQL schema
- `logs/`    – error log