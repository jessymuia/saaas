import type { Metadata } from "next";

export const metadata: Metadata = {
  title: "FAQ – PropManage SaaS",
  description: "Frequently asked questions about PropManage, pricing, features, data security, and getting started.",
};

const faqGroups = [
  {
    category: "Getting Started",
    questions: [
      { q: "How do I get started with PropManage?", a: "Sign up for a free 30-day trial at propmanage.co.ke—no credit card required. After creating your account, the onboarding wizard walks you through adding your first property, inviting tenants, and setting up payment collection. Most users are fully operational within one hour." },
      { q: "Do I need technical skills to use PropManage?", a: "Not at all. PropManage is designed to be used by property professionals, not developers. If you can send an email and use a smartphone, you can use PropManage. Our onboarding team is also available to help you get set up." },
      { q: "Can I import my existing data?", a: "Yes. PropManage supports bulk import of properties, units, and tenant data via Excel/CSV files. Our onboarding team can assist with data migration for customers on Growth plans and above." },
      { q: "Is there a mobile app?", a: "Yes. PropManage has mobile apps for iOS and Android, available for free download. Landlords and property managers access the full management dashboard, while tenants have a dedicated app for paying rent, submitting maintenance requests, and communicating with management." },
    ],
  },
  {
    category: "Payments & M-Pesa",
    questions: [
      { q: "How does M-Pesa integration work?", a: "PropManage integrates directly with Safaricom's M-Pesa API. You can set up a dedicated Paybill number or use our shared Paybill with unique account codes for each tenant. When a tenant pays, PropManage automatically reconciles the payment against the correct unit and sends a receipt." },
      { q: "Are there transaction fees for M-Pesa payments?", a: "PropManage itself does not charge per-transaction fees—you pay a flat monthly subscription. Standard M-Pesa transaction charges as set by Safaricom apply for the tenant sending money." },
      { q: "What other payment methods are supported?", a: "In addition to M-Pesa, PropManage supports bank transfers (EFT), debit/credit cards via our PCI DSS-compliant payment gateway, and cheque recording. You can enable or disable payment methods per property." },
      { q: "How are partial payments handled?", a: "PropManage fully supports partial payments. When a partial payment is received, it is recorded against the tenant's account and the outstanding balance is tracked automatically. Automated reminders for the remaining balance can be configured." },
    ],
  },
  {
    category: "Pricing & Plans",
    questions: [
      { q: "Is there a free trial?", a: "Yes. All paid plans include a 30-day free trial. No credit card is required to start your trial." },
      { q: "Can I change my plan?", a: "Yes, you can upgrade or downgrade your plan at any time from your account settings. When upgrading, you'll be charged the pro-rated difference for the remaining days in your billing period. When downgrading, the new lower rate takes effect from the next billing period." },
      { q: "What happens if I exceed my unit limit?", a: "You'll receive a notification when you approach your plan's unit limit. Your account will not be suspended immediately—we'll work with you to upgrade to the appropriate plan." },
      { q: "Do you offer annual billing discounts?", a: "Yes. Annual plans are offered at a discount equivalent to approximately 2 months free compared to monthly billing. Annual plans can be paid by M-Pesa, bank transfer, or card." },
    ],
  },
  {
    category: "Data & Security",
    questions: [
      { q: "Is my data secure?", a: "Yes. PropManage uses 256-bit SSL/TLS encryption for all data in transit and AES-256 encryption for data at rest. We are fully compliant with the Kenya Data Protection Act 2019 and perform regular security audits and penetration tests." },
      { q: "Who owns my data?", a: "You own your data entirely. PropManage acts as a data processor on your behalf. You can export all your data at any time in standard formats, and if you choose to leave PropManage, we will provide a full data export and then delete your data from our systems as required by the KDPA 2019." },
      { q: "Is PropManage KDPA 2019 compliant?", a: "Yes. PropManage is fully compliant with the Kenya Data Protection Act 2019. We are registered with the Office of the Data Protection Commissioner (ODPC) and have appointed a Data Protection Officer. For details, see our Security & Compliance page." },
      { q: "Where is my data stored?", a: "Your data is stored on servers in Kenya and the EU, in compliance with the Kenya Data Protection Act 2019's provisions on data residency. All cross-border data transfers are protected by appropriate legal mechanisms." },
    ],
  },
  {
    category: "Support",
    questions: [
      { q: "What support options are available?", a: "All plans include email support. Growth and above include live chat support during business hours. Professional and Enterprise plans include phone support and dedicated account management." },
      { q: "What are your support hours?", a: "Our support team is available Monday–Friday 8 AM–6 PM EAT and Saturday 9 AM–1 PM EAT. Enterprise customers receive 24/7 priority support." },
      { q: "Is there training available?", a: "Yes. We offer self-service training through our knowledge base, video tutorials, and webinars. For Growth and above, onboarding training sessions are included. Enterprise customers receive customised staff training." },
    ],
  },
];

export default function FAQPage() {
  return (
    <>
      <section className="bg-gradient-to-br from-blue-700 to-indigo-700 text-white py-20 px-4 text-center">
        <div className="max-w-3xl mx-auto">
          <h1 className="text-4xl sm:text-5xl font-extrabold mb-4">Frequently Asked Questions</h1>
          <p className="text-xl text-blue-100">Everything you need to know about PropManage.</p>
        </div>
      </section>

      <section className="py-20 px-4 bg-white">
        <div className="max-w-4xl mx-auto space-y-14">
          {faqGroups.map((group) => (
            <div key={group.category}>
              <h2 className="text-xl font-bold text-gray-900 mb-6 pb-3 border-b border-gray-200">
                {group.category}
              </h2>
              <div className="space-y-5">
                {group.questions.map((faq) => (
                  <div key={faq.q} className="border border-gray-100 rounded-xl p-6 bg-gray-50">
                    <h3 className="font-semibold text-gray-900 mb-2">{faq.q}</h3>
                    <p className="text-gray-600 text-sm leading-relaxed">{faq.a}</p>
                  </div>
                ))}
              </div>
            </div>
          ))}
        </div>
      </section>
    </>
  );
}
