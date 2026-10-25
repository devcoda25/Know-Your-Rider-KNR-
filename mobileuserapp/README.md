# KNR Mobile User App

React Native (Expo + Expo Router) app for stage heads, officers and passengers.

| Screen | Who | Notes |
|---|---|---|
| Welcome, Check a rider | Anyone | Plate search needs no login |
| Phone and code sign in | Stage heads, officers | OTP through the gateway |
| Home, Scan QR | Signed in | Police and stage staff get the full profile |
| Riders, Register rider | Stage head, official, super admin | Registration is one screen for now |
| Profile | Signed in | Roles and sign out |

## Run

```bash
cp .env.example .env        # set EXPO_PUBLIC_API_URL
npm install
npx expo install --fix      # aligns every Expo package to the installed SDK
npx expo start
```

Open it in Expo Go, or press `a` for an Android emulator. The backend must be running
(`make up` in the repo root). On a real phone use your computer's LAN IP in `.env`.

## Next

Five-step registration with photo capture, QR image and printable ID card, offline queue
with sync, and the police incident / stolen-bike screens.
