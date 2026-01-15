<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Admin Dashboard (Laravel 11/12)

Production-ready admin dashboard scaffolding with roles, permissions, activity logging, and reporting widgets.

### Stack
- Laravel 11/12
- MySQL
- Laravel Sanctum
- Spatie Laravel Permission
- Blade + Bootstrap
- Chart.js
- Laravel Queues & Jobs

### Folder Structure
- `app/Http/Controllers/Admin`
- `app/Models/AdminActivityLog.php`
- `app/Policies/UserPolicy.php`
- `app/Services/AdminActivityLogger.php`
- `database/migrations/2026_01_15_200000_create_permission_tables.php`
- `database/migrations/2026_01_15_200100_create_admin_activity_logs_table.php`
- `database/migrations/2026_01_15_200200_add_admin_fields_to_users_table.php`
- `database/seeders/RolePermissionSeeder.php`
- `resources/views/admin`
- `routes/admin.php`

### Setup
1. Install packages:
```bash
composer require laravel/sanctum spatie/laravel-permission
php artisan vendor:publish --provider="Laravel\\Sanctum\\SanctumServiceProvider"
php artisan vendor:publish --provider="Spatie\\Permission\\PermissionServiceProvider"
```
2. Run migrations and seeders:
```bash
php artisan migrate
php artisan db:seed
```
3. Configure admin credentials in `.env` (optional):
```
ADMIN_EMAIL=admin@example.com
ADMIN_PHONE=9999999999
ADMIN_PASSWORD=ChangeMe123!
```
4. Login with the seeded Super Admin user at `/admin/login`.

### Routes
- Admin dashboard: `/admin`
- Roles: `/admin/roles`
- Permissions: `/admin/permissions`
- Users: `/admin/users`

### Security Notes
- Role/permission checks enforced in `routes/admin.php`.
- Policy checks enforced in `app/Http/Controllers/Admin/AdminUserController.php`.
- Admin actions logged via `app/Services/AdminActivityLogger.php`.

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
