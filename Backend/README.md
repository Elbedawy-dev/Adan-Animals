# ADAN — Animal Disease Alert Network

A web-based early warning and disease surveillance platform for animal health management.
Built with Laravel 12 + Filament 3 + React (separate frontend).

## Project Overview

ADAN connects animal breeders (customers) with veterinarians (doctors) to:

- Track vaccine schedules for registered animals
- Report suspected disease cases
- Receive geo-based disease alerts when outbreaks are confirmed
- Manage geographic hierarchy (countries, governorates, cities, regions)

## Tech Stack

| Layer | Technology |
| --- | --- |
| Backend | Laravel 12 |
| Admin Panel | Filament 3 |
| API Auth | Laravel Sanctum |
| Media Uploads | Spatie Media Library 11 |
| Roles | Spatie Permission |
| Database | MySQL 8.0+ |
| Queue | Laravel Queue (database driver) |
| Notifications | Laravel Notifications (mail + database) |
| Frontend | React (separate project) |

## Installation

```bash
# 1. Clone & install
composer install
npm install

# 2. Configure environment
cp .env.example .env
php artisan key:generate

# 3. Set your DB credentials in .env
# DB_DATABASE=adan, DB_USERNAME=root, DB_PASSWORD=

# 4. Run migrations & seed
php artisan migrate
php artisan db:seed

# 5. Create storage symlink
php artisan storage:link

# 6. Run the application
php artisan serve

# 7. Start queue worker (separate terminal)
php artisan queue:work

# 8. Start scheduler (separate terminal)
php artisan schedule:work
```

## Test Accounts

| Role | Email | Password |
| --- | --- | --- |
| Doctor (Admin) | admin@adan.com | password |
| Doctor | doctor@adan.com | password |
| Customer | customer@adan.com | password |

## Admin Panel

URL: http://localhost:8000/admin  
Access: Doctors only (role = doctor)

## API Base URL

http://localhost:8000/api

## Complete API Reference

### Health Check

GET /api/health

### Authentication

| Method | Endpoint | Auth | Description |
| --- | --- | --- | --- |
| POST | /api/auth/register | No | Register new customer account |
| POST | /api/auth/login | No | Login with email + password |
| POST | /api/auth/send-otp | No | Send OTP to phone |
| POST | /api/auth/verify-otp | No | Verify OTP and get token |
| POST | /api/auth/logout | Yes | Revoke current token |
| GET | /api/auth/me | Yes | Get authenticated user profile |
| PUT | /api/auth/profile | Yes | Update authenticated user profile (name, email, phone, region_id) |

### Locations (Public)

| Method | Endpoint | Description |
| --- | --- | --- |
| GET | /api/locations/countries | List all countries |
| GET | /api/locations/countries/{id}/governorates | Governorates in a country |
| GET | /api/locations/governorates/{id}/cities | Cities in a governorate |
| GET | /api/locations/cities/{id}/regions | Regions in a city |

### Animals (Public)

| Method | Endpoint | Description |
| --- | --- | --- |
| GET | /api/animals/categories | All animal categories |
| GET | /api/animals/categories/{id} | Animals in a category with vaccines |
| GET | /api/animals/{id} | Single animal with vaccines |

### My Animals (Auth Required)

| Method | Endpoint | Description |
| --- | --- | --- |
| GET | /api/my-animals | List user's registered animals |
| POST | /api/my-animals | Register new animal + auto-generate vaccine schedule |
| GET | /api/my-animals/{id} | Get single animal details |
| DELETE | /api/my-animals/{id} | Remove animal from profile |

### Vaccine Schedules (Auth Required)

| Method | Endpoint | Description |
| --- | --- | --- |
| GET | /api/vaccine-schedules | All schedules (grouped by status) |
| PATCH | /api/vaccine-schedules/{id}/mark-done | Mark vaccine as administered |

### Disease Reports

| Method | Endpoint | Auth | Description |
| --- | --- | --- | --- |
| GET | /api/reports/approved | No | All approved reports (for map) |
| GET | /api/reports | Yes | User's own reports |
| POST | /api/reports | Yes | Submit new disease report |
| GET | /api/reports/{id} | Yes | Get single report |

### Notifications (Auth Required)

| Method | Endpoint | Description |
| --- | --- | --- |
| GET | /api/notifications | Paginated notifications |
| GET | /api/notifications/unread-count | Unread count |
| PATCH | /api/notifications/{id}/read | Mark single as read |
| POST | /api/notifications/mark-all-read | Mark all as read |

## Artisan Commands

```bash
# Send vaccine reminders manually
php artisan adan:vaccine-reminders

# Generate schedules for all animals
php artisan adan:generate-schedules

# Clear and re-seed
php artisan migrate:fresh --seed
```

## Scheduled Tasks

- Daily at 08:00 AM: SendVaccineRemindersJob (sends vaccine due reminders)

## Notification Types

| Type | Trigger | Channel |
| --- | --- | --- |
| Email verification | On register | Mail |
| vaccine_reminder | Daily scheduler | Mail + Database |
| disease_alert | Doctor approves report | Mail + Database |
| report_submitted | User submits report | Mail + Database |
| report_status | Doctor approves/rejects | Mail + Database |
