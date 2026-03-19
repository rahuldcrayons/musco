# Jikra E-commerce — AI Context Guide

## Project Overview
Laravel 12 e-commerce platform for **Jikra** (jikra.in). Sells everyday products across 50+ categories. Has admin panel, affiliate system, Instagram integration, WhatsApp messaging, and automated review system.

## IMPORTANT: Always Check .ai-memory/ First
Before starting any task, read the relevant files in `.ai-memory/` directory. These contain critical context about:

- **MEMORY.md** — Index of all memory files
- **reference_deployment.md** — Production server SSH, paths, deploy commands
- **reference_social_api.md** — All Meta/Instagram/Facebook/WhatsApp API tokens and IDs
- **reference_social_publishing.md** — Social calendar system, publishing service, scheduling, troubleshooting
- **reference_tokens.md** — All API credentials
- **reference_review_commands.md** — Review drip feed commands (run on PRODUCTION)
- **reference_whatsapp_templates.md** — Approved WhatsApp message templates
- **project_audit_findings.md** — Known bugs and issues
- **feedback_no_hardcoded_content.md** — Coding standards

## Production Server
- **Host:** 13.205.162.30 (AWS Lightsail)
- **User:** ubuntu
- **SSH Key:** `C:\Users\Rahul yadav\Downloads\Dcrayons.pem`
- **Path:** `/var/www/jikra`
- **PHP:** 8.3
- See `.ai-memory/reference_deployment.md` for full deploy commands

## Key Systems

### Review Drip Feed
- `php artisan reviews:drip-daily` — auto drips 1-3 reviews per hour (runs on production via cron)
- `php artisan reviews:seed-bulk` — bulk seed reviews per product
- Reviews with future `created_at` auto-appear when date arrives (`approvedReviews` scope filters by `created_at <= now()`)
- See `.ai-memory/reference_review_commands.md`

### Social Media Calendar
- Admin panel: `/admin/social-calendar`
- `SocialMediaPublishingService` handles IG + FB publishing with native scheduling
- Instagram uses separate IG token (`instagram_access_token`) with `publish_at` for scheduling
- Facebook uses `scheduled_publish_time` parameter
- `social:publish-scheduled` runs every minute for fallback
- See `.ai-memory/reference_social_publishing.md`

### Instagram Reels
- `InstagramReelsService` fetches reels from IG API
- Cached 1 hour, refreshed every 2 hours via scheduler
- Collab reels via shortcodes in settings
- JSON-LD `VideoObject` on reel pages

### WhatsApp
- Phone Number ID: 979502275251082
- Webhook: `https://jikra.in/webhook/whatsapp`
- See `.ai-memory/reference_whatsapp_templates.md` for templates

## Coding Standards
- Use `Setting::get()` for business data, never hardcode
- Use `config()` for technical config
- Run commands on PRODUCTION not local (local has only 95 products, production has 743+)
- Always deploy files via SCP + SSH (see deployment reference)

## Tech Stack
- Laravel 12, PHP 8.3, MySQL, Redis, Meilisearch
- Tailwind CSS, Alpine.js
- Meta Graph API v21.0
