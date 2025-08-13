# TTA Starter Kit - Aplikasi Internal Tongtji

Starter kit untuk aplikasi internal tongtji menggunakan Laravel dengan template admin Sneat dan integrasi LDAP.

## 🚀 Cara Install

### 1. Persiapan Awal
```bash
# goto project ini
cd tta-starter-sneat

# Install dependencies PHP
composer install

# Install dependencies JavaScript
npm install
```

### 2. Konfigurasi Environment
```bash
# Copy file .env
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 3. Setup Database
Pastikan database MySQL sudah dibuat dengan nama `tta_starter_sneat`, lalu:
```bash
# Jalankan migrasi database
php artisan migrate
```

## 🏃‍♂️ Cara Menjalankan

### Development Mode (Recommended)
```bash
# Jalankan semua service sekaligus (server, queue, vite)
composer run dev
```

### Manual (Jika ingin jalankan terpisah)
```bash
# Terminal 1 - Laravel server
php artisan serve

# Terminal 2 - Queue worker
php artisan queue:listen --tries=1

# Terminal 3 - Vite dev server
npm run dev
```

Buka browser ke: `http://localhost:8000`

## 👥 LDAP Integration

### Import User dari LDAP
```bash
# Import user tertentu
php artisan ldap:import users --filter="(uid=jptest)"

# Import semua user (hati-hati!)
php artisan ldap:import users
```

### Login
- Username: gunakan UID dari LDAP
- Password: password LDAP Anda

## 🧪 Testing

```bash
# Jalankan test
composer run test

# Format code (jika ada)
php artisan pint
```

## 📁 Struktur Project

- `app/Http/Controllers/` - Controller utama
- `app/Models/User.php` - Model user dengan LDAP
- `resources/views/` - Template Blade
- `public/sneat/` - Asset template Sneat
- `config/ldap.php` - Konfigurasi LDAP
- `routes/web.php` - Route aplikasi

## ⚙️ Konfigurasi LDAP

Ubah setting di file `.env`:
```env
LDAP_HOST=19.38.40.5
LDAP_USERNAME="cn=admin,dc=tongtji,dc=com"
LDAP_PASSWORD="your-password"
LDAP_BASE_DN="dc=tongtji,dc=com"
```

## 🎨 Template

Menggunakan **Sneat Bootstrap Admin Template** untuk tampilan yang modern dan responsive.

## 📞 Support

Jika ada masalah, hubungi tim IT Tongtji.