# Support Ticket API

RESTful API for enterprise-style support ticket management.

## Tech Stack

* Laravel
* PostgreSQL
* Docker

## Status

Project in development.

## Fix permissions

docker compose exec app chown -R www-data:www-data storage bootstrap/cache
docker compose exec app chmod -R 775 storage bootstrap/cache