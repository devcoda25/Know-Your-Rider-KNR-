# Know Your Rider (KNR)

**Project documentation, version 0.1 (draft)**
**Date:** 15 October 2026

---

## 1. Overview

**Know Your Rider (KNR)** is a mobile app and backend system for registering and verifying boda boda (motorcycle taxi) riders in Uganda. Stage heads register the riders in their stage, and every rider receives a unique QR code. Passengers, stage heads, police, and other authorities can scan the QR code, or search by number plate, to see verified details about a rider.

KNR is a paid service. The platform owner stores and manages the data, and stage heads pay a subscription for the registration and management services.

### 1.1 Problem

- Stage registers are mostly kept on paper or in chat groups, so they are easy to lose, forge, or dispute.
- Passengers have no easy way to know who the rider is or whether the rider and bike are legitimate.
- Police and local authorities have no quick way to verify a rider on the roadside.
- Stage heads have no single place to manage riders, permits, insurance expiry, and dues.

### 1.2 Goals

1. Give every registered rider a verifiable digital identity.
2. Let passengers check a rider before riding.
3. Give police and authorities a reliable, accountable way to verify riders.
4. Give stage heads tools to manage their stage, and make this valuable enough that they pay for it.
5. Give the platform owner a sustainable subscription-based business.

### 1.3 Non-goals (version 1)

- Ride-hailing, booking, or fare calculation.
- Live GPS tracking of riders.
- Replacing the official licensing process. KNR records and verifies; it does not issue legal permits.

---

## 2. Users and Roles

| Role | Description | Needs account |
|---|---|---|
| **Passenger / public** | Anyone who scans a QR code or searches a plate | No |
| **Rider** | A registered boda boda rider | Optional (view own profile) |
| **Stage head** | Leader of a boda boda stage who registers and manages riders | Yes |
| **Stage officials** | Secretary, treasurer, or similar roles inside a stage | Yes |
| **Traffic police** | Field verification and incident reporting | Yes (vetted) |
| **Local council (LC) officials** | Verify riders operating in their area | Yes (vetted) |
| **KCCA / municipal / division authorities** | Stage approvals, area reports, compliance | Yes (vetted) |
| **URA** (possibly) | Tax compliance reports | Yes (vetted) |
| **Insurers** (possibly) | Check and update insurance status, claims | Yes (vetted) |
| **Transport licensing body** (possibly) | Verify and update permit status | Yes (vetted) |
| **Super admin (platform owner)** | Approves stages and authorities, manages subscriptions, audit logs | Yes |

---

## 3. Core Features

### 3.1 Stage registration

- A stage head signs up with phone number and OTP.
- The stage head creates a stage profile: name, location (district, division, parish), and contact details.
- The stage must be approved by the platform admin (and later by the relevant authority).

### 3.2 Rider registration

The stage head registers each rider with:

- Full name, phone number, and photo
- National ID number
- Driving permit details and expiry date
- Bike details: number plate, make, and a photo of the bike
- Insurance details and expiry date
- Next of kin and emergency contact
- Stage membership details

On completion, the system generates the rider's **unique QR code** and a printable ID card.

The rider should consent to the registration and to the public display of their verified details.

### 3.3 QR code

- The QR code contains only a unique, signed identifier or link. It does not contain the rider's data.
- The details are retrieved live from the server, so updates, suspensions, and expiries take effect immediately.
- Scanning the QR with a normal phone camera opens a lightweight web page. No login and no app install are required for passengers.
- The QR code is issued as a digital image (shareable by SMS or WhatsApp) and as a printable ID card or sticker.

### 3.4 Number plate search

- Users can search a rider by number plate.
- **Exact match only**, with the input normalized (ignoring spaces, dashes, and case).
- The result follows the same access rules as the QR code for that user's role.
- If the plate is not found, the message is "Not registered on this system". It must not say the bike or rider is illegal, since many riders will not yet have joined the platform.

### 3.5 Document and expiry tracking

- Permit and insurance expiry dates are tracked per rider.
- Reminders (SMS or in-app) go to the rider and stage head before a document expires.
- Status shown on scans: **Verified**, **Expired**, or **Suspended**.

### 3.6 Rider management

- Approve, suspend, or remove a rider, with a recorded reason.
- Transfer a rider between stages.
- Update rider and bike details, with a history of changes.

### 3.7 Incidents and reports

- Passengers can report a rider (reckless riding, overcharging, harassment) from the scan or search result page.
- Police and authorities can report incidents against a rider.
- Reports are linked to the rider's record and visible to the stage head.
- Bikes can be flagged as **stolen**, shown as a warning on every lookup.

### 3.8 Passenger safety features

- Share ride details (rider name, photo, and plate) with a contact by SMS or WhatsApp.
- Emergency button to call the stage head or the police.
- Optional rating of rides (later version).

### 3.9 Subscriptions and payments

- Stage heads pay for the service through mobile money (MTN MoMo, Airtel Money).
- See section 8 for the pricing model.

### 3.10 Authority tools

- Scan QR or search by plate, with full verified details according to role.
- Report incidents and flag stolen bikes.
- Offline checks using a downloaded list for an area (later version).
- Every authority scan is logged (see section 6).

---

## 4. Access Control: Who Sees What

### 4.1 Public (passenger) view

- Rider photo and full name
- Bike plate, make, and photo
- Stage name and location
- Status (Verified, Expired, or Suspended)
- Permit and insurance validity
- Stage head name and phone number
- Ride-sharing, report, and emergency options

### 4.2 Restricted information

Visible only to stage heads, authorities, and admins according to role:

- National ID number
- Home address
- Next of kin
- Dues and payment history
- Internal incident notes

**Rider's own phone number** is an open decision (see section 11). The suggested starting point is to hide it from the public view and let passengers reach the rider through the stage head, or let the rider choose.

### 4.3 Permissions by authority type

| Authority | Main use | Can see | Can do |
|---|---|---|---|
| **Traffic police** | Roadside checks | Photo, name, phone, ID number, plate, permit and insurance status, flags, incident history | Report offences, flag stolen bikes |
| **LC officials** | Know who operates in their area | Name, photo, stage, plate, status, contact | Verify riders, confirm residence, raise complaints |
| **KCCA / municipal / division** | Licensing, stage permits, planning | Stage list, rider counts, compliance status, stage fees | Approve or suspend stages, view area reports |
| **URA** | Tax compliance | Ownership and plate data, stage and rider counts | Reports only |
| **Insurers** | Check cover, handle claims | Insurance status and expiry, bike and plate, claim-related incidents | Update policy records, flag claims |
| **Transport licensing body** | Permits and licensing | Permit validity, rider and bike compliance | Update permit status, verify licences |

### 4.4 Design approach for roles

- One account system, where each account belongs to an **organization** (for example, Uganda Police, a specific LC, KCCA) and has a **role** inside it.
- Permissions are stored as a configurable table per role, so adding an authority later is configuration and not new code.
- **Organization admins** (for example, a station commander or LC chairperson) approve their own officers.
- **Jurisdiction limits:** an account sees only the riders and stages in its area unless a higher role allows more.

---

## 5. Authority Onboarding and Verification

1. An officer applies in the app with name, rank or position, organization, station or area, and official ID number.
2. In the first phase, the platform admin manually vets and approves the application.
3. Long term, the organization supplies or approves its own officer list through an agreement.
4. Login is by phone number with OTP, plus a PIN.
5. Accounts can be suspended, and are re-verified periodically (for example, yearly).

---

## 6. Security, Privacy, and Accountability

### 6.1 Audit log

Every authority scan or search is recorded with: who, when, where (GPS), and which rider. Riders and stage heads can see when an authority looked up a record. The admin can review logs to detect misuse.

### 6.2 Abuse prevention

- Rate limiting on plate searches per device or phone number.
- OTP or captcha after repeated searches.
- Alerts for unusual search patterns.
- Exact-match plate search only, so the database cannot be browsed.

### 6.3 Fake QR protection

- QR codes are signed and verified against the server.
- The scan page always shows the rider's photo, so the passenger can compare it with the person in front of them.
- Links always use the official KNR domain.

### 6.4 Data protection

KNR stores sensitive personal data (national ID numbers, photos, phone numbers). The project must:

- Review the obligations under Uganda's **Data Protection and Privacy Act** (including any registration or consent requirements) before launch.
- Obtain rider consent at registration.
- Define data retention: what happens to rider data if a stage stops paying (deleted, frozen, or retained), and state this in the terms of service.
- Encrypt data in transit and at rest.
- Limit data shared with each authority to what that role needs.

### 6.5 Agreements

Formal agreements with the police, local authorities, and other bodies should be pursued before or at launch, so the QR code and data are recognized and trusted.

---

## 7. Technical Considerations

> This section describes requirements and open choices. The final stack and database design will be decided in a later document.

### 7.1 Platform

- **Mobile app** (primary), for stage heads, authorities, and optionally riders.
- **Lightweight web page** for passengers who scan a QR or search a plate (no install needed).
- **Backend API and database** hosted and owned by the platform owner.
- **Admin dashboard** (web) for the platform owner.

### 7.2 Uganda-specific requirements

- **Offline-first registration:** stage heads can register riders without internet, and data syncs later.
- **Low-data use:** compress photos, keep the scan page light.
- **SMS and USSD fallback** for basic-phone users where practical.
- **Mobile money integration** (MTN MoMo, Airtel Money).
- **Languages:** English and Luganda at minimum, with other languages later.

### 7.3 Main data entities (preliminary)

- Stage
- Rider
- Bike
- Document (permit, insurance) with expiry
- User (stage head, official, authority, admin)
- Organization and Role
- Permission
- Incident / Report
- Scan and Search log
- Subscription and Payment
- Notification

---

## 8. Business Model

The platform owner stores the data, and stage heads pay for the service.

| Option | Description |
|---|---|
| **Free tier** | A small number of riders per stage (for example, 10) so stages can try the service |
| **Subscription** | Monthly or yearly fee per stage, scaled by number of riders, paid by mobile money |
| **Per-rider fee** | Small one-time or yearly fee per QR ID issued or renewed |
| **Add-ons** | Printed ID cards or stickers, SMS announcements, dues collection, reports |
| **Partnerships (later)** | Insurers, SACCOs, and financiers, possibly with referral fees |

Pricing amounts are to be decided after talking to stage heads. Police and local authority access is expected to be free, since their participation is what makes the QR code valuable. Insurers are the most likely authority-type group to pay.

---

## 9. Release Plan

### Version 1 (launch)

1. Stage head sign-up and stage creation
2. Rider registration with photo, bike, and documents
3. QR code and printable ID card generation
4. Public scan page and number plate search
5. Authority accounts for traffic police and LC officials, with scan logging
6. Expiry reminders
7. Reporting incidents and flagging stolen bikes
8. Mobile money subscription

### Version 2

- Dues and savings tracking
- SMS announcements to riders
- Municipal (KCCA) dashboard and area reports
- Offline authority checks
- Ride sharing and the emergency button polish
- Rider self-service profile

### Version 3

- Insurer and licensing body access
- URA reports
- Ratings and rider reputation
- SACCO, loan, and insurance partnerships

The roles and permissions are designed for all authority types from the start, so adding groups later does not require rebuilding the system.

---

## 10. Risks

| Risk | Mitigation |
|---|---|
| Riders and stage heads fear the app is a tool for police harassment | Clear limits on what authorities can see, and visible logging of every authority lookup |
| Authorities do not recognize the QR code | Formal agreements and early partnerships |
| Data protection or legal non-compliance | Legal review before launch, consent at registration |
| Fake QR codes or fake registrations | Signed QR codes, photo check, admin audit trail |
| Low smartphone and internet access | Offline-first design, USSD or SMS fallback |
| Stage heads stop paying after the trial | Features they use daily: dues tracking, expiry reminders, reports |
| "Not found" read as "illegal rider" | Careful wording: "Not registered on this system" |
| Abuse of authority access | Audit logs, jurisdiction limits, suspension of misused accounts |

---

## 11. Open Questions

1. Will the first customers be individual stages, or associations covering many stages?
2. Will the rider's phone number be hidden, shown, or chosen by the rider on the public view?
3. What do the subscription fees look like in UGX, and what do stage heads currently collect as dues?
4. Which authorities will be signed up first, and are agreements possible with them?
5. What happens to rider data if a stage stops paying?
6. What are the exact registration and consent requirements under Uganda's data protection law?
7. Which technology stack and hosting will be used for the app and backend?

---

## 12. Next Steps

1. Review and confirm this document.
2. Talk to a few stage heads, riders, and an LC or police officer to test the assumptions, especially pricing and what each group is willing to share.
3. Prepare the database design and the permissions table.
4. Design the screen flow for each user type.
5. Choose the technology stack.
6. Build a first prototype covering registration, QR, scan page, and plate search.
