# TeamSync — Laravel Edition

A modern team collaboration and project management web app built with **Laravel 12** and **PHP 8.3**.

---

## Features

- **Dashboard** — Role-aware overview with task stats and workforce load distribution
- **Projects** — Full CRUD with project priorities and workspace views
- **Tasks** — Kanban board, list view, personal task management, and standalone tasks
- **Task Status** — Pending, In Progress, On Hold, Completed
- **Task Priorities** — Low, Medium, High, Critical
- **Team Management** — Project member assignment and workload tracking
- **Role-based Access** — Admin and User roles with route-level guards
- **Authentication** — Registration, login, email verification, profile management
- **Dockerized Development Environment**

---

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 12 / PHP 8.3 |
| Frontend | Blade + Vite + Tailwind CSS |
| Database | MariaDB / MySQL |
| Auth | Laravel Breeze |
| Dev Environment | Docker (Docker Compose) |

---

## Local Development (Docker)

### Prerequisites
- Docker & Docker Compose installed

### Setup

```bash
# 1. Clone the repo
git clone https://github.com/Arshath-AD/TeamSync-Laravel.git
cd TeamSync-Laravel

# 2. Copy the Docker environment file
cp .env.docker .env

# 3. Build and start containers
docker compose up --build -d

# 4. Install dependencies
docker compose exec app composer install

# 5. Generate app key
docker compose exec app php artisan key:generate

# 6. Run migrations
docker compose exec app php artisan migrate

# 7. Seed the database (creates Admin User and demo data)
docker compose exec app php artisan db:seed --class=DemoSeeder
```

### Access

| Service | URL |
|---|---|
| App | http://localhost:8080 |
| Database | `localhost:3307` |

---

## Default Credentials

| Name | Email | Password | Role |
|---|---|---|---|
| Admin User | `admin@apw.local` | `password` | Admin |
| Test User | `user@apw.local` | `password` | User |

> **Note:** Run `php artisan db:seed --class=DemoSeeder` to also load demo users (Alex Chen, Jordan Lee, etc.) with sample projects and tasks.

---

## Local Development (Without Docker)

```bash
# Install dependencies
composer install
npm install

# Set up environment
cp .env.example .env
php artisan key:generate

# Configure your DB in .env, then:
php artisan migrate
php artisan db:seed --class=DemoSeeder

# Start dev servers
php artisan serve
npm run dev
```

---

## Project Structure

```
app/
├── Http/Controllers/
│   ├── DashboardController.php
│   ├── ProjectController.php
│   ├── ProjectMemberController.php
│   └── TaskController.php
├── Models/
│   ├── User.php
│   ├── Project.php
│   ├── ProjectMember.php
│   └── Task.php
database/
├── migrations/
└── seeders/
    ├── DatabaseSeeder.php
    └── DemoSeeder.php
```

---

## Maintainer

Arshath AD

LinkedIn:
https://linkedin.com/in/arshathad

GitHub:
https://github.com/Arshath-AD

Portfolio:
https://arshath-ad.github.io

---

## License

This project is licensed under the [MIT License](LICENSE).
