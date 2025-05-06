# Expense Tracker – Laravel 12

A lightweight personal‑finance web app that lets you record day‑to‑day expenses, attach images/PDF receipts, and see neat monthly breakdowns.

---

## ✨ Features

| Area | What you get |
|------|--------------|
| **Authentication** | Register / log‑in / log‑out with hashed passwords (Laravel Breeze). |
| **Expenses CRUD** | • Create / edit / delete expenses<br>• Fields: *title, amount, date, category, notes*.<br>• Optional receipt upload (images or PDF). |
| **Receipt storage** | ✔ Local `public/storage` in development.<br>✔ Amazon S3 in production (Heroku). |
| **Dashboard** | Greets the user and lists the 5 most‑recent expenses. |
| **Monthly view** | Expenses grouped by **Month YYYY** with a subtotal. |
| **Per‑month breakdown** | Pie‑chart & table of totals **by category**. |
| **E‑mail reports** | One‑click “Send me this breakdown” (SendGrid). |
| **Logging & caching** | Laravel log channels + 5‑minute query cache for heavy lists. |
| **Responsive UI** | Tailwind CSS + Vite build pipeline. |

---

## 🛠 Local Setup

```bash
# 1. clone & install
git clone https://github.com/<you>/expense‑tracker.git
cd expense‑tracker
composer install
npm install && npm run build        # builds Tailwind + JS

# 2. environment
cp .env.example .env
php artisan key:generate

# 3. database (SQLite by default)
touch database/database.sqlite
php artisan migrate --seed          # loads default user (see seed file)

# 4. storage link
php artisan storage:link

# 5. serve
php artisan serve
Open http://localhost:8000 – register a user and start adding expenses.

Deploying to Heroku