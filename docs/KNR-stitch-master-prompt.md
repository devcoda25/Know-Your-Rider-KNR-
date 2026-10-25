# KNR: Stitch AI Master Prompt

How to use this file:
1. Fill in the colour hex codes in **Part A, section 3** (the Behance page could not be read automatically, so take the hex codes from the sample design, or from a screenshot colour picker).
2. Paste **Part A** into Stitch first. It sets the product, the design system, and the rules for every screen.
3. Then paste **Part B** batches one at a time (B1 to B6). Each batch generates one group of screens. Keep Part A at the top of every batch, or tell Stitch "use the same design system as before."

---

# PART A: MASTER PROMPT (paste first)

## 1. Product

Design the complete UI for **Know Your Rider (KNR)**, a mobile app and web system for registering and verifying **boda boda (motorcycle taxi) riders in Uganda**.

- Stage heads (leaders of a boda boda stage) register the riders in their stage.
- Every rider gets a unique **QR code** and a digital ID card.
- Passengers scan the QR code (or search a **number plate**) to see the rider's verified details before riding.
- Police, local council (LC) officials, KCCA / municipal authorities, insurers, URA and the transport licensing body use the app to verify riders and view compliance.
- Stage heads pay a subscription (mobile money) for the service. The platform owner manages everything from a web admin dashboard.

The product must feel **trustworthy, official, safe, fast and simple**. Many users are not tech-savvy, use mid-range Android phones, work outdoors in bright sunlight and often have weak internet.

## 2. Platforms and format

- **Mobile app** (Android first), frame size 390 x 844. Used by stage heads, riders, police, LC officials and other field authorities.
- **Mobile web page** (no login, no install) for passengers who scan a QR or search a plate.
- **Desktop web dashboard**, frame size 1440 x 900, for the platform admin and for municipal / insurer / URA / licensing back-office users.

## 3. Colour palette (use these exact colours)

Take the brand colours from the reference design "Ride and Delivery App UI" on Behance. Use them for brand surfaces, buttons and highlights.

| Token | Hex | Use |
|---|---|---|
| Primary | `#______` | Main buttons, active tab, headers, key highlights |
| Primary dark | `#______` | Pressed state, app bars |
| Secondary | `#______` | Secondary buttons, chips, illustrations |
| Accent | `#______` | Small highlights, badges, links |
| Background | `#______` | Screen background |
| Surface | `#______` | Cards, sheets, inputs |
| Text primary | `#______` | Headings and body |
| Text secondary | `#______` | Captions, helper text |
| Border / divider | `#______` | Lines, input outlines |

**Status colours (fixed meaning, used consistently across the whole product):**

| Status | Colour | Meaning |
|---|---|---|
| Verified | Green | Rider and documents are valid |
| Expiring soon | Amber | Permit or insurance expires within 30 days |
| Expired | Orange-red | A document has expired |
| Suspended / Stolen | Red | Rider suspended or bike flagged |
| Info / Pending | Blue or grey | Awaiting approval |

If the sample palette already contains a green, amber or red, keep these status colours consistent with it. Never use the status colours for decoration, and never show status by colour alone: always pair it with an icon and a text label.

## 4. Visual style

- Clean, modern, friendly ride-app look matching the reference design: rounded cards (corner radius 16), soft subtle shadows, generous spacing, large clear headings.
- Typography: a geometric sans-serif (Inter or Poppins style). Body 16 px minimum, headings bold, numbers (plates, amounts) in a medium-weight, slightly wider style for readability.
- Icons: one consistent rounded line-icon set.
- Buttons: full-width primary buttons with 52 px height, rounded 14 px. Minimum touch target 48 px.
- High contrast so everything stays readable in sunlight.
- Light theme only for now.
- Use **realistic placeholder profile photos** (generic illustrated or neutral avatars, no real or celebrity people) and a generic motorcycle illustration for bike photos.
- No real brand logos. For payments, show plain text labels "MTN Mobile Money" and "Airtel Money" with generic icons.

## 5. Content and localisation

- Language: English UI text, with a **language switcher (English / Luganda)** on the first screen and in settings.
- Currency: **UGX** (for example "UGX 20,000").
- Phone numbers: +256 format (for example +256 7XX XXX XXX).
- Use fictional sample data only:
  - Rider names: Ssebunya Moses, Nakato Joan, Okello David, Mugisha Brian, Namukasa Grace
  - Plates: UBK 482X, UEA 117M, UDV 905C
  - Stages: Kisenyi Stage, Wandegeya Stage, Nakawa Market Stage
  - Locations: Kampala Central Division, Nakawa Division, Makindye Division
- Keep text short and plain-language. Avoid jargon.

## 6. UX rules for every screen

1. Every screen has a clear single main action.
2. Rider status is always shown the same way: coloured status pill plus icon plus text (Verified, Expiring soon, Expired, Suspended).
3. Show an **offline / syncing indicator** on stage head and authority screens ("Offline, 3 riders waiting to sync").
4. Forms are short, split into steps with a progress indicator, and keep the keyboard and camera in mind (large inputs, camera buttons for photos and ID).
5. Empty states, loading states and error states are designed, not blank (friendly illustration plus one clear action).
6. Sensitive data (National ID number, next of kin) is partly masked unless the user's role allows full view (example: "CM95•••••••K").
7. Authority screens show a small notice: "This lookup is logged."
8. Bottom navigation (max 4 to 5 items) on mobile, a left sidebar on desktop.
9. The design must make riders and passengers feel safe, not watched. Keep the tone respectful and neutral.

## 7. Roles to design for

1. Passenger / public (no login)
2. Stage head (and stage officials)
3. Rider (optional self-view)
4. Traffic police
5. LC official
6. KCCA / municipal / division officer
7. Insurer agent
8. Licensing body officer
9. URA officer
10. Organisation admin (approves officers in an organisation)
11. Super admin (platform owner)

Show the correct role-based visibility: passengers see a limited public view, authorities see more, stage heads see their own stage fully.

---

# PART B: SCREEN BATCHES (paste one at a time)

Start each batch with: *"Use the KNR design system from the master prompt above. Design the following screens."*

## B1. Onboarding and authentication (mobile)

1. **Splash screen**: KNR logo wordmark ("Know Your Rider") with tagline "Ride with confidence."
2. **Language selection**: English / Luganda cards.
3. **Welcome / role choice**: "I'm a stage head", "I'm an officer (police, LC, authority)", "I'm a rider", "I'm a passenger, check a rider" (this last one leads to the public search).
4. **Enter phone number**: +256 input, "Send code" button.
5. **OTP verification**: 6-digit input, countdown, "Resend code" and "Call me with the code" option.
6. **Create PIN**: 4 to 6 digit PIN pad, with "Use fingerprint" option.
7. **Login with PIN** (returning user).
8. **Authority application form**: name, organisation, rank/position, station or area, official ID number, ID photo upload, "Submit for approval".
9. **Pending approval screen** with status steps (Submitted, In review, Approved).
10. **Account rejected / suspended screen** with contact support button.

## B2. Stage head app (mobile)

1. **Home dashboard**: stage name, subscription status chip, summary cards (Total riders, Verified, Expiring soon, Expired, Suspended), "Register new rider" primary button, offline sync banner, recent activity list.
2. **Riders list**: search bar, filter chips (All, Verified, Expiring, Expired, Suspended), rider cards with photo, name, plate and status pill.
3. **Register rider, step 1 of 5, personal details**: photo capture, full name, phone, date of birth, National ID number with "scan ID" button, address.
4. **Register rider, step 2 of 5, bike details**: number plate, make, model, colour, bike photo capture.
5. **Register rider, step 3 of 5, documents**: driving permit number and expiry date, insurance provider and expiry date, upload/photo buttons.
6. **Register rider, step 4 of 5, next of kin and emergency contact**.
7. **Register rider, step 5 of 5, review and consent**: summary card, rider consent checkbox and signature box, "Register rider" button.
8. **Registration success**: confetti-free simple success, the generated **QR code**, buttons "Share ID", "Print ID card", "Register another".
9. **Rider profile**: large photo, status pill, tabs (Details, Documents, History, Incidents), actions menu (Edit, Suspend, Transfer to another stage, Remove).
10. **Digital rider ID card**: front with photo, name, plate, stage, status, QR code; back with stage head contact and "Scan to verify". Show a printable-sticker version too.
11. **Suspend rider bottom sheet**: reason list, notes, confirm.
12. **Expiry reminders / notifications list**: grouped by Today, This week, Earlier.
13. **Stage profile**: stage name, location, officials list, "Add official" button.
14. **Add stage official form**: phone, role (Secretary, Treasurer), permissions summary.
15. **Subscription and payment**: current plan, rider usage bar, plan options in UGX, "Pay with MTN Mobile Money / Airtel Money" selector, payment confirmation, payment history list, a "payment due" warning state.
16. **Incidents and reports received** list and detail.
17. **Settings**: language, change PIN, linked devices, help and support, log out.

## B3. Passenger public web page (mobile web, no login)

Design these as **very light, fast-loading web pages** (no bottom navigation). Include a small "Download the KNR app" banner.

1. **Landing / search**: "Check a rider" with two big options, "Scan QR code" and "Search number plate", and a plate input field.
2. **Rider verified result**: large rider photo, name, plate, bike photo, stage name and location, big green **Verified** status, permit valid until, insurance valid until, stage head name with "Call stage head" button, buttons "Share ride details", "Report this rider", and a red "Emergency" button. A reminder line: "Check that the person in front of you matches this photo."
3. **Result, Expired**: same layout, amber/orange status with the document that has expired, and a caution message.
4. **Result, Suspended or stolen bike**: red full-width warning banner, "Do not ride", with "Report" and "Call police" buttons. Hide extra details.
5. **Plate not found**: neutral screen: "This plate is not registered on KNR." with the note "This does not mean the rider is illegal." and a button "Report an unregistered bike".
6. **Share ride details**: preview card of rider name, photo, plate, then "Send by SMS" and "Send by WhatsApp" buttons, choose a contact.
7. **Report a rider**: reason chips (Reckless riding, Overcharging, Harassment, Other), description, optional photo, optional phone number, submit, confirmation.
8. **Emergency sheet**: call stage head, call police, share my location.
9. **Rate-limit / verify you're human screen** (appears after many searches): simple OTP input.

## B4. Authority app: police and LC officials (mobile)

1. **Home**: officer name, organisation and area chip, big "Scan QR" button, "Search plate" field, today's scans count, quick actions (Report incident, Flag stolen bike, Scan history), notice "All lookups are logged."
2. **QR scanner**: camera view with scan frame, flash toggle, "Enter plate instead" link.
3. **Full rider verification result (police)**: photo, name, phone, masked National ID with "Reveal" (logged), plate, bike photo, permit and insurance status with dates, stage and stage head contact, flags (stolen, suspended), incident history, buttons "Report offence", "Flag stolen bike".
4. **LC official result**: name, photo, stage, plate, status, contact, "Confirm residence" and "Raise complaint" buttons.
5. **Report incident form**: type, description, photos, location auto-filled, submit.
6. **Flag stolen bike**: plate, details, confirmation warning.
7. **Scan history list**: date, rider, place, outcome, filter by date.
8. **Area riders list** (LC officials): riders in their parish, filter by stage and status.
9. **Offline mode screen**: downloaded area data, last synced time, "Sync now".
10. **Profile and settings**: officer details, approved device, change PIN, log out.

## B5. Municipal, insurer, licensing and URA dashboards (desktop web, 1440 x 900)

Use a left sidebar, top bar with search, and card-and-table layout.

1. **KCCA / municipal home dashboard**: KPI cards (Registered stages, Registered riders, Compliance rate, Suspended riders), compliance map or bar chart by division, recent stage applications.
2. **Stage applications queue**: table with stage, location, head, date, status, Approve / Reject actions.
3. **Stage detail**: stage info, riders summary, compliance, fees, "Suspend stage".
4. **Area reports page**: filters (division, date), charts (riders by stage, compliance trend), export buttons.
5. **Insurer dashboard**: policy lookup by plate, expiring policies table, "Update policy" side panel, claim-related incidents list.
6. **Licensing body dashboard**: permit verification search, permit status table, "Update permit status".
7. **URA reports page**: aggregate rider and stage counts by area and period, export (no personal data).

## B6. Admin dashboards (desktop web, 1440 x 900)

1. **Super admin home**: KPIs (Stages, Riders, Active subscriptions, Monthly revenue in UGX, Pending approvals), revenue chart, recent activity.
2. **Approvals queue**: tabs (Stages, Authority organisations, Authority officers), table with documents preview and Approve / Reject.
3. **Stages management**: table with filters, subscription status, rider count, actions.
4. **Authority organisations and officers**: organisation list, officer list with area, status, suspend/reactivate.
5. **Roles and permissions matrix**: roles as columns, permissions as rows, toggles, field-visibility section (which fields each role can see).
6. **Subscriptions and payments**: plans editor, payments table (MTN / Airtel, status), failed payments.
7. **Audit log**: table of scans and searches (who, role, rider, time, location), filters, "Flag suspicious activity", export.
8. **Stolen bikes and incident reports** management.
9. **Organisation admin view** (for example a police station commander): officers list, "Invite officer", approve pending officers, activity summary for their own organisation only.

---

# CLOSING INSTRUCTION FOR STITCH

Generate all screens in one consistent design system. Keep spacing, corner radius, typography and status colours identical across mobile, mobile web and desktop. Prefer clarity and large touch targets over decoration. Make every status unmistakable at a glance. Use only fictional sample data.
