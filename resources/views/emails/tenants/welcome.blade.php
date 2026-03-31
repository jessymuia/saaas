<x-mail::message>
# Welcome to PropManage, {{ $user->name }}! 🎉

Thank you for joining **PropManage SaaS** — Kenya's leading property management platform.

Your account is now active. Here's what you can do next:

<x-mail::panel>
**Your Login Details**

- **Platform URL:** {{ config('app.url') }}
- **Email:** {{ $user->email }}

*Use the password you set during registration.*
</x-mail::panel>

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
- **Tenant Portal** — Self-service portal for tenants to view invoices and payment history
- **Maintenance Tracking** — Log and track maintenance requests end-to-end

@if(file_exists(storage_path('app/public/onboarding_manual.pdf')))
We've attached the **PropManage Onboarding Guide** to this email to help you get started quickly.
@endif

<x-mail::button :url="config('app.url')" color="primary">
Log In to PropManage
</x-mail::button>

## Need Help?

Our support team is available Monday–Friday, 8 AM–6 PM EAT, and Saturdays 9 AM–1 PM.

- **Email:** support@propertysasa.com
- **Phone:** +254 700 000 000

We're excited to have you on board. Let's simplify property management together.

Warm regards,
**The PropManage Team**

<x-mail::subcopy>
You received this email because you created a PropManage account. If you did not sign up, please contact us at support@propertysasa.com immediately.
</x-mail::subcopy>
</x-mail::message>
