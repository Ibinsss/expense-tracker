

````markdown
# 💸 Expense Tracker (Laravel)

Welcome! This is a simple **Expense Tracker** app built with **Laravel**. It's designed to help you keep track of your monthly spending — by category, with totals, breakdowns, and an option to email your report. This is my learning project, so it’s clean, simple, and beginner-friendly. 😊

---

## 🛠️ Features

- 📆 View expenses by month
- 📊 Breakdown by category (with a nice pie chart!)
- 🧾 Add, edit, or delete your expenses
- 🔐 User-friendly and secure

---

## 🚀 Getting Started

Here’s how to set it up on your local machine.

### 1. Clone this repo

```bash
git clone https://github.com/Ibinsss/expense-tracker.git
cd expense-tracker
```

### 2. Install PHP dependencies

Make sure you have PHP, Composer, and Laravel installed. Then run:

```bash
composer install
```

### 3. Copy the example environment file

```bash
cp .env.example .env
```

### 4. Set up your `.env` file

Open the `.env` file and  Your database info

* App name

> Don’t worry! You can just start with:

```dotenv
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=yourpassword
```

### 5. Generate app key

```bash
php artisan key:generate
```

### 6. Run migrations

```bash
php artisan migrate
```

### 7. (Optional) Link storage for uploaded files

```bash
php artisan storage:link
```

### 8. Start the local server

```bash
php artisan serve
```

Now visit:
👉 `http://localhost:8000`
--------------------------

---

## 📦 Tech Stack

* **Laravel 10++**
* **Blade Templating**
* **MySQL** (or SQLite)

* **Chart.js** for visual breakdowns

---

