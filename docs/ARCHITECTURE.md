# KNR Architecture

## Overview

```
Mobile app (Expo)  ─┐
Admin web (Next.js) ┼──►  Gateway :8000  ──►  Auth service :8001      ── knr_auth (Postgres)
Passenger scan page ┘     /api/v1/*      └─►  Registry service :8002  ── knr_registry (Postgres)
```

Three small Laravel services, one database each, behind a gateway. They live in this repo
(a monorepo) and share nothing except an HTTP contract and two secrets.

| Service | Owns | Notes |
|---|---|---|
| **gateway** | Nothing stored | Only service exposed to clients. Verifies the JWT, rate limits, forwards requests |
| **auth** | Users, OTP codes, roles, permissions, role assignments, organizations | Issues the JWT. No data about riders |
| **registry** | Stages, riders, bikes, documents, incidents, scan logs | Trusts the identity the gateway forwards |

### Why this split

Auth and the rider registry change for different reasons and have different security needs.
The gateway gives one place for rate limiting and for the public scan endpoints. More services
(subscriptions and payments, notifications, reporting) can be added behind the gateway later by
adding a Laravel folder, a line in `docker-compose.yml` and a line in `services/gateway/config/gateway.php`.

## Request flow

1. A client calls the gateway: `https://<host>/api/v1/<resource>/...` with `Authorization: Bearer <jwt>`.
2. The gateway verifies the JWT (HS256, shared `JWT_SECRET`) and applies rate limits.
3. It forwards the request to the owning service with trusted headers:
   `X-Internal-Key` (proves it came from the gateway), `X-User-Id`, `X-User-Roles` (built from the verified token).
   Clients can never set these themselves, because the gateway only copies an allow-list of client headers.
4. The service checks permissions from `X-User-Roles` and answers.

Services reject any request without the right `X-Internal-Key`, so they cannot be called directly
from outside the Docker network even if a port is exposed.

## Authentication

- Phone number + 6-digit OTP (5 minute expiry, 5 attempts). A user is created on first successful verification.
- Optional 4 to 6 digit PIN for returning users (`/auth/pin/login`).
- The JWT carries the user's roles with scope: `{ role, scope_type, scope_id }`. A new role assignment shows up in the token the next time the user signs in.
- Super admin accounts are created from the command line: `php artisan knr:make-super-admin +256...`.
- OTP delivery is a pluggable `SmsSender`. Only a `log` driver exists. Add an SMS provider before any real use.

## Access control

Role + scope, stored as data (`roles`, `permissions`, `permission_role`, `role_assignments`) and seeded by
`RolesAndPermissionsSeeder`. Examples: *stage_head of stage X*, *traffic_officer of Kampala Central Division*.

What is enforced today (in the registry, by role and scope from the token):

| Action | Who |
|---|---|
| Approve or suspend a stage | super admin, municipal officer |
| Register, edit, suspend a rider | super admin, and the stage head or official of that stage |
| See the full verified profile | super admin, traffic officer, and staff of the rider's own stage |
| See the public profile | everyone, no login |
| Flag a bike as stolen | super admin, traffic officer, stage head |
| Report an incident | police, LC official, stage staff (passengers use the public report endpoint) |

Approving a stage assigns the applicant the `stage_head` role for that stage (registry calls the auth
service's internal endpoint, which the gateway never exposes).

## The QR flow

1. A stage head registers a rider. The registry stores the rider and bike, encrypts the National ID,
   and creates a random 48 character `qr_token`.
2. The QR code encodes `PUBLIC_SCAN_URL/<token>`, for example `http://localhost:3000/r/<token>`.
   It contains no personal data.
3. Scanning with any phone camera opens the Next.js page `/r/[token]`, which calls
   `GET /api/v1/public/riders/qr/<token>`.
4. The registry decides what to return:
   - guest: public view (photo, name, plate, stage, status, document validity, stage head contact)
   - police or own-stage staff (token sent): full view (phone, masked National ID, documents, incidents)
   - suspended rider or stolen bike: only a "do not ride" warning
5. Every lookup is written to `scan_logs` (who, which roles, which rider, method, time, IP, optional GPS).

Plate search works the same way: `GET /public/riders/plate/<plate>`. The plate is normalized
(`ubk 482-x` becomes `UBK482X`) and only an exact match is returned. An unknown plate answers
"not registered on KNR. This does not mean the rider is illegal."

The mobile scanner sends the signed-in user's token, so officers and stage heads get the full profile.

## Rider status

Computed from the rider's documents, never stored by hand:

`suspended` > `stolen` > `incomplete` (permit or insurance missing) > `expired` > `expiring_soon` (within 30 days) > `verified`

The status is always shown with an icon and a text label, not by colour alone.

## Rate limits (gateway)

| Limiter | Applies to | Limit |
|---|---|---|
| `otp` | OTP request/verify, PIN login | 5 per minute per IP, 20 per hour per phone |
| `public-lookup` | QR scan, plate search, passenger reports | 20 per minute and 120 per hour per IP |
| `public-files` | Photos on the scan page | 120 per minute per IP |
| `api` | Everything behind a token | 120 per minute per user |

## Data protection choices

- National ID is encrypted at rest (Laravel `encrypted` cast). A keyed hash is stored beside it to catch duplicates.
- The QR code holds only a random token, so a rider can be suspended or updated without reissuing the code.
- The public view hides phone, National ID, address, next of kin and dues.
- Riders consent at registration (`consent_at` is stored).
- A Data Protection and Privacy Act review is still needed before launch (see KNR-documentation.md, section 6.4).

## Known gaps and next steps

Things the boilerplate deliberately leaves out, roughly in order:

1. **Run and fix.** Nothing has been executed yet. First `composer install`, `npm install` and typecheck will turn up small issues.
   Laravel is pinned to `^13.0` and Expo to SDK 54 in `package.json` files; adjust to what is current.
2. **Real SMS provider** for OTP (e.g. Africa's Talking) behind the `SmsSender` interface; remove `OTP_DEBUG_RETURN`.
3. **Refresh tokens and token revocation.** Today there is a single access token (12 hours). Suspending a user does not
   cut an already issued token short.
4. **Authority onboarding** (application, ID photo, approval chain, organization admins) and jurisdiction scoping
   (a traffic officer should see riders in their division only; today police see any rider they look up).
5. **Registration polish on mobile:** five-step flow, photo capture and upload, QR image and printable ID card, offline queue with sync.
6. **Audit log screen** in the admin app (the data is already collected in `scan_logs`) and a rider-facing "who looked me up" view.
7. **Subscriptions and payments** service (MTN Mobile Money, Airtel Money), free tier limits, and rider-count enforcement.
8. **Stage head handover** when leadership changes, rider transfers between stages, and rider self-service.
9. **Theme.** Brand colours in `apps/admin-web/src/app/globals.css` and `apps/mobile/src/theme/index.ts` are placeholders until the
   Behance palette is filled in.
10. **Production hardening:** HTTPS, secrets management (change `JWT_SECRET` and `INTERNAL_API_KEY`), a separate key per service,
    backups, object storage for photos, CI, tests.
11. The public passenger pages currently live inside the admin Next.js app. Split them into their own small app if the admin
    app should stay private.
