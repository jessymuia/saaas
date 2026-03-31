import type { Metadata } from "next";
import Link from "next/link";

export const metadata: Metadata = {
  title: "Pricing – PropManage SaaS",
  description:
    "Simple, transparent pricing for property professionals. Starter from KES 2,499/month. 30-day free trial, no credit card required.",
};

const plans = [
  {
    name: "Starter",
    price: "KES 2,499",
    period: "/month",
    annualPrice: "KES 24,999/year",
    description: "Perfect for individual landlords managing a small residential portfolio.",
    units: "Up to 10 units",
    highlight: false,
    features: [
      "Up to 10 rental units",
      "M-Pesa & bank payment collection",
      "Digital lease generation",
      "Tenant portal access",
      "Maintenance request management",
      "Basic financial reports",
      "Email support",
      "30-day free trial",
    ],
    notIncluded: [
      "Multi-landlord management",
      "API access",
      "Custom branding",
      "Dedicated account manager",
    ],
    cta: "Start Free Trial",
    href: "/demo",
  },
  {
    name: "Growth",
    price: "KES 7,999",
    period: "/month",
    annualPrice: "KES 79,999/year",
    description: "For property agencies and serious landlords growing their portfolio.",
    units: "Up to 50 units",
    highlight: true,
    badge: "Most Popular",
    features: [
      "Up to 50 rental units",
      "Everything in Starter",
      "Multi-landlord management",
      "Advanced analytics & forecasting",
      "Automated SMS & email campaigns",
      "Contractor management module",
      "KRA-ready financial reports",
      "Priority email & chat support",
      "Tenant screening integration",
    ],
    notIncluded: [
      "White-label branding",
      "Dedicated account manager",
    ],
    cta: "Start Free Trial",
    href: "/demo",
  },
  {
    name: "Professional",
    price: "KES 19,999",
    period: "/month",
    annualPrice: "KES 199,999/year",
    description: "For established agencies managing large or multiple portfolios.",
    units: "Up to 200 units",
    highlight: false,
    features: [
      "Up to 200 rental units",
      "Everything in Growth",
      "White-label branding",
      "API access & webhooks",
      "Custom lease templates",
      "Advanced user permissions",
      "Accounting software integration",
      "Phone support",
      "Monthly review calls",
    ],
    notIncluded: [
      "Unlimited units",
      "Dedicated account manager",
    ],
    cta: "Start Free Trial",
    href: "/demo",
  },
  {
    name: "Enterprise",
    price: "Custom",
    period: "",
    annualPrice: "Annual contracts available",
    description: "For large real estate companies, developers, and corporate facilities managers.",
    units: "Unlimited units",
    highlight: false,
    features: [
      "Unlimited units & properties",
      "Everything in Professional",
      "Dedicated account manager",
      "Custom integrations & workflows",
      "On-premise deployment option",
      "SLA-backed 99.9% uptime",
      "Staff training & onboarding",
      "24/7 priority support",
      "Quarterly business reviews",
    ],
    notIncluded: [],
    cta: "Contact Sales",
    href: "/contact",
  },
];

const faqs = [
  { q: "Is there a free trial?", a: "Yes — all paid plans include a 30-day free trial. No credit card is required to start." },
  { q: "Can I change plans later?", a: "Absolutely. Upgrade or downgrade at any time. Prorated billing ensures you only pay for what you use." },
  { q: "What payment methods do you accept?", a: "We accept M-Pesa, credit/debit cards, and bank transfers. Annual plans can be paid by cheque or EFT." },
  { q: "Are there per-transaction fees?", a: "No. PropManage charges a flat monthly fee with no per-transaction or per-payment fees." },
  { q: "Is my data safe?", a: "Yes. We use 256-bit SSL encryption, daily backups, and are fully compliant with the Kenya Data Protection Act 2019." },
];

export default function PricingPage() {
  return (
    <>
      <section className="bg-gradient-to-br from-blue-700 to-indigo-700 text-white py-20 px-4 text-center">
        <div className="max-w-3xl mx-auto">
          <h1 className="text-4xl sm:text-5xl font-extrabold mb-4">Simple, Transparent Pricing</h1>
          <p className="text-xl text-blue-100">
            No hidden fees. No per-transaction charges. Upgrade or downgrade any time.
          </p>
        </div>
      </section>

      <section className="py-20 px-4 bg-gray-50">
        <div className="max-w-7xl mx-auto">
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            {plans.map((plan) => (
              <div
                key={plan.name}
                className={`rounded-2xl border p-7 flex flex-col ${
                  plan.highlight
                    ? "bg-blue-600 border-blue-600 text-white shadow-2xl scale-105"
                    : "bg-white border-gray-200 text-gray-900"
                }`}
              >
                {plan.badge && (
                  <span className="bg-yellow-400 text-yellow-900 text-xs font-bold px-2 py-1 rounded-full self-start mb-3">
                    {plan.badge}
                  </span>
                )}
                <h2 className={`font-bold text-lg mb-1 ${plan.highlight ? "text-white" : "text-gray-900"}`}>
                  {plan.name}
                </h2>
                <p className={`text-3xl font-extrabold mb-0 ${plan.highlight ? "text-white" : "text-gray-900"}`}>
                  {plan.price}
                  <span className={`text-sm font-normal ${plan.highlight ? "text-blue-200" : "text-gray-500"}`}>
                    {plan.period}
                  </span>
                </p>
                <p className={`text-xs mb-2 ${plan.highlight ? "text-blue-200" : "text-gray-400"}`}>
                  {plan.annualPrice}
                </p>
                <p className={`text-sm mb-4 ${plan.highlight ? "text-blue-100" : "text-gray-600"}`}>
                  {plan.description}
                </p>
                <p className={`text-xs font-semibold uppercase mb-4 ${plan.highlight ? "text-blue-200" : "text-gray-500"}`}>
                  {plan.units}
                </p>

                <ul className="space-y-2 mb-6 flex-1">
                  {plan.features.map((f) => (
                    <li key={f} className="flex items-start gap-2 text-sm">
                      <svg className={`w-4 h-4 mt-0.5 flex-shrink-0 ${plan.highlight ? "text-blue-200" : "text-blue-500"}`} fill="currentColor" viewBox="0 0 20 20">
                        <path fillRule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clipRule="evenodd" />
                      </svg>
                      <span className={plan.highlight ? "text-blue-50" : "text-gray-700"}>{f}</span>
                    </li>
                  ))}
                  {plan.notIncluded.map((f) => (
                    <li key={f} className="flex items-start gap-2 text-sm opacity-40">
                      <svg className="w-4 h-4 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fillRule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clipRule="evenodd" />
                      </svg>
                      <span>{f}</span>
                    </li>
                  ))}
                </ul>

                <Link
                  href={plan.href}
                  className={`block text-center font-bold py-3 rounded-xl transition-colors ${
                    plan.highlight
                      ? "bg-white text-blue-700 hover:bg-blue-50"
                      : "bg-blue-600 text-white hover:bg-blue-700"
                  }`}
                >
                  {plan.cta}
                </Link>
              </div>
            ))}
          </div>
        </div>
      </section>

      <section className="py-20 px-4 bg-white">
        <div className="max-w-3xl mx-auto">
          <h2 className="text-2xl font-bold text-gray-900 mb-8 text-center">Pricing FAQ</h2>
          <div className="space-y-5">
            {faqs.map((faq) => (
              <div key={faq.q} className="border border-gray-200 rounded-xl p-6">
                <h3 className="font-semibold text-gray-900 mb-2">{faq.q}</h3>
                <p className="text-gray-600 text-sm">{faq.a}</p>
              </div>
            ))}
          </div>
        </div>
      </section>
    </>
  );
}
