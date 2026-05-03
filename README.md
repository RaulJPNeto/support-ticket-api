# Support Ticket API

RESTful API for enterprise-style support ticket management, built with Laravel and PostgreSQL.

---

## Tech Stack

- **Laravel** — PHP framework
- **PostgreSQL** — relational database
- **Docker** — containerized environment
- **Laravel Sanctum** — token-based authentication
- **Swagger / OpenAPI** — API documentation
- **Mailtrap** — email testing in development

---

## Architecture

```
Request → FormRequest → Controller → Service/Query → Model → Resource → Response
```

| Layer       | Responsibility            |
| ----------- | ------------------------- |
| Controller  | HTTP orchestration        |
| Service     | Write business logic      |
| Query       | Read, filters, pagination |
| FormRequest | Input validation          |
| Policy      | Authorization rules       |
| Resource    | Response transformation   |

---

## Roles & Access Control

| Role     | Permissions                                             |
| -------- | ------------------------------------------------------- |
| `CLIENT` | View and manage own tickets                             |
| `AGENT`  | View assigned or unassigned tickets                     |
| `ADMIN`  | Full access, including delete, restore and force delete |

---

## Enums

| Enum                | Values                                                                       |
| ------------------- | ---------------------------------------------------------------------------- |
| `TicketStatus`      | `OPEN`, `IN_PROGRESS`, `WAITING_CUSTOMER`, `RESOLVED`, `CLOSED`, `CANCELLED` |
| `TicketPriority`    | `LOW`, `MEDIUM`, `HIGH`, `URGENT`                                            |
| `TicketCategory`    | `INCIDENT`, `ACCESS`, `BUG`, `FEATURE_REQUEST`, `INFRASTRUCTURE`, `OTHER`    |
| `UserRole`          | `CLIENT`, `AGENT`, `ADMIN`                                                   |
| `SupportLevel`      | `N1`, `N2`, `N3`                                                             |
| `CommentVisibility` | `PUBLIC`, `INTERNAL`                                                         |

---

## API Endpoints

### Auth

| Method | Endpoint             | Auth |
| ------ | -------------------- | ---- |
| POST   | `/api/auth/register` | No   |
| POST   | `/api/auth/login`    | No   |
| POST   | `/api/auth/logout`   | Yes  |
| GET    | `/api/auth/me`       | Yes  |

### Tickets

| Method | Endpoint                         | Auth             |
| ------ | -------------------------------- | ---------------- |
| GET    | `/api/tickets`                   | Yes              |
| GET    | `/api/tickets/{id}`              | Yes              |
| POST   | `/api/tickets`                   | Yes              |
| PUT    | `/api/tickets/{id}`              | Yes              |
| DELETE | `/api/tickets/{id}`              | Yes (Admin only) |
| POST   | `/api/tickets/{id}/restore`      | Yes (Admin only) |
| DELETE | `/api/tickets/{id}/force-delete` | Yes (Admin only) |

All authenticated routes require:

```
Authorization: Bearer {token}
```

---

## Events & Notifications

The system dispatches events on key actions, triggering logs and email notifications:

| Event                 | Trigger                | Listeners                              |
| --------------------- | ---------------------- | -------------------------------------- |
| `TicketStatusChanged` | Status updated via PUT | Log to `laravel.log` + Email to client |
| `TicketDeleted`       | Ticket soft deleted    | Log to `laravel.log` + Email to client |

---

## Status History

Every status change is recorded in `ticket_status_histories` with:

```
ticket_id · from_status · to_status · changed_by · created_at
```

---

## API Documentation

Interactive documentation available via Swagger UI after starting the project:

```
http://localhost:8000/api/documentation
```

---

## Getting Started

### Requirements

- Docker
- Docker Compose

### Setup

```bash
# Clone the repository
git clone https://github.com/RaulJPNeto/support-ticket-api.git
cd support-ticket-api

# Copy environment file
cp .env.example .env

# Start containers
docker compose up -d

# Fix permissions
docker compose exec app chown -R www-data:www-data storage bootstrap/cache
docker compose exec app chmod -R 775 storage bootstrap/cache

# Install dependencies
docker compose exec app composer install

# Generate app key
docker compose exec app php artisan key:generate

# Run migrations and seed
docker compose exec app php artisan migrate:fresh --seed
```

### Environment — Mail (Mailtrap)

Add your Mailtrap credentials to `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@supportticket.com
MAIL_FROM_NAME="Support Ticket"
```

### Default Users (seeded)

| Role  | Email            | Password   |
| ----- | ---------------- | ---------- |
| Admin | `admin@test.com` | `password` |

---

## Development

```bash
# Generate Swagger docs
docker compose exec app php artisan l5-swagger:generate

# Generate Swagger docs file for a new controller
docker compose exec app php artisan make:swagger ExampleController

# Run migrations fresh with seed
docker compose exec app php artisan migrate:fresh --seed
```

---

## Commit Convention

This project follows [Conventional Commits](https://www.conventionalcommits.org/).

### Format

```
<type>: <short description>
```

### Types

| Type       | When to use                              |
| ---------- | ---------------------------------------- |
| `feat`     | New feature or endpoint                  |
| `fix`      | Bug fix                                  |
| `refactor` | Code change that is not a fix or feature |
| `chore`    | Config, dependencies, tooling            |
| `docs`     | Documentation only                       |
| `test`     | Adding or updating tests                 |
| `style`    | Formatting, missing semicolons, etc.     |

### Examples

```
feat: add ticket status history tracking
fix: rename suport_level to support_level
refactor: extract ticket filters into query layer
chore: update gitignore and readme
docs: add swagger documentation for ticket endpoints
test: add unit tests for ticket service
```

---

## Project Status

```
✅ Authentication (Sanctum)
✅ Authorization (Policies)
✅ Ticket CRUD
✅ Filters & pagination
✅ API documentation (Swagger)
✅ Ticket status history
✅ Soft delete (restore + force delete)
✅ Events & email notifications
⬜ Automated tests
```
