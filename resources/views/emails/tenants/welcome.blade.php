<x-mail::message>
# Welcome to PropManage, {{ $user->name }}!

Your PropManage account has been created and is ready to use. Below are your login credentials — please save them somewhere safe.

<x-mail::panel>
## Your Login Credentials

| | |
|---|---|
| **Login URL** | [{{ $loginUrl }}]({{ $loginUrl }}) |
| **Email** | {{ $user->email }} |
| **Password** | `{{ $plainPassword }}` |

**We strongly recommend changing your password after your first login.**
</x-mail::panel>

<x-mail::button :url="$loginUrl" color="primary">
Log In Now
</x-mail::button>

---

## Getting Started

Here are three steps to get up and running quickly:

<x-mail::table>
| Step | Action | Description |
|:----:|--------|-------------|
| 1 | Add Properties | Set up your first property and units |
| 2 | Add Tenants | Create tenant profiles and link them to units |
| 3 | Configure M-Pesa | Connect your M-Pesa Paybill for automated payments |
</x-mail::table>

## What PropManage Does For You

- **Automated Rent Collection** — M-Pesa payments reconciled in real time
- **Invoice Management** — Automatic rent invoices and payment reminders
- **Lease Management** — Digital lease agreements with e-signature support
- **Financial Reporting** — Monthly income statements and landlord disbursements
- **Maintenance Tracking** — Log and track maintenance requests end-to-end

@if(file_exists(storage_path('app/public/onboarding_manual.pdf')))
We've attached the **PropManage Onboarding Guide** to help you get started quickly.
@endif

## Need Help?

Our support team is available Monday–Friday, 8 AM–6 PM EAT, and Saturdays 9 AM–1 PM.

- **Email:** support@propertysasa.com
- **Phone:** +254 700 000 000

Warm regards,
**The PropManage Team**

<x-mail::subcopy>
You received this email because a PropManage account was created using this email address. If you did not request an account, please contact us at support@propertysasa.com immediately.
</x-mail::subcopy>
</x-mail::message>
