# Sembark URL Shortener

A multi-company, role-based URL Shortener web application built with Laravel 12, MySQL, Bootstrap 5, and Maatwebsite Excel.

---

## Overview & Roles

The system supports three user roles with strict authorization boundaries:

1. **Super Admin**
   - Platform owner.
   - Manages clients/companies (creates companies and invites client admins).
   - Views all generated short URLs across all companies with analytics (total hits).
   - **Cannot** generate short URLs (strictly restricted per business requirement).

2. **Client Admin**
   - Company owner / manager.
   - Views and creates short URLs for their own company.
   - Invites team members (Admins or Members) to their company.
   - Views company team members.

3. **Client Member**
   - Company team member.
   - Creates short URLs for their company.
   - Can only view their own generated short URLs and hit counts.

---

## Core Features

- **Public Redirection (`/links/{code}`)**: High-performance redirect that tracks click/hit count.
- **Invitation Flow**:
  - SuperAdmin invites new Clients (creates company & sends admin invitation).
  - Client Admin invites new team members (Admin or Member role).
  - Invited users open `/invitations/{token}` to set their name and password to activate their account.
- **DataTables AJAX Integration**: Fast server-side search, sorting, and pagination via Yajra DataTables.
- **Date Interval Filtering**: Filter URLs by `Today`, `Last Week`, `This Month`, or `Last Month`.
- **Excel & CSV Export**: Export filtered URL analytics directly to CSV via `Maatwebsite/Excel`.
- **Client-Side Validation**: Clean form validation using jQuery Validate and Bootstrap 5.

---

## Default Login Credentials

After seeding the database (`php artisan db:seed`), use these accounts to test:

| Role | Email | Password | Scope |
|---|---|---|---|
| **Super Admin** | `superadmin@sembark.com` | `12345678` | Sembark Tech |

---

## Installation & Setup

### Prerequisites
- PHP >= 8.2
- Composer
- MySQL >= 8.0 (or MariaDB in XAMPP)

### Step 1: Clone Repository & Install Dependencies
```bash
git clone <repository-url>
cd sembark-url-shortener
composer install
```

### Step 2: Environment Configuration
Copy `.env.example` to `.env`:
```bash
cp .env.example .env
php artisan key:generate
```

Configure your database settings in `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sembark_url_shortener
DB_USERNAME=root
DB_PASSWORD=
```

### Step 3: Run Migrations & Seeders
```bash
php artisan migrate:fresh --seed
```
*Note: As per assignment specifications, `SuperAdminSeeder` creates the initial SuperAdmin using raw SQL.*

### Step 4: Start Development Server
```bash
php artisan serve
```
Open your browser at `http://127.0.0.1:8000`.

---

## Running Automated Tests

Run the complete test suite including all 5 core assignment tests:
```bash
php artisan test
```

### Test Coverage Highlights:
- `test_member_and_admin_can_create_short_urls`
- `test_superadmin_cannot_create_short_urls`
- `test_admin_can_only_see_short_urls_created_in_their_own_company`
- `test_member_can_only_see_short_urls_created_by_themselves`
- `test_short_urls_are_publicly_resolvable_and_redirect_to_original_url`
- Full invitation and account activation lifecycle tests.
- Export feature tests for Member, Admin, and SuperAdmin.

---
