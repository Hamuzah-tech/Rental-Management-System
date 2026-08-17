# Developer Documentation

**Alendi / Rental Management System**

This document describes the **current implementation** in this workspace. Features that exist only as unused views, unused routes, comments, or incomplete methods are marked **Partially implemented** or **Not currently implemented**.

Do not treat this file as a product roadmap. Inspect the code before relying on any behaviour described here.

---

## Project Overview

### What the system does

The application is a multi-role rental (hostel) management system branded in the UI as **Alendi Estates**. It lets a super administrator manage landlord accounts, and lets landlords manage properties (called **hostels** in the landlord UI), tenants, and rent payment submissions.

Tenants do **not** have a login account. They use public pages: a registration link for a specific property, and a tenant-code-based portal to submit payment proof and view payment history.

### Main purpose

- Give administrators a central view of landlords, properties, and tenants.
- Give landlords tools to create hostels, register tenants (manually or via a shareable link), review payment submissions, and export tenant lists to PDF.
- Give tenants a way to register against a hostel and submit rent payment records with a screenshot.



### Target users


| Role                | Who they are          | How they access the system                       |
| ------------------- | --------------------- | ------------------------------------------------ |
| Super administrator | Platform operator     | `/admin/login` (email + password)                |
| Landlord            | Hostel/property owner | `/landlord/login` (username or email + password) |
| Tenant              | Occupant / student    | Public pages only (no authentication)            |




### Technology stack


| Layer                                | Technology                                                                               |
| ------------------------------------ | ---------------------------------------------------------------------------------------- |
| Framework                            | Laravel 12 (`laravel/framework: ^12.0`)                                                  |
| PHP                                  | `^8.2`                                                                                   |
| Database (default in `.env.example`) | SQLite                                                                                   |
| Database (production-oriented code)  | MySQL (admin backup uses `SHOW TABLES`)                                                  |
| Frontend                             | Blade templates, Tailwind CSS 3, Alpine.js 3, Vite 7                                     |
| Auth helpers                         | Laravel session guards; Spatie Permission is installed but **not used at runtime**       |
| PDF                                  | `barryvdh/laravel-dompdf`                                                                |
| Excel package                        | `maatwebsite/excel` is installed; landlord Excel export is **Not currently implemented** |
| Icons                                | `blade-ui-kit/blade-heroicons`                                                           |
| HTTP API                             | Laravel Sanctum is installed; **no** `routes/api.php` **is registered**                  |




### Laravel / PHP / database / frontend (exact)

- **Laravel:** 12.x
- **PHP:** 8.2 or higher
- **Database:** configured via `DB_CONNECTION`. `.env.example` uses `sqlite`. Admin backup (`app/Http/Controllers/Admin/SettingsController.php`) is written for **MySQL**.
- **Frontend build:** Vite (`resources/css/app.css`, `resources/js/app.js`). Production requires `npm run build`. The admin login view still loads Tailwind via CDN in addition to Vite.

There is **no REST API** in this project. All application routes live in `routes/web.php`. There is no `routes/api.php`.

---



## System Architecture

The app is a conventional Laravel MVC application with three user-facing areas (admin, landlord, tenant/public) sharing one `users` table for staff accounts.

```
Browser
  ├── Public home / tenant portal / registration
  ├── Admin session (guard: admin)
  └── Landlord session (guard: landlord)
           │
           ▼
routes/web.php
           │
           ▼
Controllers  →  Models (Eloquent)  →  MySQL/SQLite
           │
           ▼
Blade views (layouts/admin, layouts/landlord, standalone tenant pages)
```



### Laravel project structure (important paths)


| Path                                                      | Purpose                                                                |
| --------------------------------------------------------- | ---------------------------------------------------------------------- |
| `app/Http/Controllers/`                                   | HTTP controllers, grouped by `Admin/`, `Landlord/`, `Tenant/`, `Auth/` |
| `app/Http/Middleware/RoleMiddleware.php`                  | Custom `role` middleware (string column on `users`)                    |
| `app/Http/Requests/admin/`                                | Form requests for admin property create/update                         |
| `app/Models/`                                             | `User`, `Property`, `Tenant`, `Payment`                                |
| `app/Models/Traits/HasPublicId.php`                       | ULID `public_id` generation and route key                              |
| `app/Services/`                                           | `PropertyService`, `LandlordService`, `PasswordGenerator`              |
| `app/Mail/LandlordWelcomeMail.php`                        | Welcome email when an admin creates a landlord                         |
| `app/Notifications/LandlordResetPasswordNotification.php` | Landlord password-reset email                                          |
| `app/Traits/SoftDeleteTrait.php`                          | Unused helper trait                                                    |
| `bootstrap/app.php`                                       | Middleware aliases and guest redirects                                 |
| `config/auth.php`                                         | Guards and providers                                                   |
| `database/migrations/`                                    | Schema changes                                                         |
| `database/seeders/`                                       | Roles + super admin                                                    |
| `resources/views/`                                        | Blade UI                                                               |
| `routes/web.php`                                          | All HTTP routes                                                        |
| `routes/console.php`                                      | Only the default `inspire` command                                     |
| `public/payments/`                                        | Payment screenshot files (not Laravel Storage)                         |




### Models


| Model      | File                      | Table        | Route key          |
| ---------- | ------------------------- | ------------ | ------------------ |
| `User`     | `app/Models/User.php`     | `users`      | `id`               |
| `Property` | `app/Models/Property.php` | `properties` | `public_id` (ULID) |
| `Tenant`   | `app/Models/Tenant.php`   | `tenants`    | `public_id` (ULID) |
| `Payment`  | `app/Models/Payment.php`  | `payments`   | `public_id` (ULID) |


Relationships:

- `User` `hasMany` `Property` via `landlord_id`
- `Property` `belongsTo` `User` as `landlord`
- `Property` `hasMany` `Tenant`
- `Tenant` `belongsTo` `Property`
- `Tenant` `hasMany` `Payment`
- `Payment` `belongsTo` `Tenant`
- `Payment` `belongsTo` `User` as `approver` (`approved_by`)

`User::tenant()` (`hasOne Tenant`) is defined but tenants are **not** linked to users. There is no `user_id` column on `tenants`.

`Property`, `Tenant`, and `Payment` use `HasPublicId`. `User`, `Property`, and `Tenant` use `SoftDeletes`. **No migration in this repo adds** `deleted_at`**.** See [Known Issues](#known-issues--technical-debt).

### Controllers


| Area                    | File                                                                                        |
| ----------------------- | ------------------------------------------------------------------------------------------- |
| Admin login             | `app/Http/Controllers/Auth/AdminLoginController.php`                                        |
| Landlord login          | `app/Http/Controllers/Auth/LandlordLoginController.php`                                     |
| Admin dashboard         | `app/Http/Controllers/Admin/DashboardController.php`                                        |
| Admin landlords         | `app/Http/Controllers/Admin/LandlordController.php`                                         |
| Admin properties        | `app/Http/Controllers/Admin/PropertyController.php`                                         |
| Admin tenants           | `app/Http/Controllers/Admin/TenantController.php`                                           |
| Admin settings          | `app/Http/Controllers/Admin/SettingsController.php`                                         |
| Landlord dashboard      | `app/Http/Controllers/Landlord/DashboardController.php`                                     |
| Landlord properties     | `app/Http/Controllers/Landlord/PropertyController.php`                                      |
| Landlord tenants        | `app/Http/Controllers/Landlord/TenantController.php`                                        |
| Landlord payments       | `app/Http/Controllers/Landlord/PaymentController.php`                                       |
| Landlord password reset | `app/Http/Controllers/Landlord/ForgotPasswordController.php`, `ResetPasswordController.php` |
| Tenant registration     | `app/Http/Controllers/TenantRegistrationController.php`                                     |
| Tenant payments         | `app/Http/Controllers/Tenant/PaymentController.php`                                         |




### Middleware

Registered in `bootstrap/app.php`:

```php
'role' => \App\Http\Middleware\RoleMiddleware::class
```

Guest redirect:

- Paths matching `admin/*` → `admin.login`
- Everything else (including `landlord/*`) → `landlord.login`

Route groups:

- Admin: `auth:admin` + `role:super_admin`
- Landlord: `auth:landlord` + `role:landlord`

`RoleMiddleware` (`app/Http/Middleware/RoleMiddleware.php`) checks:

1. A user is authenticated on the default guard for the request (Laravel’s `auth:{guard}` middleware calls `shouldUse`, so this works).
2. `strtolower($user->role)` is in the allowed list.
3. If `is_active` is set and false, the request is aborted with 403.

This is **not** Spatie permission middleware.

### Services


| Service             | File                                 | Used by                                         |
| ------------------- | ------------------------------------ | ----------------------------------------------- |
| `PropertyService`   | `app/Services/PropertyService.php`   | Admin `PropertyController` create/update/delete |
| `LandlordService`   | `app/Services/LandlordService.php`   | **Not used** by the admin landlord create flow  |
| `PasswordGenerator` | `app/Services/PasswordGenerator.php` | **Not referenced** elsewhere                    |


Admin landlord creation happens inline in `Admin\LandlordController::store()`.

### Requests / validation

Form request classes exist only for admin properties:

- `app/Http/Requests/admin/StorePropertyRequest.php`
- `app/Http/Requests/admin/UpdatePropertyRequest.php`

Those requests validate `landlord_id`, `name`, `address`, `description` only. They do **not** validate `monthly_rent` or `max_tenants`.

Most other validation is inline `$request->validate(...)` in controllers.

### Views / Blade templates


| Layout / area             | Path                                                                                              |
| ------------------------- | ------------------------------------------------------------------------------------------------- |
| Admin layout              | `resources/views/layouts/admin.blade.php`                                                         |
| Admin sidebar / topbar    | `resources/views/components/admin/sidebar.blade.php`, `topbar.blade.php`                          |
| Landlord layout           | `resources/views/layouts/landlord.blade.php`                                                      |
| Landlord sidebar / header | `resources/views/landlord/partials/sidebar.blade.php`, `header.blade.php`                         |
| Public home               | `resources/views/home.blade.php`                                                                  |
| Tenant pages              | `resources/views/tenant/` (standalone HTML, not the landlord layout)                              |
| PDF templates             | `resources/views/exports/tenants-pdf.blade.php`, `resources/views/admin/tenants/export.blade.php` |


Vite is included via `@vite(['resources/css/app.css', 'resources/js/app.js'])` on most pages.

### Routes

All HTTP routes: `routes/web.php`. Console: `routes/console.php` (no scheduled jobs). See [Routes Documentation](#routes-documentation).

### Authentication and authorization (summary)

- One Eloquent provider: `App\Models\User` (`config/auth.php`).
- Guards `web`, `admin`, `landlord`, and `tenant` all use that provider.
- The `tenant` guard is **unused**. Tenants never log in.
- Authorization is the `users.role` string (`super_admin` / `landlord`) plus `is_active`, plus landlord ownership checks (`property.landlord_id === Auth::guard('landlord')->id()`).



### How major components communicate

1. A request hits `public/index.php` → `routes/web.php`.
2. Middleware authenticates the correct guard and checks `users.role`.
3. Controllers query Eloquent models. Properties/tenants/payments are bound by `public_id`.
4. Landlords only see rows whose property `landlord_id` matches their user id.
5. Tenant registration and payment submission are public and identified by registration token or tenant code.
6. Payment screenshots are saved under `public/payments/` and served either as static files or via `GET /payments/{filename}`.

---



## Installation & Setup



### Requirements (local)

- PHP 8.2+ with extensions typically required by Laravel 12 (OpenSSL, PDO, Mbstring, Tokenizer, XML, Ctype, JSON, BCMath, Fileinfo)
- Composer
- Node.js + npm (Vite / Tailwind)
- A database: SQLite for the default example, or MySQL if you match production
- `doctrine/dbal` is required because some migrations call `->change()`



### Clone / download

```bash
git clone <repository-url>
cd "Rental Management System"
```



### Composer

```bash
composer install
```



### Environment file

```bash
copy .env.example .env
php artisan key:generate
```

On Linux/macOS use `cp .env.example .env`.

### Database configuration

**SQLite (matches** `.env.example`**):**

```env
DB_CONNECTION=sqlite
```

Create the file if needed:

```bash
php artisan migrate
```

Laravel’s Composer `post-create-project-cmd` would also `touch database/database.sqlite`. If that file is missing, create `database/database.sqlite`.

**MySQL (typical production):**

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

Do not commit real passwords.

### Migrations

```bash
php artisan migrate
```

**Warning:** a fresh migrate of this repository is not guaranteed to succeed. Several migrations overlap or assume columns that other migrations also add. Soft-delete columns used by the models are missing from migrations. See [Known Issues](#known-issues--technical-debt).

If you are targeting an existing production database, **do not** assume `migrate` is a no-op. Inspect `php artisan migrate:status` first.

### Seeders

```bash
php artisan db:seed
```

This runs `database/seeders/DatabaseSeeder.php`, which calls:

1. `RolesSeeder` — Spatie roles named `Super Admin` and `Landlord`
2. `SuperAdminSeeder` — creates a user with email `admin@rms.com`

**Partially implemented:** `SuperAdminSeeder` does **not** set `role = super_admin` or `is_active = 1`, but admin login requires both. It also calls `$admin->assignRole('Super Admin')` even though `User` does not use Spatie’s `HasRoles` trait. Inspect and fix the seeder before relying on it. Default credentials live in `database/seeders/SuperAdminSeeder.php` and must be changed for any shared or production environment.

### Storage linking

```bash
php artisan storage:link
```

Payment screenshots are **not** stored on the `storage` disk. They are written to `public/payments/`. Create that directory and make it writable:

```bash
mkdir public\payments
```

Admin database backups are written to `storage/app/backups/` (created automatically if missing).

### Cache / configuration commands (local)

Usually you do **not** want config/route/view caching while developing:

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```



### Frontend / build

```bash
npm install
npm run dev
```

For a one-off production-like asset build:

```bash
npm run build
```

Without a Vite build (or `npm run dev`), pages that use `@vite` will fail with a Vite manifest error.

Composer also defines:

```bash
composer run dev
```

which starts `php artisan serve`, a queue listener, Pail, and Vite together. The application does **not** currently require a queue worker for its implemented mail (welcome mail is sent with `Mail::send()`, not queued).

### Development server

```bash
php artisan serve
```

Then open the URL shown (typically `http://127.0.0.1:8000`).

Home page: `/`  
Admin login: `/admin/login`  
Landlord login: `/landlord/login`  
Tenant portal: `/tenant/payments`

---



## Environment Configuration

Values below are from `.env.example` and `config/*.php`. Do not put real secrets in documentation or git.


| Variable                                                                      | What it controls                                                                                               |
| ----------------------------------------------------------------------------- | -------------------------------------------------------------------------------------------------------------- |
| `APP_NAME`                                                                    | Application name in mail and some UI (`config/app.php`). Home page branding is hard-coded as “Alendi Estates”. |
| `APP_ENV`                                                                     | `local` / `production`                                                                                         |
| `APP_KEY`                                                                     | Encryption key. Required. Generate with `php artisan key:generate`.                                            |
| `APP_DEBUG`                                                                   | Detailed error pages. Must be `false` in production.                                                           |
| `APP_URL`                                                                     | Root URL. Used for password-reset links and `route()` URLs. Must match the public URL (including `https`).     |
| `APP_LOCALE`                                                                  | Locale (`en`)                                                                                                  |
| `LOG_CHANNEL` / `LOG_LEVEL`                                                   | Logging                                                                                                        |
| `DB_CONNECTION`                                                               | `sqlite` or `mysql` (or another supported driver)                                                              |
| `DB_HOST` / `DB_PORT` / `DB_DATABASE` / `DB_USERNAME` / `DB_PASSWORD`         | MySQL connection (unused when `sqlite`)                                                                        |
| `SESSION_DRIVER`                                                              | Default example: `database` (needs `sessions` table)                                                           |
| `SESSION_LIFETIME`                                                            | Session lifetime in minutes (default 120)                                                                      |
| `QUEUE_CONNECTION`                                                            | Default example: `database`. Implemented mail is sent synchronously.                                           |
| `CACHE_STORE`                                                                 | Default example: `database`                                                                                    |
| `MAIL_MAILER`                                                                 | Default example: `log` (writes mail to the log). Production typically `smtp`.                                  |
| `MAIL_HOST` / `MAIL_PORT` / `MAIL_USERNAME` / `MAIL_PASSWORD` / `MAIL_SCHEME` | SMTP settings                                                                                                  |
| `MAIL_FROM_ADDRESS` / `MAIL_FROM_NAME`                                        | From header                                                                                                    |
| `FILESYSTEM_DISK`                                                             | Default `local`                                                                                                |
| `VITE_APP_NAME`                                                               | Exposed to Vite                                                                                                |


Password reset tokens expire in **60 minutes** (`config/auth.php`).

---



## Database Documentation

Schema below is the **effective** shape implied by migrations **plus** columns the models/controllers actually use. Columns with no migration in this repo are called out.

### Relationship overview

```
users (role = landlord | super_admin)
   └── properties.landlord_id
          └── tenants.property_id
                 └── payments.tenant_id
                        └── payments.approved_by → users.id
```

The `landlords` table exists (`database/migrations/2026_07_07_105351_create_landlords_table.php`) with only `id` and timestamps. **It is unused.** Landlords are rows in `users` with `role = 'landlord'`.

Spatie tables (`roles`, `permissions`, `model_has_roles`, etc.) are created by `database/migrations/2026_07_06_135052_create_permission_tables.php`. Runtime authorization does **not** query them.

### `users`

Created by `database/migrations/0001_01_01_000000_create_users_table.php`.


| Column                      | Type (migration)         | Notes                                                    |
| --------------------------- | ------------------------ | -------------------------------------------------------- |
| `id`                        | bigint PK                |                                                          |
| `name`                      | string                   |                                                          |
| `username`                  | string, unique           | Landlord login can use this                              |
| `email`                     | string, unique, nullable | Admin login uses email                                   |
| `phone`                     | string                   | Required in migration                                    |
| `second_phone`              | string, nullable         |                                                          |
| `email_verified_at`         | timestamp, nullable      | Not used by the app login flow                           |
| `password`                  | string                   | Hashed                                                   |
| `status`                    | boolean, default true    | Toggled together with `is_active` in admin landlord code |
| `remember_token`            | string                   |                                                          |
| `created_at` / `updated_at` | timestamps               |                                                          |


**Used in code, no matching migration in this repo:**


| Column          | Used for                                                      |
| --------------- | ------------------------------------------------------------- |
| `role`          | `'super_admin'` or `'landlord'`                               |
| `is_active`     | Login and `RoleMiddleware`                                    |
| `last_login_at` | Set on successful admin/landlord login                        |
| `deleted_at`    | Soft deletes on `User`                                        |
| `landlord_id`   | Listed in `User::$fillable` but not used as a landlord parent |




### `properties`

Base: `database/migrations/2026_07_06_140306_create_properties_table.php`, then several alter migrations.


| Column                      | Type                                         | Notes                                                             |
| --------------------------- | -------------------------------------------- | ----------------------------------------------------------------- |
| `id`                        | bigint PK                                    |                                                                   |
| `public_id`                 | ULID, unique, nullable                       | Route key; auto-set on create                                     |
| `landlord_id`               | FK → `users.id`, cascade delete              | Owner                                                             |
| `name`                      | string                                       | Hostel name                                                       |
| `address`                   | string, later made nullable                  |                                                                   |
| `description`               | text, nullable                               |                                                                   |
| `monthly_rent`              | decimal(10,2), default 0                     | Copied onto new tenants                                           |
| `max_tenants`               | integer, default 10                          | Capacity for `isFull()`                                           |
| `registration_token`        | string, nullable (one migration also unique) | Public registration URL                                           |
| `status`                    | boolean, default true                        | Active/inactive; inactive properties cannot be registered against |
| `registration_open`         | boolean, default true                        | Landlord can close registration                                   |
| `created_at` / `updated_at` | timestamps                                   |                                                                   |


**Used in code, no matching migration:** `deleted_at`.

`Property::getFullAddressAttribute()` reads `city`, `state`, `postal_code`, `country`. **Those columns are not in migrations.** Treat as unused/dead code.

Capacity is **not** a database constraint. `Property::isFull()` counts non-deleted tenants and compares to `max_tenants`.

### `tenants`

Base: `database/migrations/2026_07_06_140307_create_tenants_table.php`.


| Column                      | Type                                                             | Notes                                        |
| --------------------------- | ---------------------------------------------------------------- | -------------------------------------------- |
| `id`                        | bigint PK                                                        |                                              |
| `public_id`                 | ULID, unique, nullable                                           | Route key                                    |
| `tenant_code`               | string, unique                                                   | Lookup key for public payments               |
| `name`                      | string                                                           |                                              |
| `email`                     | string, nullable in original migration; registration requires it |                                              |
| `phone`                     | string                                                           | Normalized Malawi local format               |
| `property_id`               | FK → `properties.id`, cascade delete                             |                                              |
| `monthly_rent`              | decimal(12,2) after later change                                 | Used to calculate expected payment amount    |
| `move_in_date`              | date, nullable                                                   |                                              |
| `status`                    | string, migration default `'Active'`                             | Application writes `'active'`, `'moved_out'` |
| `created_at` / `updated_at` | timestamps                                                       |                                              |


**Used in code, no matching migration:** `deleted_at`, `move_out_date` (also **not** in `$fillable`, so mass assignment of move-out date is ignored).

Phone uniqueness: one migration adds a global unique index on `phone`; another tries to replace it with unique `(property_id, phone)`. Application logic checks uniqueness **per property**. See technical debt.

Tenant codes:

- Self-registration sets `'TEN-' . strtoupper(Str::random(8))`.
- Landlord-created tenants leave `tenant_code` empty; `Tenant::boot()` generates a 6-character alphanumeric code.



### `payments`

Base: `database/migrations/2026_07_06_140307_create_payments_table.php`. A later migration (`2026_07_09_133821_add_payment_fields_to_payments_table.php`) tries to add the **same** columns again and will fail on a database that already ran the create migration.


| Column                      | Type                                        | Notes                                                  |
| --------------------------- | ------------------------------------------- | ------------------------------------------------------ |
| `id`                        | bigint PK                                   |                                                        |
| `public_id`                 | ULID, unique, nullable                      | Route key                                              |
| `tenant_id`                 | FK → `tenants.id`, cascade delete           |                                                        |
| `payment_month`             | string                                      | Comma-separated `Y-m` values, e.g. `2026-08,2026-09`   |
| `amount`                    | decimal                                     | Must equal `monthly_rent * month_count` at submit time |
| `screenshot`                | string                                      | Filename only, under `public/payments/`                |
| `status`                    | enum `Pending`, `Approved`, `Rejected`      | Default `Pending`                                      |
| `remarks`                   | text, nullable                              | Used when rejecting                                    |
| `submitted_at`              | timestamp (nullable in the later migration) | **Not set** by `Tenant\PaymentController::store()`     |
| `approved_at`               | timestamp, nullable                         | Set on approve/reject                                  |
| `approved_by`               | FK → `users.id`, null on delete             | Landlord who processed it                              |
| `created_at` / `updated_at` | timestamps                                  |                                                        |


There is **no** “Paid / Unpaid / Partially Paid” column. Paid/Unpaid in the landlord property tenant list is computed: a tenant is **Paid** for a month if they have an **Approved** payment whose `payment_month` list contains that `Y-m`.

### Other tables


| Table                                  | Purpose                                       |
| -------------------------------------- | --------------------------------------------- |
| `sessions`                             | Database sessions (`SESSION_DRIVER=database`) |
| `password_reset_tokens`                | Landlord password reset                       |
| `cache` / `cache_locks`                | Database cache                                |
| `jobs` / `job_batches` / `failed_jobs` | Queue tables (no app jobs are dispatched)     |
| `personal_access_tokens`               | Sanctum (unused by this app’s routes)         |
| Spatie permission tables               | Seeded; not used for HTTP authorization       |
| `landlords`                            | Empty stub table                              |


---



## Authentication & Authorization



### Login system

**Admin** (`AdminLoginController`):

- URL: `GET/POST /admin/login`
- Credentials: email + password
- Extra attempt constraints: `role = super_admin` and `is_active = 1`
- Rate limit: 5 attempts per email+IP
- On success: regenerates session, sets `last_login_at`, redirects to `admin.dashboard`
- Logout: `POST /admin/logout`

**Landlord** (`LandlordLoginController`):

- URL: `GET/POST /landlord/login`
- Field `username` accepts either username or email
- After a successful `auth:landlord` attempt, the controller rejects non-`landlord` roles and inactive users
- Rate limit: 5 attempts; failed/unauthorized hits use a 5-minute decay
- Logout: `POST /landlord/logout`

**Tenant login:** **Not currently implemented.** The `tenant` guard exists in `config/auth.php` only. `Tenant\PaymentController::create()` contains a branch for `auth()->user()->role === 'tenant'` and `Tenant::where('user_id', ...)`, but tenants have no `user_id` and no login routes.

### User registration

Public **self-registration of application users** (Breeze-style) is **Not currently implemented** in `routes/web.php`.

What exists:

- Admin creates landlord users (`Admin\LandlordController::store`).
- Tenants register against a property via `/register/property/{token}` (`TenantRegistrationController`). That creates a `tenants` row, not a `users` row.



### Password handling

- Passwords are hashed with Laravel’s hasher (`Hash::make` / `Auth::attempt`).
- Landlord forgot-password: `ForgotPasswordController` uses the `users` password broker. It only sends mail if the email belongs to an active landlord, but always shows the same success message (avoids email enumeration).
- Reset uses `LandlordResetPasswordNotification` and `ResetPasswordController`.
- Admin can reset a landlord password (`admin.landlords.reset-password`). The new password is flashed to the admin session (`new_password`). There is no landlord self-service “change password while logged in” screen.



### Roles

Runtime roles are strings on `users.role`:


| Value         | Meaning         |
| ------------- | --------------- |
| `super_admin` | Admin portal    |
| `landlord`    | Landlord portal |


Spatie roles `Super Admin` and `Landlord` are seeded only.

`User::hasRole($role)` compares the string column, case-insensitive.

### Permissions

There is **no** per-action permission matrix (no `can:edit-tenants` style checks). Access is:

1. Guard + role middleware
2. Ownership checks inside landlord controllers (`authorizeProperty` / `authorizeTenant` / `abort_unless` on payments)

Admin landlord methods `statistics()`, `bulkAction()`, `export()`, `search()` exist on the controller but have **no routes**. **Not currently implemented** as HTTP features.

### Access restrictions by actor


| Actor       | Can access                                                                                |
| ----------- | ----------------------------------------------------------------------------------------- |
| Super admin | `/admin/*` dashboard, landlords, properties, tenants list/export/archive, settings backup |
| Landlord    | `/landlord/*` own hostels, own tenants, own payments                                      |
| Tenant      | Public registration + `/tenant/payments*` by tenant code                                  |
| Guest       | Home, both login pages, password reset, public tenant routes                              |


A landlord hitting `/admin/*` is unauthenticated on the admin guard and is redirected to admin login. An admin hitting `/landlord/*` is redirected to landlord login unless they also have a landlord session.

---



## Application Modules



### Dashboard (admin)

1. **Purpose:** Show totals.
2. **Workflow:** After admin login, `/admin/dashboard` counts landlords (`users.role = landlord`), properties, and tenants.
3. **Routes:** `GET /admin/dashboard` (`admin.dashboard`)
4. **Controller:** `app/Http/Controllers/Admin/DashboardController.php`
5. **Models:** `User`, `Property`, `Tenant`
6. **Views:** `resources/views/admin/index.blade.php`
7. **Tables:** `users`, `properties`, `tenants`
8. **Validation:** none
9. **Permissions:** `auth:admin` + `role:super_admin`



### Dashboard (landlord)

1. **Purpose:** Show hostel count and tenant count.
2. **Workflow:** `/landlord/dashboard` counts the landlord’s properties and tenants. `$activeTenants` is computed with `status = 'Active'` but **not displayed**. Because registration writes `'active'`, that count is likely always 0.
3. **Routes:** `GET /landlord/dashboard`
4. **Controller:** `app/Http/Controllers/Landlord/DashboardController.php`
5. **Models:** `Property`, `Tenant`
6. **Views:** `resources/views/landlord/dashboard.blade.php`
7. **Tables:** `properties`, `tenants`
8. **Validation:** none
9. **Permissions:** `auth:landlord` + `role:landlord`

The landlord sidebar (`resources/views/landlord/partials/sidebar.blade.php`) links to Dashboard, Hostels, Add Tenant, Payments, and Archive.

### Landlords (admin)

1. **Purpose:** Create and manage landlord user accounts.
2. **Workflow:** Admin fills name, username, email, phone. System generates a random password, creates `users.role = landlord`, emails `LandlordWelcomeMail`, lists landlords with property counts. Admin can edit, toggle `status`/`is_active`, reset password, soft-delete (blocked if the landlord still has properties), restore, or force-delete.
3. **Routes:** `admin.landlords.`*, `admin.landlords.status`, `admin.landlords.reset-password`, trash restore/force-delete
4. **Controller:** `app/Http/Controllers/Admin/LandlordController.php`
5. **Model:** `User`
6. **Views:** `resources/views/admin/landlords/`, `resources/views/admin/trash/landlords.blade.php`
7. **Tables:** `users`
8. **Validation:** unique username/email/phone on create; unique email/phone/username on update
9. **Permissions:** super admin only

`DB::commit()` is called in `store()` without `DB::beginTransaction()`. See technical debt.

### Properties / Hostels

**Admin**

1. **Purpose:** CRUD all properties, assign a landlord.
2. **Workflow:** List → create/edit via `PropertyService` → archive if no active tenants → restore / force-delete.
3. **Routes:** `admin.properties.`*, `admin.trash.properties*`
4. **Controller:** `app/Http/Controllers/Admin/PropertyController.php`
5. **Model:** `Property` (`app/Services/PropertyService.php`)
6. **Views:** `resources/views/admin/properties/`, `resources/views/admin/trash/properties.blade.php`
7. **Tables:** `properties`
8. **Validation:** `StorePropertyRequest` / `UpdatePropertyRequest`
9. **Permissions:** super admin

**Partially implemented:** `create()` uses `User::role('Landlord')` (Spatie). `User` does not use `HasRoles`, so the create form is likely to error.

**Landlord**

1. **Purpose:** Manage the landlord’s own hostels and the tenants in each hostel.
2. **Workflow:** Create hostel (rent, max tenants, token generated) → list → show tenants with Paid/Unpaid filter → edit → toggle active status → open/close registration → archive → restore. PDF export of the tenant list.
3. **Routes:** `landlord.properties.`*, `landlord.properties.status`, `landlord.properties.toggle-registration`, `landlord.properties.export.pdf`, trashed/restore
4. **Controller:** `app/Http/Controllers/Landlord/PropertyController.php`
5. **Model:** `Property`, `Tenant`, `Payment`
6. **Views:** `resources/views/landlord/properties/`
7. **Tables:** `properties`, `tenants`, `payments`
8. **Validation:** name, optional address/description, monthly_rent ≥ 0, max_tenants ≥ 1
9. **Permissions:** landlord, plus ownership check



### Tenants

**Landlord**

1. **Purpose:** Add, view, edit, archive, restore tenants; generate registration links; mark moved out.
2. **Workflow:** Add Tenant form can either create a tenant directly or generate a registration link (AJAX). Tenant show page lists that tenant’s payments.
3. **Routes:** `landlord.tenants.`*, `landlord.tenants.generate-link`, move-out, reactivate, trashed/restore, `landlord.tenants.export.pdf`
4. **Controller:** `app/Http/Controllers/Landlord/TenantController.php`
5. **Model:** `Tenant`
6. **Views:** `resources/views/landlord/tenants/`
7. **Tables:** `tenants`, `properties`, `payments`
8. **Validation:** property exists, name, email, Malawi phone, move-in date ≥ today; duplicate phone per property blocked
9. **Permissions:** landlord + tenant’s property ownership

`index()` exists (`landlord.tenants.index`) but the sidebar goes to **Add Tenant**, not the full tenant list. Tenants are mainly managed from the hostel show page.

**Admin**

1. **Purpose:** Search/filter all tenants, export PDF, archive/restore/force-delete.
2. **Routes:** `admin.tenants.index`, `admin.tenants.export`, `admin.tenants.destroy`, trash routes
3. **Controller:** `app/Http/Controllers/Admin/TenantController.php`
4. **Views:** `resources/views/admin/tenants/index.blade.php`, `export.blade.php`, `resources/views/admin/trash/tenants.blade.php`
5. **Permissions:** super admin

**Partially implemented:** `Route::resource('tenants', ...)` also registers create/store/show/edit/update, but those controller methods are missing. Views `admin/tenants/create.blade.php`, `edit.blade.php`, `show.blade.php` exist but are unused. Restore/force-delete look up by numeric `id` while the route parameter is named `{public_id}`.

### Tenant registration

1. **Purpose:** Let a tenant join a hostel without a landlord typing the record.
2. **Workflow:** See [Main Workflows](#main-workflows).
3. **Routes:** `tenant.registration`, `tenant.registration.store`, success/full/closed, `tenant.check-phone`
4. **Controller:** `app/Http/Controllers/TenantRegistrationController.php`
5. **Models:** `Property`, `Tenant`
6. **Views:** `resources/views/tenant/registration/`
7. **Tables:** `tenants`, `properties`
8. **Validation:** name, unique email among non-deleted tenants, Malawi phone, move-in date today…+2 years, optional custom rent; rate limit 3 attempts / 10 minutes / IP
9. **Permissions:** public, gated by token, `status`, `registration_open`, and capacity



### Registration links / tokens

- Each property has `registration_token` (random 40 characters on create).
- Public URL: `/register/property/{token}` named `tenant.registration`.
- Landlord Add Tenant page calls `POST /landlord/tenants/generate-link` (`generateRegistrationLink`) which ensures a token exists and returns JSON `{ success, link, token }`.
- Landlord can toggle `registration_open` on the hostel show page.

**Not currently implemented:**

- `POST /landlord/tenants/copy-link` → `copyRegistrationLink` (no controller method)
- `GET /tenants/property/{property}/registration-link` → `Admin\TenantController::registrationLink` (no method; also **unauthenticated**)
- View `resources/views/landlord/registration-link.blade.php` is unused by working routes



### Payments

1. **Purpose:** Tenants submit proof of payment; landlords approve or reject.
2. **Workflow:** See [Payment Workflow](#payment-workflow).
3. **Routes:** tenant `tenant.payments.`*; landlord `landlord.payments.index|show|approve|reject`
4. **Controllers:** `app/Http/Controllers/Tenant/PaymentController.php`, `app/Http/Controllers/Landlord/PaymentController.php`
5. **Model:** `Payment`
6. **Views:** `resources/views/tenant/payments/`, `resources/views/landlord/payments/`
7. **Tables:** `payments`
8. **Validation:** tenant_code exists, month `Y-m`, month_count 1–12, amount matches rent × months, screenshot image max 102400 KB; reject remarks optional max 1000
9. **Permissions:** tenant routes public; landlord routes authenticated + ownership

**Not currently implemented:** `tenant.payments.public-search` and `tenant.payments.public-history` (methods missing). View `resources/views/tenant/payments/results.blade.php` is unused.

Landlord payment month filter options are **hard-coded from August 2026 to December 2027** in `Landlord\PaymentController::index()`.

### Payment status

Stored statuses: **Pending**, **Approved**, **Rejected** only.

Derived hostel-list statuses: **Paid** / **Unpaid** for a selected month (Approved payment covers that month or not).

**Partially Paid** is **Not currently implemented**. Outstanding balance as a stored or computed ledger is **Not currently implemented** (there is `Tenant::getTotalPaymentsAttribute()` summing approved amounts, but no balance-due UI).

### Notifications

See [Notification System](#notification-system).

### Reports / PDF exports

Implemented:


| Who      | What                              | Route                            | Template                                                  |
| -------- | --------------------------------- | -------------------------------- | --------------------------------------------------------- |
| Landlord | Tenant list for one property      | `landlord.properties.export.pdf` | `resources/views/exports/tenants-pdf.blade.php`           |
| Landlord | All landlord tenants (filterable) | `landlord.tenants.export.pdf`    | same (route exists; **no button** in current Blade views) |
| Admin    | All tenants (filterable)          | `admin.tenants.export`           | `resources/views/admin/tenants/export.blade.php`          |


**Partially implemented / broken:** `landlord.properties.export.property.pdf` loads `exports.property-tenants-pdf`, which **does not exist**.

**Not currently implemented:** `landlord.tenants.export.excel` (no `exportExcel` method). `maatwebsite/excel` is unused in controllers. Leftover view `resources/views/tenants-pdf.blade.php` is unused.

### Email

Implemented:

- `LandlordWelcomeMail` when an admin creates a landlord (`Mail::to(...)->send(...)`)
- `LandlordResetPasswordNotification` for landlord password reset

No tenant emails. No payment-approved emails. Queue workers are not required for these two flows.

### User management

Only via admin landlord CRUD. There is no admin user-management UI for additional super admins. Super admin is expected to exist as a seeded/manual `users` row.

### Settings

1. **Purpose:** Database backup.
2. **Routes:** `admin.settings.index`, `backup`, `download`, `delete`
3. **Controller:** `app/Http/Controllers/Admin/SettingsController.php`
4. **View:** `resources/views/admin/settings/index.blade.php` — currently only a **Create Backup** button
5. **Storage:** `storage/app/backups/*.sql`

**Partially implemented:** download and delete routes exist, but the current settings view does not list files or link to those routes. Backup SQL generation uses MySQL `SHOW TABLES` and `SHOW CREATE TABLE`; it is not SQLite-compatible. Chunking assumes an `id` column on every table.

### Other discovered items

- Public home manuals dropdown in `home.blade.php` calls JavaScript `requestApproval()` / `requestTenantApproval()` — UI stub, not an in-app manual viewer.
- Health endpoint: `GET /up` (Laravel default from `bootstrap/app.php`).
- Error view: `resources/views/errors/419.blade.php`.

---



## Routes Documentation

Important routes from `routes/web.php`. `{property}`, `{tenant}`, and `{payment}` bind on `public_id`. `{landlord}` binds on `User` id.

### Public


| Method | URL                           | Name                          | Action                              | Auth   | Purpose                  |
| ------ | ----------------------------- | ----------------------------- | ----------------------------------- | ------ | ------------------------ |
| GET    | `/`                           | `home`                        | Closure → `home` view               | Guest  | Portal chooser           |
| GET    | `/register/property/{token}`  | `tenant.registration`         | `TenantRegistrationController@show` | Public | Registration form        |
| POST   | `/register/property/{token}`  | `tenant.registration.store`   | `store`                             | Public | Submit registration      |
| GET    | `/register/success/{tenant}`  | `tenant.registration.success` | `success`                           | Public | Show tenant code         |
| GET    | `/register/full/{property}`   | `tenant.registration.full`    | `full`                              | Public | Property full page       |
| GET    | `/register/closed/{property}` | `tenant.registration.closed`  | `closed`                            | Public | Registration closed page |
| POST   | `/tenant/check-phone`         | `tenant.check-phone`          | `checkPhone`                        | Public | AJAX phone uniqueness    |
| GET    | `/tenant/payments`            | `tenant.payments.index`       | `Tenant\PaymentController@index`    | Public | Tenant portal home       |
| GET    | `/tenant/payments/create`     | `tenant.payments.create`      | `create`                            | Public | Payment form             |
| POST   | `/tenant/payments`            | `tenant.payments.store`       | `store`                             | Public | Save payment             |
| GET    | `/tenant/payments/history`    | `tenant.payments.history`     | `history`                           | Public | History by tenant code   |
| POST   | `/tenant/payments/search`     | `tenant.payments.search`      | `search`                            | Public | Redirect to history      |
| GET    | `/payments/{filename}`        | (unnamed)                     | Closure                             | Public | Serve screenshot         |


**Broken / missing methods:**


| Method | URL                                              | Name                                 | Problem                                            |
| ------ | ------------------------------------------------ | ------------------------------------ | -------------------------------------------------- |
| POST   | `/tenant/payments/public-search`                 | `tenant.payments.public-search`      | `publicSearch` missing                             |
| GET    | `/tenant/payments/history/{reference}`           | `tenant.payments.public-history`     | `publicHistory` missing                            |
| GET    | `/tenants/property/{property}/registration-link` | `landlord.tenants.registration-link` | `registrationLink` missing; **no auth middleware** |




### Admin auth


| Method | URL             | Name                | Action    | Auth         |
| ------ | --------------- | ------------------- | --------- | ------------ |
| GET    | `/admin/login`  | `admin.login`       | `create`  | Guest        |
| POST   | `/admin/login`  | `admin.login.store` | `store`   | Guest        |
| POST   | `/admin/logout` | `admin.logout`      | `destroy` | `auth:admin` |




### Admin (middleware `auth:admin`, `role:super_admin`, prefix `/admin`)


| Method       | URL                                                          | Name                                 | Action                            | Purpose             |
| ------------ | ------------------------------------------------------------ | ------------------------------------ | --------------------------------- | ------------------- |
| GET          | `/admin/dashboard`                                           | `admin.dashboard`                    | `Admin\DashboardController@index` | Stats               |
| GET/POST     | `/admin/landlords`                                           | `admin.landlords.index/store`        | LandlordController                | List / create       |
| GET          | `/admin/landlords/create`                                    | `admin.landlords.create`             | `create`                          | Form                |
| GET          | `/admin/landlords/{landlord}`                                | `admin.landlords.show`               | `show`                            | Detail              |
| GET/PUT      | `/admin/landlords/{landlord}/edit`                           | `admin.landlords.edit/update`        | edit/update                       | Edit                |
| DELETE       | `/admin/landlords/{landlord}`                                | `admin.landlords.destroy`            | `destroy`                         | Soft delete         |
| PATCH        | `/admin/landlords/{landlord}/status`                         | `admin.landlords.status`             | `toggleStatus`                    | Activate/deactivate |
| POST         | `/admin/landlords/{landlord}/reset-password`                 | `admin.landlords.reset-password`     | `resetPassword`                   | Reset password      |
| GET          | `/admin/landlords/trashed`                                   | `admin.trash.landlords`              | `trashed`                         | Archive list        |
| PATCH        | `/admin/landlords/trashed/{landlord}/restore`                | `admin.trash.landlords.restore`      | `restore`                         | Restore             |
| DELETE       | `/admin/landlords/trashed/{landlord}/force-delete`           | `admin.trash.landlords.force-delete` | `forceDelete`                     | Permanent delete    |
| resource     | `/admin/properties`                                          | `admin.properties.*`                 | PropertyController                | CRUD                |
| GET          | `/admin/properties/trashed`                                  | `admin.trash.properties`             | `trashed`                         | Archive             |
| PATCH/DELETE | `/admin/properties/trashed/{public_id}/restore|force-delete` | trash restore/force-delete           | restore/forceDelete               | Archive actions     |
| GET          | `/admin/tenants`                                             | `admin.tenants.index`                | `index`                           | List                |
| GET          | `/admin/tenants/export`                                      | `admin.tenants.export`               | `export`                          | PDF                 |
| DELETE       | `/admin/tenants/{tenant}`                                    | `admin.tenants.destroy`              | `destroy`                         | Soft delete         |
| GET          | `/admin/tenants/trashed`                                     | `admin.trash.tenants`                | `trashed`                         | Archive             |
| GET          | `/admin/settings`                                            | `admin.settings.index`               | `index`                           | Settings            |
| POST         | `/admin/settings/backup`                                     | `admin.settings.backup`              | `backup`                          | Create SQL dump     |
| GET          | `/admin/settings/download/{file}`                            | `admin.settings.download`            | `download`                        | Download dump       |
| DELETE       | `/admin/settings/delete/{file}`                              | `admin.settings.delete`              | `delete`                          | Delete dump         |




### Landlord auth


| Method | URL                                | Name                        | Action      | Auth            |
| ------ | ---------------------------------- | --------------------------- | ----------- | --------------- |
| GET    | `/landlord/login`                  | `landlord.login`            | `create`    | Guest           |
| POST   | `/landlord/login`                  | `landlord.login.store`      | `store`     | Guest           |
| POST   | `/landlord/logout`                 | `landlord.logout`           | `destroy`   | `auth:landlord` |
| GET    | `/landlord/password/reset`         | `landlord.password.request` | forgot form | Guest           |
| POST   | `/landlord/password/email`         | `landlord.password.email`   | send link   | Guest           |
| GET    | `/landlord/password/reset/{token}` | `landlord.password.reset`   | reset form  | Guest           |
| POST   | `/landlord/password/reset`         | `landlord.password.update`  | reset       | Guest           |




### Landlord (middleware `auth:landlord`, `role:landlord`, prefix `/landlord`)


| Method   | URL                                                   | Name                                      | Action                        | Purpose                  |
| -------- | ----------------------------------------------------- | ----------------------------------------- | ----------------------------- | ------------------------ |
| GET      | `/landlord/dashboard`                                 | `landlord.dashboard`                      | DashboardController@index     | Stats                    |
| resource | `/landlord/properties`                                | `landlord.properties.*`                   | PropertyController            | CRUD hostels             |
| GET      | `/landlord/properties/trashed`                        | `landlord.properties.trashed`             | `trashed`                     | Archived hostels         |
| PATCH    | `/landlord/properties/{public_id}/restore`            | `landlord.properties.restore`             | `restore`                     | Restore hostel           |
| PATCH    | `/landlord/properties/{property}/status`              | `landlord.properties.status`              | `toggleStatus`                | Active flag              |
| PATCH    | `/landlord/properties/{property}/toggle-registration` | `landlord.properties.toggle-registration` | `toggleRegistration`          | Open/close registration  |
| GET      | `/landlord/properties/{property}/export-pdf`          | `landlord.properties.export.pdf`          | `exportPdf`                   | PDF (working)            |
| GET      | `/landlord/properties/{property}/tenants`             | `landlord.properties.tenants`             | TenantController@showProperty | Alternate tenant list    |
| GET      | `/landlord/properties/{property}/export-property-pdf` | `landlord.properties.export.property.pdf` | `exportPropertyPdf`           | PDF (**missing view**)   |
| resource | `/landlord/tenants`                                   | `landlord.tenants.*`                      | TenantController              | Tenant CRUD              |
| GET      | `/landlord/tenants/trashed`                           | `landlord.tenants.trashed`                | `trashed`                     | Archived tenants         |
| PATCH    | `/landlord/tenants/{public_id}/restore`               | `landlord.tenants.restore`                | `restore`                     | Restore tenant           |
| POST     | `/landlord/tenants/generate-link`                     | `landlord.tenants.generate-link`          | `generateRegistrationLink`    | JSON registration URL    |
| POST     | `/landlord/tenants/copy-link`                         | `landlord.tenants.copy-link`              | `copyRegistrationLink`        | **method missing**       |
| PATCH    | `/landlord/tenants/{tenant}/move-out`                 | `landlord.tenants.moveout`                | `moveOut`                     | Soft-delete as moved out |
| PATCH    | `/landlord/tenants/{tenant}/reactivate`               | `landlord.tenants.reactivate`             | `reactivate`                  | Set status active        |
| GET      | `/landlord/tenants/export/pdf`                        | `landlord.tenants.export.pdf`             | `exportPdf`                   | PDF                      |
| GET      | `/landlord/tenants/export/excel`                      | `landlord.tenants.export.excel`           | `exportExcel`                 | **method missing**       |
| GET      | `/landlord/payments`                                  | `landlord.payments.index`                 | PaymentController@index       | Payment inbox            |
| GET      | `/landlord/payments/{payment}`                        | `landlord.payments.show`                  | `show`                        | Detail + screenshot      |
| PATCH    | `/landlord/payments/{payment}/approve`                | `landlord.payments.approve`               | `approve`                     | Approve                  |
| PATCH    | `/landlord/payments/{payment}/reject`                 | `landlord.payments.reject`                | `reject`                      | Reject                   |


---



## Main Workflows

Only workflows that are implemented are listed.

### Admin creates a landlord

1. Admin logs in at `/admin/login`.
2. Admin opens Landlords → create form.
3. Admin submits name, username, email, phone (optional second phone).
4. System hashes a random 10-character password, sets `role = landlord`, `status = true`, `is_active = true`.
5. System emails `LandlordWelcomeMail` with the username and temporary password.
6. Landlord appears on the landlords index.



### Landlord logs in

1. Landlord opens `/` → Landlord, or goes directly to `/landlord/login`.
2. Enters username or email and password.
3. System authenticates on the `landlord` guard, then verifies `role === 'landlord'` and `is_active`.
4. `last_login_at` is updated.
5. Redirect to `/landlord/dashboard`.



### Landlord creates a hostel

1. Landlord opens Hostels → create.
2. Submits name, optional address/description, monthly rent, max tenants.
3. System sets `landlord_id`, `status = true`, `registration_open = true`, generates `registration_token` and `public_id`.
4. Hostel appears on the hostels list.



### Tenant registration (self-service)

1. Landlord opens **Add Tenant**, selects a hostel, clicks generate link (`POST landlord.tenants.generate-link`).
2. Landlord copies the returned URL and shares it.
3. Tenant opens `/register/property/{token}`.
4. If the hostel is inactive → 404. If registration is closed → closed page. If full → full page.
5. Tenant submits name, email, Malawi phone, move-in date, optional custom rent.
6. System validates, normalizes phone, checks duplicates, locks the property row, creates the tenant with status `'active'` and a `TEN-XXXXXXXX` code.
7. Tenant sees the success page with the tenant code.
8. Landlord sees the tenant on the hostel show page (and in tenant counts).



### Landlord creates a tenant manually

1. Landlord opens Add Tenant and fills the form (property, name, email, phone, move-in date).
2. System validates Malawi phone and per-property uniqueness, rejects if the hostel is full.
3. Tenant is created with status `'active'`, rent copied from the property, and a 6-character `tenant_code` from `Tenant::boot()`.
4. Redirect to the hostel show page.



### Tenant submits a payment

1. Tenant opens `/tenant/payments` → Record Payment.
2. Enters tenant code, starting month, number of months, amount, and screenshot.
3. System looks up the tenant, builds a comma-separated month list, requires amount = rent × month count, rejects if any of those months already has Pending or Approved payment.
4. Screenshot is stored in `public/payments/`. Payment row is created with status `Pending`.
5. Landlord sees it under Payments and can approve or reject.



### Landlord processes a payment

1. Landlord opens Payments (optional filters: hostel, status, month).
2. Opens a payment, views screenshot.
3. Approve → `status = Approved`, `approved_by`, `approved_at`.
4. Reject → `status = Rejected`, optional remarks, same auditor fields.
5. Already processed payments cannot be processed again.
6. Hostel tenant list Paid/Unpaid updates based on Approved months.



### Archive / restore (landlord)

1. Landlord archives a hostel or tenant (soft delete).
2. Record disappears from normal lists and appears under Archive.
3. Restore uses `public_id`.

Move out: sets `status = 'moved_out'`, attempts `move_out_date` (not fillable), then soft-deletes the tenant.

---



## Payment Workflow



### Recording

- Only the **tenant public form** creates payments. Landlords do not have a “record payment” form.
- Required: `tenant_code`, `payment_month` (`Y-m`), `amount`, image screenshot.
- Optional: `month_count` (default 1, max 12). Consecutive months are generated from the start month.



### Storage

- Row in `payments`.
- `payment_month` is a comma-separated list of `Y-m` tokens.
- `screenshot` is a filename; file path is `public/payments/{filename}`.
- Default status `Pending` (model boot + explicit create).



### Calculation

```
expectedAmount = tenant.monthly_rent * month_count
```

The submitted amount must match (rounded to 2 decimals). There is no proration and no outstanding-balance ledger.

### Reflection in tenant payment status


| Layer               | How status appears                                                                                          |
| ------------------- | ----------------------------------------------------------------------------------------------------------- |
| Payment record      | `Pending` / `Approved` / `Rejected`                                                                         |
| Tenant history page | Those same statuses                                                                                         |
| Hostel tenant list  | **Paid** if an Approved payment’s month list contains the selected (or current) month; otherwise **Unpaid** |


Pending payments do **not** count as Paid. Rejected payments do not count as Paid and do not block a new submission for the same month (only Pending and Approved block duplicates).

---



## Notification System

This project does **not** use Laravel’s database notifications table for in-app alerts. There is no mark-as-read API.

### Admin “notifications”

`resources/views/components/admin/topbar.blade.php` builds a “Recent Activity” dropdown by querying:

- Latest 5 properties (message: `{landlord} added property "{name}"`)
- Latest 5 tenants (message: `{landlord} registered tenant "{name}"`)

Sorted by `created_at`, capped at 10. The badge is the count of those rows, not unread state. Closing the dropdown does not clear anything.

### Landlord in-app notifications

**Not currently implemented.** The landlord header has no notification list.

### Email notifications


| Event                            | Channel     | File                                                                                         |
| -------------------------------- | ----------- | -------------------------------------------------------------------------------------------- |
| Admin creates landlord           | Mail (sync) | `app/Mail/LandlordWelcomeMail.php`, view `resources/views/emails/landlord-welcome.blade.php` |
| Landlord requests password reset | Mail        | `app/Notifications/LandlordResetPasswordNotification.php`                                    |


No emails are sent for tenant registration, payment submit, approve, or reject.

---



## PDF & Email Features



### PDF

Package: `barryvdh/laravel-dompdf` (`config/dompdf.php`).

Usage pattern:

```php
$pdf = Pdf::loadView('exports.tenants-pdf', [...]);
$pdf->setPaper('A4', 'landscape');
return $pdf->download($filename);
```

Controllers: `Landlord\PropertyController::exportPdf`, `Landlord\TenantController::exportPdf`, `Admin\TenantController::export`.

DomPDF needs writable `storage/fonts` on first run. Missing PHP extensions (`ext-gd` / `ext-mbstring`) cause generation failures.

### Email

- Config: `config/mail.php`, driven by `MAIL_*` env vars.
- Local default: `MAIL_MAILER=log` (no real email).
- Production: set SMTP (or another real mailer) and a valid `MAIL_FROM_ADDRESS`.
- `APP_URL` must be correct or password-reset links will point at the wrong host.

Welcome mail is **not** queued (`Mail::send`). `ShouldQueue` is not implemented on `LandlordWelcomeMail` despite `Queueable`.

---



## Deployment



### Server requirements (production)

- PHP 8.2+ with Laravel’s required extensions
- Composer
- Node.js + npm **on the machine that builds assets** (or build elsewhere and upload `public/build`)
- MySQL recommended (admin backup is MySQL-specific)
- Web server: Apache (`public/.htaccess`) or Nginx pointing document root at `public/`
- Write access to `storage/`, `bootstrap/cache/`, `public/payments/`, `storage/app/backups/`



### Upload / clone

Clone or upload the project **excluding** `.env`. Never deploy `APP_DEBUG=true` with a public site.

### Composer (production)

```bash
composer install --no-dev --optimize-autoloader
```



### `.env`

Set at least:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.example
DB_CONNECTION=mysql
# DB_* credentials
MAIL_MAILER=smtp
# MAIL_* credentials
SESSION_DRIVER=database
```

Generate `APP_KEY` on the server if it does not already exist. Do not reuse a development key in documentation or tickets.

### Database

Create the MySQL database, then:

```bash
php artisan migrate --force
```

Only run seeders if you intend to create the Spatie roles / super admin row, and only after fixing `SuperAdminSeeder` (role + `is_active` + `HasRoles` issue).

### Storage permissions

```bash
php artisan storage:link
```

Ensure:

- `storage/` and `bootstrap/cache/` are writable by the web user
- `public/payments/` exists and is writable (screenshots)
- `storage/app/backups/` is writable (admin backups) and **not** publicly served



### Cache optimization

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

After Blade/layout deploys, if pages look stale:

```bash
php artisan view:clear
```



### Frontend assets

```bash
npm ci
npm run build
```

Commit or upload `public/build/` (Vite manifest). Missing `public/build/manifest.json` breaks `@vite` pages.

Admin login still depends on the Tailwind CDN if its view is used as-is; other pages need the Vite build.

### Web server

Document root **must** be `public/`, not the project root.

Apache: `public/.htaccess` already rewrites to `index.php`.

Nginx (typical):

```nginx
root /var/www/project/public;
index index.php;
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```



### PHP configuration

- `upload_max_filesize` / `post_max_size` large enough for payment screenshots (validation allows up to 102400 KB)
- `memory_limit` high enough for DomPDF and admin SQL backups
- `max_execution_time` high enough for backups of large databases



### Queue / cron

**Not required** for the currently implemented features.

- `routes/console.php` has no scheduled tasks.
- Welcome mail is synchronous.
- `QUEUE_CONNECTION=database` is unused by application jobs.

If you later queue mail, run `php artisan queue:work` and a cron entry for `php artisan schedule:run`.

### SSL

Serve the site over HTTPS. Set `APP_URL` to the `https://` origin so password-reset and registration links are correct. Optionally set secure session cookies via Laravel session config / env once HTTPS is in place.

### Must be done before this project is production-ready

1. Ensure `users.role`, `users.is_active`, `users.last_login_at`, and all `deleted_at` columns exist (they are used but not migrated in this repo).
2. Fix or skip broken routes (`exportExcel`, `copyRegistrationLink`, `publicSearch`, `publicHistory`, `registrationLink`, missing `property-tenants-pdf` view).
3. Fix SuperAdmin seeding so admin login can succeed (`role` + `is_active`).
4. Point mail at a real SMTP server if landlords must receive welcome/reset emails.
5. Create `public/payments`.
6. Set `APP_DEBUG=false` and a real `APP_KEY`.
7. Restrict `/payments/{filename}` in production if screenshots should not be guessable (currently public if the filename is known).
8. Do not expose seeder default passwords.

---



## Troubleshooting



### Route not found (404)

- **Cause:** Wrong URL, `route:cache` stale, or hitting a route that was never registered.
- **Diagnose:** `php artisan route:list --name=landlord` (or `admin` / `tenant`).
- **Fix:** Confirm the name in `routes/web.php`. After deploy, `php artisan route:cache` or `route:clear`.



### View not found

- **Cause:** `View [exports.property-tenants-pdf] not found` or similar.
- **Diagnose:** Compare `Pdf::loadView(...)` / `view(...)` names with files under `resources/views`.
- **Fix:** Use `exports.tenants-pdf` or `admin.tenants.export`. Do not call `landlord.properties.export.property.pdf` until that template exists.



### Undefined variable

- **Cause:** Controller did not `compact()` a variable the Blade file expects.
- **Diagnose:** Stack trace in `storage/logs/laravel.log` with `APP_DEBUG=true` locally.
- **Fix:** Pass the variable or remove it from the view.



### Database errors / missing column

- **Cause:** Models use `role`, `is_active`, `deleted_at`, `public_id`, etc. Missing from a fresh migrate.
- **Diagnose:** Compare error column name with migrations vs `User`/`Property`/`Tenant` fillable lists.
- **Fix:** Add a proper migration (do not edit old migrations that already ran in production). Duplicate payment-column migration will fail on fresh installs — skip or guard it with `hasColumn`.



### Unique constraint / phone errors

- **Cause:** Conflicting unique indexes on `tenants.phone` vs `(property_id, phone)`.
- **Diagnose:** `SHOW INDEX FROM tenants;` (MySQL).
- **Fix:** Align indexes with the application rule (unique phone **per property**).



### Permission / 403

- **Cause:** Wrong role, `is_active = false`, or landlord accessing another landlord’s `public_id`.
- **Diagnose:** Log lines from `RoleMiddleware`; confirm `users.role` and `properties.landlord_id`.
- **Fix:** Correct the user row or use a record the landlord owns. Inactive landlords cannot use the portal.



### Authentication problems

- **Cause:** Admin login requires `role = super_admin` and `is_active = 1`. Seeder may not set them. Wrong guard (admin vs landlord). Session driver/table missing.
- **Diagnose:** Attempt credentials against the correct URL. Check `sessions` table if `SESSION_DRIVER=database`.
- **Fix:** Set role/active flags. `php artisan migrate` for sessions. Confirm `APP_KEY` and cookie domain.



### Missing build files / Vite manifest

- **Cause:** `npm run build` not run; `public/build` not deployed.
- **Diagnose:** Error mentioning `ViteManifestNotFoundException` or missing `/build/assets`.
- **Fix:** `npm install && npm run build`. Deploy `public/build`.



### Missing PHP / Node dependencies

- **Cause:** `composer install` or `npm install` skipped; missing `ext-gd` for PDF; missing `doctrine/dbal` for `->change()` migrations.
- **Diagnose:** Composer/npm errors; `php -m`.
- **Fix:** Install extensions and run Composer/npm.



### Storage permission problems

- **Cause:** Web user cannot write `storage/`, `bootstrap/cache/`, or `public/payments/`.
- **Diagnose:** Laravel log “Permission denied”; screenshots fail on payment submit.
- **Fix:** Create directories and grant write permission to the PHP/web user.



### Email problems

- **Cause:** `MAIL_MAILER=log`; invalid SMTP; `APP_URL` wrong; seeder/create path errors before mail is sent.
- **Diagnose:** `storage/logs/laravel.log`; with `log` mailer, messages appear in the log file instead of inboxes.
- **Fix:** Configure SMTP. Confirm `LandlordWelcomeMail` view exists. Landlord forgot-password always shows success even if no user exists.



### PDF generation problems

- **Cause:** Missing view, empty `$tenants`, DomPDF font/cache permissions, memory limit.
- **Diagnose:** Log from `Admin\TenantController::export` or landlord export methods.
- **Fix:** Ensure the Blade template exists; `chmod` `storage/fonts`; raise `memory_limit`.



### Registration link does not work

- **Cause:** Wrong token; property `status = false`; `registration_open = false`; hostel full; production `APP_URL` mismatch.
- **Diagnose:** Look up `properties.registration_token`, `status`, `registration_open`, tenant count vs `max_tenants`.
- **Fix:** Generate a new link, open registration, or raise capacity.



### Hamburger / CSS missing in production

- **Cause:** Tailwind classes missing from the built CSS if `npm run build` is stale; views cached.
- **Diagnose:** Compare local `npm run dev` vs production `public/build`.
- **Fix:** Rebuild assets; `php artisan view:clear`.

---



## Extending the System

Follow existing conventions. Prefer landlord ownership checks and `public_id` for tenant-facing URLs.

### New model

1. `php artisan make:model Name -m`
2. Add `$fillable`, casts, relationships.
3. If the record should appear in URLs, `use HasPublicId` like `Property` / `Tenant` / `Payment` (`app/Models/Traits/HasPublicId.php`).
4. Add `deleted_at` in the **same** migration if you `use SoftDeletes` (do not repeat the current gap).



### Migration

```bash
php artisan make:migration create_example_table
php artisan migrate
```

Guard `hasColumn` when altering tables that already diverged in production. Never assume `role` / `deleted_at` migrations exist.

### Controller

Place under `app/Http/Controllers/Admin` or `Landlord` or `Tenant`. Mirror ownership:

```php
abort_if($property->landlord_id !== Auth::guard('landlord')->id(), 403);
```



### Route

Add to the correct group in `routes/web.php` (the only HTTP route file). Put static paths (`trashed`, `export`) **before** `Route::resource` so they are not captured as `{id}`.

Do not add a controller method name to a route unless the method exists (`exportExcel` is the anti-pattern to avoid).

### Blade view

- Admin: `@extends('layouts.admin')`
- Landlord: `@extends('layouts.landlord')` and add a sidebar link in `resources/views/landlord/partials/sidebar.blade.php`
- Tenant/public: standalone HTML with `@vite` like `resources/views/tenant/payments/index.blade.php`



### Form validation

Prefer a Form Request under `app/Http/Requests/...` (see `StorePropertyRequest`). Landlord/tenant flows currently validate inline; either style is present. Keep Malawi phone checks on `Tenant::isValidMalawiPhone()`.

### Role / permission

To add a new staff role with the **current** system:

1. Store the string on `users.role`.
2. Add a guard or reuse `web`.
3. Register a route group with `role:new_role`.
4. Update `RoleMiddleware` only if extra checks are needed.

Do **not** call `$user->assignRole()` until `User` uses `HasRoles` and you have decided Spatie is the source of truth (today it is not).

### Notification

For email, add a Mailable or Notification next to `LandlordWelcomeMail` / `LandlordResetPasswordNotification`.

For in-app landlord alerts, there is no existing table or unread flag. You would be adding a new module, not extending a complete one. Do not assume the admin topbar pattern is a notification system.

### PDF export

Copy `Landlord\PropertyController::exportPdf`: query the data, `Pdf::loadView(...)`, landscape A4, `download()`. Put the Blade file under `resources/views/exports/`.

### New module (suggested order)

1. Migration + model
2. Controller
3. Routes in the correct middleware group
4. Views + sidebar link
5. Validation
6. Ownership / role checks
7. Feature test if you add tests (existing tests are mostly Breeze leftovers and may not match this app)

---



## Known Issues / Technical Debt

1. `users.role`**,** `is_active`**,** `last_login_at`**,** `deleted_at` **have no migrations** in this repo but are required by login and Eloquent.
2. **SoftDeletes on Property/Tenant/User** without `deleted_at` migrations. Queries use `whereNull('deleted_at')`.
3. **Spatie Permission is installed and seeded but unused at runtime.** `User` has no `HasRoles`. `SuperAdminSeeder::assignRole` and `User::role('Landlord')` in admin property create are inconsistent with that.
4. **SuperAdminSeeder does not set** `role` **or** `is_active`, so admin login can fail after seeding.
5. **Unused** `landlords` **table.**
6. **Duplicate / conflicting migrations:** payment columns added twice; tenant `status` added twice (one guarded); phone unique vs composite unique; `2026_01_15` unique-constraint migration may fail on a true fresh install.
7. **Tenant status casing:** DB default `'Active'`; writes use `'active'`; dashboard counts `'Active'`.
8. **Two tenant code formats:** `TEN-` + 8 chars vs 6-char codes.
9. `move_out_date` **not fillable and not migrated.**
10. **Routes without controller methods:** `copyRegistrationLink`, `exportExcel`, `publicSearch`, `publicHistory`, `registrationLink`.
11. **Missing PDF view** `exports.property-tenants-pdf`.
12. **Unauthenticated registration-link route** in “Additional Routes”.
13. **Admin tenant/property restore** looks up numeric `id` while the URL parameter is `public_id`.
14. **Admin Tenant resource** registers CRUD methods that do not exist.
15. **Admin landlord** `store()` calls `DB::commit()` without `beginTransaction()`.
16. `LandlordService` **/** `PasswordGenerator` **/** `SoftDeleteTrait` **unused** (or unused by the live landlord-create path).
17. **Payment** `submitted_at` **not set** on create. Logs reference `$payment->reference`, which is not a column.
18. **Admin settings download/delete unused by the current Blade file.** Backup is MySQL-only.
19. **Hard-coded month ranges** (payments filter Aug 2026–Dec 2027; property show months Jan 2026–Dec 2027).
20. **No outstanding-balance or partial-payment support.**
21. **Public payment screenshots** if the filename is known.
22. **Sanctum, Excel package, tenant guard, Breeze leftover tests/views** are not part of the running product.
23. **Queue connection configured but unused.**
24. **Admin login view uses Tailwind CDN**; other pages depend on Vite.

---



## Important File Reference


| Path                                                      | What it controls                                            |
| --------------------------------------------------------- | ----------------------------------------------------------- |
| `routes/web.php`                                          | Every HTTP route                                            |
| `bootstrap/app.php`                                       | `role` middleware alias, guest redirects, no API routes     |
| `config/auth.php`                                         | Guards `web`, `admin`, `landlord`, `tenant`; all use `User` |
| `config/mail.php`                                         | Mail transport                                              |
| `config/dompdf.php`                                       | PDF renderer                                                |
| `app/Http/Middleware/RoleMiddleware.php`                  | Role + `is_active` gate                                     |
| `app/Models/User.php`                                     | Staff accounts, `hasRole()`, password reset notification    |
| `app/Models/Property.php`                                 | Hostels, capacity, registration token/link                  |
| `app/Models/Tenant.php`                                   | Tenant codes, phone normalization, paid-for-month helpers   |
| `app/Models/Payment.php`                                  | Payment statuses and month lists                            |
| `app/Models/Traits/HasPublicId.php`                       | ULID route keys                                             |
| `app/Http/Controllers/Auth/AdminLoginController.php`      | Admin login                                                 |
| `app/Http/Controllers/Auth/LandlordLoginController.php`   | Landlord login                                              |
| `app/Http/Controllers/Admin/LandlordController.php`       | Landlord CRUD, welcome email, password reset                |
| `app/Http/Controllers/Admin/PropertyController.php`       | Admin properties                                            |
| `app/Http/Controllers/Admin/TenantController.php`         | Admin tenant list + PDF                                     |
| `app/Http/Controllers/Admin/SettingsController.php`       | MySQL SQL backups                                           |
| `app/Http/Controllers/Landlord/PropertyController.php`    | Hostels, Paid/Unpaid filter, working PDF export             |
| `app/Http/Controllers/Landlord/TenantController.php`      | Tenants, registration link JSON, move-out                   |
| `app/Http/Controllers/Landlord/PaymentController.php`     | Approve/reject payments                                     |
| `app/Http/Controllers/TenantRegistrationController.php`   | Public tenant signup                                        |
| `app/Http/Controllers/Tenant/PaymentController.php`       | Public payment submit/history                               |
| `app/Mail/LandlordWelcomeMail.php`                        | Welcome email                                               |
| `app/Notifications/LandlordResetPasswordNotification.php` | Reset email                                                 |
| `database/seeders/SuperAdminSeeder.php`                   | Default admin user (inspect before use)                     |
| `database/seeders/RolesSeeder.php`                        | Spatie role names                                           |
| `resources/views/layouts/admin.blade.php`                 | Admin chrome                                                |
| `resources/views/layouts/landlord.blade.php`              | Landlord chrome / mobile sidebar state                      |
| `resources/views/home.blade.php`                          | Public landing (Alendi Estates)                             |
| `resources/css/app.css`                                   | Tailwind entry + project CSS                                |
| `resources/js/app.js`                                     | Alpine.js startup                                           |
| `vite.config.js`                                          | Frontend build inputs                                       |
| `public/index.php`                                        | Front controller                                            |
| `public/.htaccess`                                        | Apache rewrite                                              |
| `public/payments/`                                        | Payment screenshots                                         |
| `.env.example`                                            | Documented environment defaults                             |


---

*Generated from the codebase in this workspace. If the code and this file disagree, the code wins.*