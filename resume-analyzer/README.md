# Capstone Web Project (23CSE404)

This repository contains a complete frontend + backend capstone web application aligned with the Web Technologies project requirements.

## Project Structure

- `frontend/` - static multi-page UI (GitHub Pages ready)
  - Pages: `index.html`, `about.html`, `features.html`, `data.html`, `login.html`, `contact.html`
  - Assets: shared responsive CSS + JavaScript interactivity
- `backend/` - PHP + MySQL application
  - `public/` - executable PHP pages
  - `config/db.php` - PDO database connection
  - `database/schema.sql` - MySQL schema
  - `includes/` - reusable layout and auth guard

## Features Implemented

### Frontend
- ChatGPT-inspired dark interface with modern card styling, spacing, and typography.
- Responsive design using Box Model, positioning, floats, and media queries.
- JavaScript features:
  - Interactive calculator
  - Live character counter
  - Client-side login form validation

### Backend (PHP)
- User registration and login with password hashing.
- Session management with protected dashboard page.
- Cookie management through a persistent theme cookie.
- Form handling with MySQL insertion.
- File upload with filename sanitization.

### Database (PHP + MySQL)
- CRUD minimum requirement covered (Create + Read):
  - Add and list products.
- Additional persistence:
  - User accounts
  - Contact messages

## Setup Instructions

### 1) Frontend Setup
- Open `frontend/index.html` directly in a browser, or
- Deploy `frontend` on GitHub Pages.

### 2) Backend Setup (XAMPP / Localhost)
1. Copy `backend` to your server web root (for example `htdocs`).
2. Start Apache and MySQL.
3. Import `backend/database/schema.sql` in phpMyAdmin.
4. Update DB credentials in `backend/config/db.php` if required.
5. Open `http://localhost/backend/public/index.php`.

## Tech Stack

- HTML5
- CSS3
- JavaScript
- PHP (PDO)
- MySQL