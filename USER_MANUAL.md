# User Manual

**Alendi Estates — Rental Management System**

This guide is for **landlords** and **tenants**. It explains how to use the system as it works today.

It is not a technical document. If a feature is not listed here, it is not available in the current system.

---

## Getting Started

### How to access the system

Open the website in a browser. The home page is titled **Alendi Estates** and asks you to choose a portal:

- **Landlord** — for hostel owners who manage properties, tenants, and payments
- **Tenant** — for people living in a hostel who need to record rent or check payment history

There is also a separate **administrator** login used by the organisation that runs the system. Ordinary landlords and tenants do not use that login.

### How to log in (landlords)

1. On the home page, choose **Landlord**, or go to the landlord login page.
2. Enter your **username or email** and your **password**.
3. Select **Log in**.

If you type the wrong details several times, the system may temporarily lock further attempts. Wait and try again, or use **Forgot password** if that link is shown.

**Tenants do not log in.** You do not get a username. You use your **tenant code** on the tenant pages instead.

### What you see after logging in (landlords)

After a successful login you land on the **Dashboard**. It shows two numbers:

- **Hostels** — how many properties you have
- **Tenants** — how many tenants are linked to those hostels

On the left (or behind the menu button on a phone) you can open:

- Dashboard
- Hostels
- Add Tenant
- Payments
- Archive (archived hostels and archived tenants)

Use **Log out** when you finish.

### What tenants see

Tenants are not taken to a private account. The tenant portal has two jobs:

- **Record Payment** — submit rent with a screenshot
- **Payment History** — look up past submissions using your tenant code

---

## Landlord Dashboard

### Dashboard overview

The dashboard is a summary only. It does not list individual tenants or payments. Use **Hostels** and **Payments** for day-to-day work.

### Important statistics

| Figure | Meaning |
|--------|---------|
| Hostels | Number of properties (hostels) under your account |
| Tenants | Number of tenant records linked to your hostels |

There is no separate “active tenants” card on the dashboard in the current system.

### Navigation

- **Hostels** — add, view, and edit properties; open a hostel to see its tenants
- **Add Tenant** — type a tenant in yourself, or create a registration link to send to a student
- **Payments** — review rent submissions and approve or reject them
- **Archive** — records you removed from the main lists

On a phone, tap the menu icon in the header to open this list.

### Notifications

Landlords **do not** currently have an in-app notification bell.

You may receive **email** if:

- An administrator created your account (welcome email with login details)
- You requested a **password reset**

You will **not** get an automatic email when a tenant registers or when a payment is submitted. Check **Payments** and each hostel’s tenant list yourself.

### Tenant and property information on the dashboard

The dashboard does not show names. Open **Hostels**, then open a hostel to see tenant names, codes, phones, emails, and whether rent for a month is marked Paid or Unpaid.

---

## Managing Properties (Hostels)

In the landlord menu, properties are called **Hostels**.

### Adding a property

1. Open **Hostels**.
2. Choose the option to add / create a hostel.
3. Enter:
   - **Name** (required)
   - **Address** (optional)
   - **Description** (optional)
   - **Monthly rent** (required)
   - **Maximum number of tenants** (required)
4. Save.

The system stores the hostel under your account, marks it active, and prepares a registration link for tenants.

### Viewing properties

Open **Hostels** to see your list. Open a hostel to see its tenants, registration status (open or closed), and payment filters.

### Editing property information

Open the hostel’s edit screen to change name, address, description, monthly rent, and maximum tenants.

Changing monthly rent **does not automatically change** rent already saved on existing tenant records. New tenants usually receive the hostel’s current rent.

### Other hostel functions that exist today

- **Active / inactive** — you can switch a hostel’s status. Inactive hostels cannot be used for public registration.
- **Open / close registration** — on the hostel page you can open or close the public registration link. Closed registration shows a closed message to anyone who still has the link.
- **Archive** — remove a hostel from the main list. You can restore it later from **Archive**.
- **PDF list** — from a hostel’s tenant list you can download a PDF of tenants (including payment filters you selected).

The system stops new registrations when the number of current tenants reaches the maximum you set.

---

## Registering Tenants

You can add a tenant yourself, or send a link so the tenant fills in their own details.

### Option A — Send a registration link (usual for students)

1. Log in as a landlord.
2. Open **Add Tenant**.
3. Select the hostel.
4. Generate the registration link (the page requests a link from the system and shows it).
5. Copy the link and send it to the tenant (for example by WhatsApp or email).
6. The tenant opens the link in a browser.
7. The tenant enters:
   - Full name
   - Email
   - Malawi phone number
   - Move-in date
   - Optional custom monthly rent (if you allow them to type a different amount)
8. The tenant submits the form.
9. If the information is valid, the system creates the tenant record and shows a **tenant code**.
10. The tenant should **write down or screenshot that code**. They need it to record payments.
11. You will see the tenant when you open that hostel.

The link only works if:

- The hostel is active
- Registration is **open**
- The hostel is not full
- The phone number is a valid Malawi mobile number (starting with 08 or 09)
- That phone is not already used by another tenant **in the same hostel**
- The email is not already used by another current tenant

If registration is closed, the tenant sees a closed page. If the hostel is full, they see a full page.

### Option B — Add the tenant yourself

1. Open **Add Tenant**.
2. Choose the hostel.
3. Enter name, email, phone, and move-in date.
4. Save.

The system creates the tenant and generates a tenant code automatically. Move-in date must be today or a future date.

### After registration

The tenant appears on that hostel’s tenant list. You can open the tenant to see their details and payment records.

---

## Tenant Information

On a hostel’s tenant list you can typically see:

- Name
- Tenant code
- Phone
- Email
- Monthly rent
- Status (for example active)
- Whether they are **Paid** or **Unpaid** for the month you selected in the filter

You can:

- Open a tenant to view and search their payment records
- Edit tenant details (within your own hostels)
- Archive a tenant
- Restore an archived tenant
- Mark a tenant as moved out (the record is removed from the active list)

You cannot see tenants who belong to another landlord.

Administrators of the whole system can see tenants across landlords. That is not part of the landlord login.

---

## Recording Rent Payments

### Who records a payment?

**The tenant** records the payment on the public tenant pages.

Landlords do **not** type payments in on behalf of tenants in the current system. Landlords **approve** or **reject** what the tenant submitted.

### How a tenant records a payment

1. Open the home page → **Tenant**, or go to the tenant portal.
2. Choose **Record Payment**.
3. Enter your **tenant code** (from registration).
4. Choose the **first month** you are paying for.
5. Choose how many months (1 to 12). The system fills the following months automatically.
6. Enter the **amount**. It must equal your monthly rent multiplied by the number of months.
7. Upload a **screenshot** of the payment (an image file).
8. Submit.

If everything is correct, you see a success message. The payment is stored as **Pending** until the landlord reviews it.

### Required information

- Tenant code
- Payment month
- Amount (must match rent × number of months)
- Screenshot

### How payment amounts are stored

The amount is stored as a single total for all months included in that submission. The months are stored together (for example August and September as one record).

The system does **not** keep a running “balance due” account. It checks that this submission’s amount matches rent times the number of months.

### How outstanding balances are calculated

**The system does not calculate an outstanding balance** (no “you still owe MK …”).

Landlords judge whether a tenant is Paid or Unpaid **for a chosen month** by looking at approved payments for that month.

### How payment history can be viewed

**Tenant:** Tenant portal → **Payment History** → enter tenant code.

**Landlord:**

- **Payments** — all submissions for your hostels, with filters
- Open a payment to see details and the screenshot
- Open a tenant to see that tenant’s payment list

---

## Payment Status

### Statuses on each payment record

These are the statuses the system actually uses:

| Status | Meaning |
|--------|---------|
| **Pending** | Submitted. Waiting for the landlord to review. |
| **Approved** | Landlord accepted the payment. |
| **Rejected** | Landlord did not accept it. The landlord may add a remark. |

Once a payment is approved or rejected, it cannot be processed again.

### Paid and Unpaid on the hostel tenant list

These are **not** stored as extra statuses on the tenant. They are a view of a selected month:

| Label | Meaning |
|-------|---------|
| **Paid** | There is an **Approved** payment that includes that month |
| **Unpaid** | There is no approved payment covering that month |

A **Pending** payment does **not** make the tenant show as Paid.

**Partially Paid** is not used in this system.

### Duplicate months

You cannot submit another payment for a month that already has a **Pending** or **Approved** payment. If a payment was **Rejected**, a new submission for that month is allowed.

---

## Tenant Usage

This is everything tenants can do **today**:

1. Open a **registration link** from the landlord and submit their details (when the hostel is open and not full).
2. See a **success page** with their **tenant code**.
3. Open the **tenant portal** (no password).
4. **Record a payment** with tenant code, months, amount, and screenshot.
5. **View payment history** by entering the tenant code (Pending, Approved, and Rejected).

Tenants **cannot**:

- Log in with a username and password
- Edit their profile after registration
- Approve payments
- See other tenants
- Download landlord PDF reports
- Receive in-app notifications

Keep the tenant code private. Anyone who has it can submit a payment in that tenant’s name or view that tenant’s payment history.

---

## Notifications

### Landlords

There is no notification list inside the landlord portal.

Check **Payments** regularly for new **Pending** items.

Email is only used for account welcome (sent when an administrator creates you) and password reset.

### Tenants

No notification emails are sent when a payment is approved or rejected. Use **Payment History** to see the result.

### Administrators

Administrators see a **Recent Activity** list (latest properties and tenants). That list is not a personal inbox and does not mark items as read. Landlords do not see that list.

---

## Reports & PDF Exports

### Landlord

From a hostel’s tenant list, use the PDF download button. If you filtered by month or Paid/Unpaid, the PDF follows those filters.

There is **no Excel download** in the current system.

### Administrator

Administrators can export a tenant PDF from the admin tenants page (with optional search/landlord filters).

### How to use a PDF

Download the file and open it with any PDF reader. Use it for printing or sharing a snapshot of who is paid for a month. It is a copy of the data at the moment you clicked export; it does not update itself later.

If a PDF does not download, try again from the hostel’s tenant list, or ask the administrator. The server must be allowed to create PDF files.

---

## Account & Security

### Logging in (landlords)

Use the landlord login with username or email and password. Do not use the admin login page.

### Password management

- If you forget your password, use the landlord **Forgot password** page. If an active landlord account exists for that email, a reset link is sent. For privacy, the site shows the same message whether the email exists or not.
- The reset link expires after **60 minutes**.
- An administrator can also reset your password. They should give you the new password through a secure channel. Change it if you are asked to.

There is no separate “change password” page while you are already logged in. Use forgot-password or ask an administrator.

### Keeping login credentials safe

- Do not share your password.
- Log out on shared computers.
- Tenants: do not share your **tenant code**. It works like a password for payments and history.

### Permissions

- You only see **your** hostels, tenants, and payments.
- You cannot open another landlord’s records.
- If an administrator **deactivates** your account, you cannot log in until they activate it again.

### Logging out

Use **Log out** in the landlord area. Always log out on a phone or computer that other people use.

---

## Common Problems

### I cannot log in

- Confirm you are on the **landlord** login page, not the admin page.
- Check caps lock and that you are using the username **or** the email the administrator gave you.
- If you see a message about too many attempts, wait and try later.
- If you see that the account is deactivated, contact the administrator.
- Use **Forgot password** if you no longer have the password.

Tenants: you are not supposed to log in. Use the tenant portal and your tenant code.

### My tenant registration link does not work

Ask the landlord to check:

- The hostel is **active**
- **Registration is open**
- The hostel is **not full**
- You are using the full link, with no missing characters
- You are not registering the same phone twice in that hostel

If the hostel was closed or filled after the link was sent, the landlord must open registration or free a space.

### A tenant is not appearing

- Open the correct **hostel**, not a different one.
- Refresh the page.
- Check **Archive** — the tenant may have been archived or marked moved out.
- Confirm the tenant completed registration and saw the success page.
- Confirm the tenant used **your** link, not another landlord’s link.

### A payment is showing the wrong status

- **Pending** means you have not approved or rejected it yet.
- **Paid** on the tenant list only counts **Approved** payments for the month you filtered. Choose the correct month.
- Paying two months in one submission marks **both** months when approved.
- Amount mistakes: the tenant must send the exact rent × months. Wrong amounts are rejected at submit time, so they never appear as a payment.

Landlords cannot edit the amount after submit. If the screenshot is wrong, **Reject** it (optionally with a remark) so the tenant can submit again.

### I cannot access a page

- Your session may have expired. Log in again.
- You may be opening a link that belongs to another landlord (the system will refuse it).
- On a phone, use the menu button to reach Hostels, Add Tenant, and Payments.

### A report / PDF is not generating

- Stay logged in and try the PDF button on the **hostel tenant list**.
- If nothing downloads, ask the administrator. The server must be allowed to create PDF files.
- Excel export is not available.

### Payment screenshot does not show

Tell the administrator. Screenshots are stored as files on the server. If the file is missing, the payment record can still exist without a visible image.

### I did not receive a welcome or reset email

- Check spam.
- Confirm the administrator used the correct email address.
- Password reset always shows a success message; email is only sent if an active landlord account exists.
- If mail is not configured on the server, no email will arrive. Ask the administrator to send the password another way.

---

## Important Notes

### For landlords

- Keep tenant names, phones, and emails accurate. The tenant code is how they prove who they are.
- Share registration links only with the intended student. Anyone with the link can try to register while it is open.
- Close registration when you are not accepting new tenants.
- Do not approve a payment until you have checked the **screenshot**, **amount**, and **months**.
- Approval cannot be undone in the system. Reject if anything is wrong.
- Monthly rent on the hostel is the default for new tenants. Custom rent (if entered at registration) is stored on that tenant only.
- Archived records are hidden from normal lists but can be restored unless an administrator permanently deletes them.
- The payment month filters currently cover a fixed range of months set in the system (from 2026 into 2027). If you need a month outside that list, contact the administrator.

### For tenants

- Save your **tenant code** as soon as you register.
- Pay using the amount the system expects (rent × months).
- After you submit, wait for the landlord to approve. Check **Payment History** instead of submitting twice for the same month.
- If a payment is rejected, read any remark and submit again with the correct proof.

### For everyone

- This system records **proof of payment**. It does not move money by itself. Actual bank or mobile-money transfer happens outside the website.
- Use a current web browser. On a phone, use the menu to reach landlord pages.

---

*This manual matches the current Alendi Estates rental system. If a screen you see does not match this guide, follow the screen and ask your administrator.*
