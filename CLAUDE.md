# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Declarame is a Laravel 11 web application for automating tax declarations to Ecuador's SRI (Servicio de Rentas Internas). Built as an Inertia.js SPA with Vue 3 frontend and Jetstream multi-tenant teams.

## Tech Stack

- **Backend**: Laravel 11, PHP 8.3, Jetstream (teams + Sanctum auth)
- **Frontend**: Vue 3 + Inertia.js, Tailwind CSS (with forms/typography plugins), Vite
- **Database**: PostgreSQL 15 (primary), SQLite (fallback for local dev)
- **Infrastructure**: Laravel Sail (Docker), Redis, Meilisearch, Mailpit

## Common Commands

```bash
# Start Docker environment
./vendor/bin/sail up -d

# Install PHP dependencies (without Sail running)
docker run --rm -u "$(id -u):$(id -g)" -v "$(pwd):/var/www/html" -w /var/www/html laravelsail/php83-composer:latest composer install --ignore-platform-reqs

# Frontend dev server
npm run dev

# Build frontend for production
npm run build

# Run all tests
./vendor/bin/sail test
# or without Sail:
php artisan test

# Run a single test file
php artisan test --filter=ExampleTest

# Code formatting
./vendor/bin/pint

# Database
php artisan migrate
php artisan migrate:fresh --seed
```

## Architecture

### Routing Pattern
Routes are defined in `routes/web.php` using Inertia renders — most routes return `Inertia::render()` calls directly in closures rather than through controllers. All authenticated routes use `auth:sanctum` + `verified` middleware. Spanish-language URL paths are used (`/compras`, `/contribuyentes`, etc.).

API routes in `routes/api.php` handle external service calls (SRI company lookups).

### Domain Models
Core business entities scoped to teams (multi-tenancy):
- **Company** — taxpayers with RUC (tax ID) and SRI flags (special contribution, retention agent, etc.)
- **Shop** — sales invoices with multi-rate VAT tracking (5%, 8%, 12%, 15%)
- **Order** — purchase documents with similar tax structure
- **Contact** — external contacts/suppliers
- **ShopRetentionItem / OrderRetentionItem** — tax withholding line items
- **ShopPayItem** — payment method tracking

### Frontend Structure
- `resources/js/Pages/` — Inertia page components (organized by domain: `App/Shops/`, `App/Companies/`)
- `resources/js/Components/` — reusable Vue components
- `resources/js/Layouts/` — admin layout wrappers
- Ziggy is used for frontend route generation

### Authentication & Multi-Tenancy
Jetstream with teams support. Users belong to teams, and companies are scoped per team. API tokens have three permission levels: admin, editor, read-only.
