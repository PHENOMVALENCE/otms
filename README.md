# OTMS – Online Task Management System

A web-based task and leave management system with user and admin roles.

## Features

- **Users**: Register, log in, view/update assigned tasks, apply for leave, check leave status, receive notifications.
- **Admins**: Log in, create/edit/delete tasks, assign tasks to **one or more users**, approve/reject leave, view notifications.
- **Feedback**: Submit bug reports and feature requests from the landing page.

## Requirements

- PHP 7.4+ (mysqli)
- MySQL / MariaDB
- Web server (e.g. XAMPP, Apache)

## Setup

1. **Database**
   - Create a database named `otms`.
   - Import `SQL/otms.sql` for a fresh install.
   - If you already have an existing OTMS database, run `SQL/otms_schema_updates.sql` to add new columns and the feedback table.
   - To enable **multi-user task assignment**, run `SQL/task_assignments_migration.sql` once (adds `task_assignments` table and migrates existing data).

2. **Configuration**
   - Edit `includes/config.php` if your DB credentials differ (default: `root`, no password, `otms`).

3. **Web root**
   - Point your web server document root at this folder (or access via `http://localhost/otms/` with XAMPP).

## Default admin (fresh install)

- **Username:** `admin`
- **Password:** `admin123`

Change these after first login.

## Structure

- `index.php` – Landing, role selection, contact/feedback modals
- `user_login.php`, `register.php`, `success.php` – User auth
- `user_dashboard.php` – User home; loads `task.php`, `leaveForm.php`, `leave_status.php` via AJAX
- `update_status.php` – Update task status (standalone)
- `admin/admin_login.php`, `admin_dashboard.php` – Admin auth and home
- `admin/create_task.php`, `manage_task.php`, `edit_task.php`, `delete_task.php` – Task CRUD
- `admin/view_leave.php`, `approve_leave.php`, `reject_leave.php` – Leave management
- `submit_feedback.php` – Handles feedback form
- `logout.php` – Logout (users and admins)

## Theme

Warm, professional theme (light backgrounds, teal accents, Sora font). Styles in `css/style.css`.
