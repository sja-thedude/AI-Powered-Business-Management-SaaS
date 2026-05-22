# NovaBiz AI

> The AI-powered business operating system — a modular, multi-tenant ERP/SaaS for startups and companies. Think **Odoo × Zoho × HubSpot**, with AI automation baked into the architecture.

Built with **Laravel 12**, **Inertia + Vue 3**, **Tailwind CSS v4**, **PostgreSQL**, **Redis**, **Laravel Reverb** (WebSockets), **Horizon** (queues), **Cashier/Stripe** (billing) and **Sanctum** (API).

---

## Highlights

- **Multi-tenant SaaS** — single-database, row-level isolation via a `tenant_id` global scope. Every domain model is auto-scoped; cross-tenant leakage is impossible by default (and covered by tests).
- **Modular ERP** — modules are declared once in `config/modules.php` and the navigation, plan-gating and permissions all react. New modules drop in forever.
- **Subscription billing** — Stripe via Laravel Cashier, billed at the **workspace (tenant)** level, with 14-day trials and plan-based module gating.
- **API-first** — versioned, token-authenticated REST API at `/api/v1` (Sanctum) alongside the Inertia web app.
- **Real-time** — Laravel Reverb + Echo for live dashboard widgets and a collaborative Kanban deal board.
- **AI-ready** — a single swappable AI gateway (`App\Services\AI`) fronts every AI capability; runs offline (deterministic `fake` driver) with zero config, or against Anthropic Claude in production.
- **Enterprise plumbing** — RBAC (per-tenant roles via spatie/permission "teams"), an immutable audit trail, Redis queues with Horizon, and a full Docker stack.
- **Premium UI** — responsive admin dashboards, analytics widgets (Chart.js), and persistent dark/light mode.

## Modules

| Module | Status | What's inside |
|---|---|---|
| **CRM** | ✅ Full | Leads (with AI scoring), Clients, Pipelines, Kanban deal board, communication history |
| HR Management | 🟡 Scaffold | Departments, employees, recruitment, leave, performance reviews |
| Payroll | 🟡 Scaffold | Salary structures, payroll runs, payslips, tax rules |
| Attendance | 🟡 Scaffold | Shifts, assignments, check-in/out (biometric-ready) |
| Inventory | 🟡 Scaffold | Warehouses, suppliers, products, stock movements |
| Invoicing | 🟡 Scaffold | Invoices, line items, payments, taxes |
| Projects | 🟡 Scaffold | Projects, tasks, Kanban columns, time tracking |
| Client Portal | 🟡 Scaffold | Tickets, replies, file sharing, messaging |

**Full** = models, controllers, web + REST API, polished Vue UI, and tests. **Scaffold** = database schema, Eloquent models, tenant-scoped routes, and a UI landing page — ready to build out following the CRM pattern.

---

## Architecture

```
Browser ──Inertia──┐
                   ├─►  Laravel 12 (web routes)  ─┐
Mobile / 3rd-party ┘                              │
   └──Sanctum token──►  /api/v1 (REST)            │
                                                  ▼
   IdentifyTenant middleware ─► TenantContext (singleton)
                                                  │
        Global TenantScope auto-filters every query by tenant_id
                                                  │
   PostgreSQL   Redis (cache/session/queue)   Reverb (WebSockets)
                     │
              Horizon workers ─► AI jobs (App\Jobs\AI) ─► AiManager ─► Claude / fake
```

### Multi-tenancy
- `App\Models\Tenant` is the isolation boundary **and** the Cashier billable entity.
- `App\Models\Concerns\BelongsToTenant` applies `App\Models\Scopes\TenantScope` and stamps `tenant_id` on create.
- `App\Http\Middleware\IdentifyTenant` resolves the tenant (from the authenticated user, or a subdomain/custom domain) and binds `TenantContext` + the spatie permission "team".

### Authorization
- Per-tenant roles (Owner, Admin, Manager, Member) via **spatie/permission** with the *teams* feature keyed on `tenant_id`.
- The global permission catalog is generated from the module registry (`App\Support\Permissions`).
- The workspace **Owner** bypasses granular checks via a `Gate::before` rule.

### Billing & plans
- Plans live in `config/billing.php` (`starter`, `growth`, `scale`). `min_plan` on each module gates access.
- `EnsureTenantSubscribed` and `EnsureModuleEnabled` middleware enforce the paywall and module access.

### AI layer
- `config/ai.php` declares capabilities (sales prediction, invoice generation, OCR, chatbot, …).
- `AiManager` resolves a driver: `fake` (offline, deterministic — default) or `anthropic` (Claude). Lead scoring runs as a queued, tenant-aware job (`App\Jobs\AI\ScoreLeadJob`).

---

## Local development

### Requirements
- PHP **8.2+**, Composer 2
- Node 20+, npm
- PostgreSQL 14+ (MySQL 8 also supported)
- Redis (optional locally — local config uses database-backed cache/queue/session)

### Setup

```bash
# 1. Install dependencies
composer install
npm install

# 2. Environment (a local .env is already provided, pointing at Postgres "novabiz")
php artisan key:generate          # only if APP_KEY is empty
createdb novabiz                  # or: CREATE DATABASE novabiz;

# 3. Migrate + seed a demo workspace with CRM data
php artisan migrate:fresh --seed

# 4. Build assets (or `npm run dev` for hot reload)
npm run build

# 5. Serve
php artisan serve
```

Visit **http://localhost:8000**.

**Demo login:** `owner@novabiz.test` / `password`
(also `admin@novabiz.test` and `member@novabiz.test`, same password)

> **PHP version note:** if your default `php` is < 8.2 (e.g. Homebrew has multiple), prefix commands, e.g.
> `export PATH="/opt/homebrew/opt/php@8.2/bin:$PATH"` before `composer`/`php artisan`.

### Real-time & queues (optional, locally)
```bash
php artisan reverb:start     # WebSocket server
php artisan queue:work       # or `php artisan horizon` with Redis
```

---

## Testing

The suite runs against a disposable Postgres database so production SQL behaves identically:

```bash
createdb novabiz_test
php artisan test
```

Covers tenant isolation (the core multi-tenancy guarantee), CRM CRUD over web **and** API, lead→client conversion, and API auth.

---

## REST API (v1)

```bash
# Issue a token
curl -X POST http://localhost:8000/api/v1/auth/token \
  -H "Accept: application/json" \
  -d email=owner@novabiz.test -d password=password -d device_name=cli

# Use it
curl http://localhost:8000/api/v1/crm/leads \
  -H "Authorization: Bearer <token>" -H "Accept: application/json"
```

Resources: `auth/{token,me,logout}`, `crm/leads`, `crm/clients`, `crm/deals` (full CRUD, tenant-scoped, plan-gated).

---

## Production / Docker

A full production-shaped stack is provided:

```bash
cp .env.example .env        # set APP_KEY, DB, Stripe & Reverb secrets
docker compose up -d --build
docker compose exec app php artisan migrate --seed
```

Services: **app** (php-fpm), **web** (nginx, port 8080), **pgsql**, **redis**, **horizon** (queue workers), **reverb** (WebSockets), **scheduler**.

The image is multi-stage (Node asset build → Composer deps → php-fpm runtime) with OPcache + JIT tuned for production. See `Dockerfile`, `docker-compose.yml` and `docker/`.

### Production checklist
- Set `APP_ENV=production`, `APP_DEBUG=false`, a strong `APP_KEY`.
- Switch `CACHE_STORE`, `SESSION_DRIVER`, `QUEUE_CONNECTION` to `redis`.
- Configure Stripe (`STRIPE_KEY/SECRET/WEBHOOK_SECRET`) and the plan price IDs (`STRIPE_PRICE_*`).
- Point Reverb env (`REVERB_*`, `VITE_REVERB_*`) at your WebSocket host.
- Set `AI_DRIVER=anthropic` + `ANTHROPIC_API_KEY` to enable live AI.
- Run `php artisan config:cache route:cache event:cache` (the Docker entrypoint does this automatically).

---

## Tech stack

Laravel 12 · PHP 8.2 · Inertia.js + Vue 3 · Tailwind CSS v4 · Vite · PostgreSQL · Redis · Laravel Reverb · Laravel Horizon · Laravel Cashier (Stripe) · Laravel Sanctum · spatie/laravel-permission · Chart.js · Docker.

## License

MIT.
