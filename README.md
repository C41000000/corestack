# MultiStore Hub API

> A robust, high-performance multi-tenant e-commerce core and catalog API built for modern micro-enterprises and franchises. Designed with strict data isolation, database schemas, and high-concurrency request handling.

---

## Tech Stack & Architecture

This project is built using cutting-edge tools to mirror a real-world enterprise SaaS backend:
* **Framework:** Laravel 13 (PHP 8.4)
* **High-Performance Server:** RoadRunner (HTTP/Worker Server via PHP Octane)
* **Database:** PostgreSQL 17 (Multi-tenancy via **Database Schemas** for absolute data isolation)
* **Environment:** Docker & Docker Compose (Multi-stage build, Alpine Linux)
* **Testing:** Pest PHP (Feature & Integration testing)
* **Static Analysis:** Larastan & Laravel Pint

---

## Architecture Highlights

* **Tenant Data Isolation:** Leverages PostgreSQL schemas (`schema_name.table_name`) per tenant rather than shared tables, ensuring secure and clean multi-tenancy.
* **Domain & Route Resolution:** Dynamic tenant identification via subdomains or custom HTTP headers (`X-Tenant`).
* **Clean Code Structure:** Domain-driven directory organization (`app/Domains/`) separating business logic, models, and infrastructure.

---

## 🛠️ Getting Started (Local Development)

Make sure you have **Docker** and **Docker Compose** installed on your machine.

1. **Clone the repository:**
   ```bash
   git clone [https://github.com/YOUR_USERNAME/your-repo-name.git](https://github.com/YOUR_USERNAME/your-repo-name.git)
   cd your-repo-name
   cp .env.example .env
   docker compose up -d --build
   docker compose exec app composer install
   docker compose exec app php artisan key:generate
   docker compose exec app php artisan migrate
   ```
   
## Running Tests
   ```bash
    docker compose exec app vendor/bin/pest
   ```

## License
Open-source project licensed under the MIT license.
