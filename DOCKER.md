# TeamSync Local Development Docker

This is the development Docker setup for TeamSync-Laravel.

## Ports

| Service | Host Port | Container Port |
|---------|-----------|----------------|
| Laravel app | **8080** | 8000 |
| MariaDB | **3307** | 3306 |

> Port 8080 is used instead of 8000 to avoid conflicts with a locally running `php artisan serve`.
> Port 3307 is used instead of 3306 to avoid conflicts with XAMPP's local MySQL.

## Initial Setup

1. Copy the docker environment file:
   ```bash
   cp .env.docker .env
   ```

2. Build and start the containers:
   ```bash
   docker compose up --build -d
   ```

3. Install Composer dependencies:
   ```bash
   docker compose exec app composer install
   ```

4. Generate Application Key:
   ```bash
   docker compose exec app php artisan key:generate
   ```

5. Run migrations:
   ```bash
   docker compose exec app php artisan migrate
   ```

6. Seed initial data (admin user + test project):
   ```bash
   docker compose exec app php artisan tinker
   ```
   Then paste:
   ```php
   App\Models\User::firstOrCreate(['email'=>'admin@apw.local'],['name'=>'Admin User','password'=>bcrypt('password'),'role'=>'admin']);
   ```

7. Open the app at **http://localhost:8080**

   Login credentials:
   - **Email:** `admin@apw.local`
   - **Password:** `password`

## Container Access

```bash
# Open a bash shell inside the Laravel container
docker compose exec app bash

# Run artisan commands
docker compose exec app php artisan tinker
docker compose exec app php artisan migrate
```

## Logs

```bash
# All services
docker compose logs -f

# App only
docker compose logs -f app

# Database only
docker compose logs -f db
```

## Shutdown

```bash
# Stop containers (preserves data)
docker compose down

# Stop and delete all data (WARNING: destroys the database volume)
docker compose down -v
```
