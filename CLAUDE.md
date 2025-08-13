# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Laravel 12 starter kit for tongtji inhouse applications using the Sneat admin template with LDAP authentication integration. The project combines Laravel's robust backend framework with a modern Bootstrap-based admin interface and OpenLDAP user authentication.

## Development Commands

### Initial Setup
```bash
# Install PHP dependencies
composer install

# Install JavaScript dependencies  
npm install

# Copy and configure environment
cp .env.example .env

# Generate application key
php artisan key:generate

# Run database migrations
php artisan migrate
```

### Development Server
```bash
# Start all development services (Laravel server, queue worker, and Vite)
composer run dev

# Or run components separately:
php artisan serve              # Laravel development server
php artisan queue:listen --tries=1  # Queue worker
npm run dev                    # Vite development server
```

### Build and Test
```bash
# Build assets for production
npm run build

# Run tests
composer run test

# Code formatting (if configured)
php artisan pint
```

## Architecture

### LDAP Authentication System
- **Provider**: Uses `LdapRecord\Models\OpenLDAP\User` as LDAP provider in `config/auth.php:74-93`
- **User Model**: `App\Models\User` implements `LdapAuthenticatable` with traits `AuthenticatesWithLdap` and `HasLdapUser`
- **Attribute Mapping**: LDAP attributes are synced to database columns:
  - `cn` → `name`
  - `uid` → `username` 
  - `mail` → `email`
  - `guid` → `guid`
  - `domain` → `domain`
- **Migration**: LDAP-specific columns added via `2025_01_01_000000_add_ldap_columns_to_users_table.php`

### Frontend Integration
- **Template**: Sneat Bootstrap admin template integrated in `public/sneat/`
- **Build System**: Vite with Laravel plugin, builds from `resources/sass/app.scss` and `resources/js/app.js`
- **Layouts**: Multiple Blade layouts in `resources/views/layouts/` including `sneat.blade.php` for admin interface

### User Management
- **Controller**: `UserController` provides basic CRUD operations and DataTables integration
- **Routes**: User management routes protected by `auth` middleware in `routes/web.php:17-21`
- **DataTables**: Server-side processing via `yajra/laravel-datatables-oracle` package

### Key Dependencies
- **LDAP**: `directorytree/ldaprecord-laravel` for LDAP integration
- **UI**: `laravel/ui` for authentication scaffolding
- **DataTables**: `yajra/laravel-datatables-oracle` for data grid functionality
- **Avatar**: `laravolt/avatar` for user avatar generation

## LDAP Operations

### Import Users from LDAP
```bash
# Import specific user by uid
php artisan ldap:import users --filter="(uid=jptest)"

# Import all users (use with caution)
php artisan ldap:import users
```

### LDAP Configuration
LDAP settings are configured via environment variables in `.env`:
- `LDAP_HOST`: LDAP server hostname
- `LDAP_USERNAME`: Bind DN for LDAP connection
- `LDAP_PASSWORD`: Bind password
- `LDAP_BASE_DN`: Base distinguished name for searches
- `LDAP_LOGGING`: Enable/disable LDAP operation logging

## File Structure Notes

### Controllers
- `HomeController`: Dashboard after authentication
- `UserController`: User management with DataTables support
- `Auth/`: Laravel UI authentication controllers

### Models
- `User`: Extended with LDAP authentication capabilities

### Views
- `layouts/sneat.blade.php`: Main admin template layout
- `users/`: User management views with DataTables integration
- `auth/`: Authentication views styled with Sneat template

### Migrations
- Standard Laravel user table with additional LDAP columns (guid, username, domain)
- Supports both MySQL and SQL Server with conditional unique constraints