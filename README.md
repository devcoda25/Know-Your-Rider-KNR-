# Know Your Rider (KNR)

A mobile app and backend system for registering and verifying boda boda (motorcycle taxi) riders in Uganda. Stage heads register riders, each rider gets a unique QR code, and passengers, police and other authorities can scan the QR code or search a number plate to see verified rider details.

## Repository layout

```
mobileuserapp/     React Native (Expo): stage heads, officers, passengers
admin-platform/    Next.js: super admin app + public passenger scan pages (/r/[token], /check)
knr-backend/       Laravel services behind a gateway, plus Docker setup
  gateway/           the only public entry point (JWT check, rate limits, routing)
  auth/              OTP + PIN login, JWT issuing, roles, permissions, scopes
  registry/          stages, riders, bikes, documents, incidents, scan logs
  docker/            shared PHP image, Postgres init
  docker-compose.yml
docs/              project documentation and design prompts
```

Details of how the pieces fit together are in [docs/ARCHITECTURE.md](docs/ARCHITECTURE.md).

## Documentation

| Document | Description |
|---|---|
| [docs/ARCHITECTURE.md](docs/ARCHITECTURE.md) | Services, request flow, QR flow, access control, known gaps |
| [docs/KNR-documentation.md](docs/KNR-documentation.md) | Product documentation: goals, roles, features, security, business model, release plan |
| [docs/KNR-stitch-master-prompt.md](docs/KNR-stitch-master-prompt.md) | Master prompt for Stitch AI to generate the UI screens |

## Run it locally

You need Docker, Node 20+ and (for the phone app) Expo Go or an Android emulator.

```bash
# 1. Backend: Postgres, Redis, gateway (:8000), auth (:8001), registry (:8002)
make up                      # first start installs Composer packages, so give it a few minutes

# 2. In another terminal: create the first super admin (use your own number)
cd knr-backend
docker compose exec auth php artisan knr:make-super-admin +256700000000

# 3. Optional: sample stage and riders (prints their QR links)
docker compose exec registry php artisan db:seed --class=DemoSeeder
cd ..

# 4. Admin platform on http://localhost:3000
cd admin-platform && cp .env.example .env.local && npm install && npm run dev

# 5. Mobile app (new terminal, from the repo root)
cd mobileuserapp && cp .env.example .env && npm install && npx expo install --fix && npx expo start
```

Sign in with the phone number from step 2. In local development the one-time code is shown on the
screen (`OTP_DEBUG_RETURN=true`) and also written to `knr-backend/auth/storage/logs/laravel.log`.

Handy checks:

```bash
curl localhost:8000/api/v1/health                      # gateway + upstream status
curl localhost:8000/api/v1/public/riders/plate/UBK482X # after seeding the demo data
```

## Status

Boilerplate. Nothing here has been run yet, because the first scaffold was written in an environment
without access to npm or Composer. Expect to fix small version and typing issues on the first
`composer install`, `npm install` and `npm run typecheck`. The known gaps and the next steps are listed
in [docs/ARCHITECTURE.md](docs/ARCHITECTURE.md#known-gaps-and-next-steps).
