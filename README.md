# Expense Tracker 💸

A simple Laravel 12 + Vite + Tailwind app that lets authenticated users log expenses, 
upload and view receipts, group totals by month/category, and email monthly breakdowns via SendGrid.  
It can run **locally with MySQL + local storage** or **on Heroku with PostgreSQL + S3**.

<div align="center">
  <img src="public/screenshots/dashboard.png" width="700" alt="Dashboard screenshot">
</div>

---

## ✨ Features

|                           | Details |
|---------------------------|---------|
| Auth / sessions           | Breeze‑style routes (`/login`, `/register`, logout) |
| Expenses CRUD             | Title, amount, date, category, notes |
| Receipt upload            | Images **or** PDFs (max 5 MB) |
| Monthly dashboard         | List of months + totals |
| Per‑month breakdown       | Pie chart + category table |
| Email breakdown           | Sends HTML email through SendGrid |
| Storage switch            | Local disk in dev, S3 in production |
| CI‑ready                  | PHPUnit, Pint, Composer scripts |

---

## 🏗️ Prerequisites

| Tool                | Version |
|---------------------|---------|
| PHP                 | ≥ 8.2   |
| Composer            | ≥ 2.5   |
| Node + npm          | Node ≥ 18 / npm ≥ 9 |
| MySQL or MariaDB    | (Local dev) |
| [Heroku CLI](https://devcenter.heroku.com/articles/heroku-cli) | If you deploy |
| AWS account         | For S3 bucket (production) |
| SendGrid account    | For email (optional locally, required in prod) |

---

## 🚀 Local setup

```bash
git clone https://github.com/<your‑org>/expense-tracker.git
cd expense-tracker

# PHP deps
composer install

# Node deps
npm i
npm run dev &
