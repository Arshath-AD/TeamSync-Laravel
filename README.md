# TeamSync — Laravel Edition

A modern team collaboration and project management web app built with **Laravel 12** and **PHP 8.2**.

---

## Features

- **Dashboard** — Role-aware overview with task stats and workforce load distribution
- **Projects** — Full CRUD with per-project member management
- **Tasks** — Kanban board view, list view, and personal "My Tasks" view
- **Task Status** — Inline status updates (Pending → In Progress → Completed)
- **Role-based Access** — Admin and regular User roles with route-level guards
- **Auth** — Registration, login, email verification, and profile management via Laravel Breeze

---

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 12 / PHP 8.2 |
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

After seeding with `DemoSeeder`:

| Name | Email | Password | Role |
|---|---|---|---|
| Alex Chen | `admin@teamsync.test` | `password` | Admin |
| Jordan Lee | `jordan@teamsync.test` | `password` | User |
| Sam Rivera | `sam@teamsync.test` | `password` | User |
| Taylor Kim | `taylor@teamsync.test` | `password` | User |
| Morgan Blake | `morgan@teamsync.test` | `password` | User |

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

## License

MIT
