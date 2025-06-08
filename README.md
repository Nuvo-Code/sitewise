# 🌐 Sitewise

**Sitewise** is a Laravel-based, multi-tenant platform for serving static sites under custom domains. Each domain hosts its own site and admin panel, supporting flexible page content types, templates, and Markdown rendering.

---

## 🚀 Project Goals

- Quickly deploy and manage multiple static websites under different domains.
- Centralized control via a shared admin interface (`/admin`), scoped per domain.
- Support flexible content delivery: HTML, Markdown, and JSON.
- Reusable page templates with dynamic content blocks.
- Simple deployment via [Coolify](https://coolify.io) with optional scaling.

---

## 🧩 Features

### ✅ Domain-based Multi-Tenancy
- Each domain maps to a single `Site` (record in DB).
- Admin panel is accessed via `/admin` on that domain (e.g. `https://client1.com/admin`).
- All admin actions are scoped to the current domain automatically.
- Domains are manually registered and not user-generated (trusted origin only).

### 🛠️ Admin Panel (via FilamentPHP)
- Accessible per-site at `/admin`.
- Automatically creates the `Site` record if the domain is not yet registered (with read-only domain field).
- Clean and scoped resource management:
  - Pages (`pages`)
  - Templates (`templates`)
  - Template Blocks (`template_contents`)

### 📄 Page System
- Pages are attached to a specific site.
- Each page can be of one of the following types:
  - `html`: Raw HTML content rendered in a Blade view.
  - `markdown`: Parsed using CommonMark and rendered.
  - `json`: Raw or dynamic JSON returned via API.
- Pages may optionally be linked to a `template_id`.
  - If a template is selected, raw content is hidden and dynamic fields are shown.

### 🧱 Template System
- Templates define reusable content block structures via JSON (e.g. `hero_title`, `cta_text`, etc.).
- Templates are assigned **per page**, not per site.
- Template content is stored in `template_contents`, linked by page and key.

### 📝 Markdown Support
- Markdown rendering is powered by `league/commonmark`.
- Pages with `response_type = markdown` display parsed Markdown content.
- Admin UI allows Markdown editing with a preview option (if available).

### 🖼️ Static Assets
- Shared frontend assets (e.g., Tailwind CSS, JS) served via `/sitewise-assets`.
- Compatible with Laravel Mix or Vite build tools.
- CDN-optimized and versioned delivery planned.

---

## 🗂️ Tech Stack

- **Backend**: Laravel 11+
- **Admin**: FilamentPHP
- **Markdown**: league/commonmark
- **Deployment**: Coolify (Dockerized)
- **Templating**: Blade
- **Frontend Assets**: Tailwind, Alpine, etc.

---

## 🧰 Setup & Installation

```bash
git clone https://github.com/nuvo-code/sitewise.git
cd sitewise
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install && npm run dev

✅ Make sure to point your local domain to the correct host entry for testing subdomains.

⸻

🌐 Domain Handling
	•	Each domain (or subdomain) is mapped to a site in the DB.
	•	A middleware detects the current domain and injects app()->site into the request lifecycle.
	•	Unregistered domains will auto-create a site record with a read-only domain value.

⸻

📦 Deployment (Coolify)
	•	Single instance Laravel app deployed via Coolify.
	•	One container serves all domains (multi-tenant via middleware).
	•	Optional scaling via more containers or CDN caching in the future.

⸻

🔒 Security
	•	No public user registration.
	•	Admin access is protected (optionally via VPN or Basic Auth).
	•	All domains are manually managed by the system operator.
	•	Pages and templates are always scoped to app()->site.

⸻

📌 Roadmap Ideas
	•	Preview mode for pages in admin.
	•	API for CLI/CI-based site provisioning.
	•	Domain SSL automation (via Coolify/Traefik).
	•	Import/export content tools.
	•	Webhook support on page updates.

⸻

🧑‍💻 Maintained By

Nuvo Code — a software development company focused on developer-friendly tooling and automation.

⸻

📄 License

This project is licensed under the MIT License.