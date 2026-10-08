# تالارتو | Talarto

Single-venue ceremony booking website built with Laravel 13 / PHP 8.4 and Persian RTL Blade UI. **No SaaS and no marketplace features.**

## Local run on god

```bash
cd ~/Videos/talarto
docker compose up -d --build
docker compose ps
```

- Website: http://127.0.0.1:8000/
- Customer login: http://127.0.0.1:8000/login
- Admin login: http://127.0.0.1:8000/admin/login
- Commerce: http://127.0.0.1:8000/admin/commerce
- Readiness check: http://127.0.0.1:8000/up

Docker runs in host network mode **bound only to 127.0.0.1** to avoid conflicting with the exhausted Docker subnet pools on god. Volumes preserve database and uploads across restarts. APP_KEY is saved securely in the persistent database volume on development deployments.

## Administrator setup

No admin password is embedded in the source code or supplied as a default.
To create/rotate an administrator interactively:

```bash
docker exec -it talarto php artisan talarto:admin-create admin@your-domain.tld
```

Local credentials generated for acceptance testing live only on the authorized host under `local-backups/admin-local-access.txt` (mode 0600). Never commit or publish this file. Reset the testing password before internet exposure.

## Reservation / pricing

- Calendar with Jalali dates and day/night slots; API `GET /availability?date=1406/02/25`.
- Temporary holds plus unique permanent occupancy, to prevent concurrent double booking.
- Server-side pricing and selection snapshots, with versioned quotes.
- Customer must explicitly accept latest quote; admin may record a deposit and then confirm available dates.
- Payment records are **manual accounting entries**. There is **no active payment gateway**.
- Print-friendly quote using the browser's Print → Save as PDF option. A digitally signed contract is not implemented.
- Admin access uses hashed passwords, with authentication for management routes.
- Test workflow: `docker run --rm --network none --entrypoint php -v "$PWD:/app" -w /app talarto-app:latest vendor/bin/phpunit`

## Release checklist / limitations

This repository has been hardened for local QA, **not certified for public production release**. Before enabling public bookings:

1. Replace demo venue data, sample testimonial names/images, pricing and placeholder contact details with authentic data.
2. Review and approve terms and deposit/refund policies for the actual venue.
3. Integrate and credential your payment gateway, add callback and refund tests, and perform financial reconciliation.
4. Configure SMS/OTP provider if mobile verification and notifications are desired.
5. Replace development PHP server with a production HTTP stack, TLS, firewall and monitoring; use `APP_ENV=production`, `APP_DEBUG=false`, and a securely supplied `APP_KEY`.
6. Configure backups and test database/media restoration, plus an end-to-end browser acceptance suite.

The app is intentionally **single-venue**. It contains no vendor onboarding, multi-tenant billing, commission or marketplace comparison.

## Developer notes

- `local-backups/` contains preserved local work and test credentials; it is excluded from version control.
- The original uncommitted local modifications were preserved separately as a Git stash before implementation.
- See `tests/Feature/BookingHardeningTest.php` for regression tests for authorization, confirmation, payment and availability.
