# KNR Admin Platform

Next.js super admin app for the platform owner, plus the public passenger scan pages.

| Route | Who | What |
|---|---|---|
| `/login` | Platform admin | Phone number + one-time code |
| `/dashboard`, `/approvals`, `/stages`, `/riders` | Super admin | Working screens against the gateway |
| `/authorities`, `/roles`, `/subscriptions`, `/audit-log` | Super admin | Placeholders (see each page for the planned content) |
| `/r/[token]` | Anyone | Page opened by scanning a rider's QR code |
| `/check` | Anyone | Plate search |

## Run

```bash
cp .env.example .env.local
npm install
npm run dev        # http://localhost:3000
```

The backend must be running (`make up` in the repo root) and a super admin must exist
(see the root README).

## Theme

Colour tokens live in `src/app/globals.css`. The brand colours are placeholders until the
palette from the Behance reference is filled in. Status colours (verified, expiring, expired,
suspended) have fixed meanings across all KNR apps.
