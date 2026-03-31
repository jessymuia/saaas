import type { Metadata } from "next";
import Link from "next/link";

export const metadata: Metadata = {
  title: "PropManage SaaS – Modern Property Management Platform for Africa",
  description:
    "PropManage is the leading multi-tenant property management SaaS platform. Automate rent collection, manage maintenance requests, track leases, and communicate with tenants—all from one dashboard.",
};

const features = [
  {
    icon: "🏢",
    title: "Multi-Property Dashboard",
    description:
      "Manage hundreds of properties, units, and portfolios from a single, intuitive dashboard. Real-time occupancy rates, revenue tracking, and performance metrics at your fingertips.",
  },
  {
    icon: "💳",
    title: "Automated Rent Collection",
    description:
      "Accept M-Pesa, bank transfers, and card payments. Automated reminders, receipts, and reconciliation eliminate manual chasing and reduce late payments by up to 60%.",
  },
  {
    icon: "🔧",
    title: "Maintenance Management",
    description:
      "Tenants submit maintenance requests via mobile or web. Track work orders from submission to completion, assign contractors, and keep landlords informed in real time.",
  },
  {
    icon: "📄",
    title: "Digital Lease Management",
    description:
      "Generate, sign, and store lease agreements digitally. Automated renewals, escalation clauses, and expiry alerts keep your portfolio legally protected and fully compliant.",
  },
  {
    icon: "📊",
    title: "Financial Reporting",
    description:
      "Income statements, expense tracking, tax summaries, and cash-flow reports built for Kenyan property regulations. Export to PDF or Excel for your accountant in seconds.",
  },
  {
    icon: "💬",
    title: "Tenant Communication",
    description:
      "Built-in messaging, broadcast announcements, and notice boards. Send SMS or email notifications for rent due dates, maintenance updates, and community events.",
  },
];

const testimonials = [
  {
    name: "Amina Wanjiku",
    role: "Portfolio Landlord, Nairobi",
    quote:
      "PropManage cut my rent collection time from three weeks to three days. The M-Pesa integration alone paid for the entire year's subscription in the first month.",
    avatar: "AW",
  },
  {
    name: "David Otieno",
    role: "Property Agent, Mombasa",
    quote:
      "Managing 14 different landlords and 200+ units used to require a team of five. With PropManage, my team of two handles everything seamlessly.",
    avatar: "DO",
  },
  {
    name: "Grace Kamau",
    role: "Real Estate Developer, Nakuru",
    quote:
      "The lease management and automated renewal reminders have saved us from so many costly oversights. I can't imagine running our development without it.",
    avatar: "GK",
  },
];

const stats = [
  { value: "12,000+", label: "Properties Managed" },
  { value: "KES 4.2B+", label: "Rent Collected" },
  { value: "98.7%", label: "Uptime Guarantee" },
  { value: "47,000+", label: "Tenants Served" },
];

export default function HomePage() {
  return (
    <>
      {/* Hero */}
      <section className="bg-gradient-to-br from-blue-700 via-blue-600 to-indigo-700 text-white py-24 px-4">
        <div className="max-w-7xl mx-auto text-center">
          <span className="inline-block bg-blue-500/40 border border-blue-400/50 text-blue-100 text-xs font-semibold px-3 py-1 rounded-full mb-6 uppercase tracking-wider">
            Africa's #1 Property Management Platform
          </span>
          <h1 className="text-4xl sm:text-5xl lg:text-6xl font-extrabold leading-tight mb-6">
            Manage Every Property.
            <br />
            <span className="text-blue-200">Effortlessly.</span>
          </h1>
          <p className="text-xl text-blue-100 max-w-3xl mx-auto mb-10 leading-relaxed">
            PropManage SaaS is the all-in-one platform that landlords, property managers, and real estate
            agencies use to automate rent collection, streamline maintenance, manage leases, and grow
            their portfolios—all from one powerful dashboard. Built for Kenya. Designed for Africa.
          </p>
          <div className="flex flex-col sm:flex-row gap-4 justify-center">
            <Link
              href="/demo"
              className="bg-white text-blue-700 font-bold px-8 py-4 rounded-xl hover:bg-blue-50 transition-colors text-lg shadow-lg"
            >
              Request Free Demo
            </Link>
            <Link
              href="/pricing"
              className="border-2 border-white text-white font-bold px-8 py-4 rounded-xl hover:bg-white/10 transition-colors text-lg"
            >
              View Pricing
            </Link>
          </div>
          <p className="mt-6 text-blue-200 text-sm">
            No credit card required · 30-day free trial · Setup in under 10 minutes
          </p>
        </div>
      </section>

      {/* Stats */}
      <section className="bg-white border-b border-gray-100 py-14 px-4">
        <div className="max-w-7xl mx-auto">
          <div className="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
            {stats.map((s) => (
              <div key={s.label}>
                <p className="text-3xl font-extrabold text-blue-600">{s.value}</p>
                <p className="text-sm text-gray-500 mt-1">{s.label}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Why PropManage */}
      <section className="py-20 px-4 bg-gray-50">
        <div className="max-w-7xl mx-auto">
          <div className="text-center mb-14">
            <h2 className="text-3xl sm:text-4xl font-extrabold text-gray-900 mb-4">
              Why Property Professionals Choose PropManage
            </h2>
            <p className="text-lg text-gray-600 max-w-2xl mx-auto">
              Running a property portfolio in Kenya and across Africa comes with unique challenges—fragmented
              payment methods, informal lease agreements, and slow maintenance workflows. PropManage was built
              from the ground up to solve these exact problems.
            </p>
          </div>
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            {features.map((f) => (
              <div key={f.title} className="bg-white rounded-2xl p-7 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                <div className="text-4xl mb-4">{f.icon}</div>
                <h3 className="text-lg font-bold text-gray-900 mb-2">{f.title}</h3>
                <p className="text-gray-600 text-sm leading-relaxed">{f.description}</p>
              </div>
            ))}
          </div>
          <div className="text-center mt-10">
            <Link href="/features" className="text-blue-600 font-semibold hover:underline">
              Explore all features →
            </Link>
          </div>
        </div>
      </section>

      {/* How It Works */}
      <section className="py-20 px-4 bg-white">
        <div className="max-w-7xl mx-auto">
          <div className="text-center mb-14">
            <h2 className="text-3xl sm:text-4xl font-extrabold text-gray-900 mb-4">
              How PropManage Works
            </h2>
            <p className="text-lg text-gray-600 max-w-2xl mx-auto">
              Getting started with PropManage takes less than ten minutes. Our onboarding wizard guides you
              through every step so you can start collecting rent and managing tenants immediately.
            </p>
          </div>
          <div className="grid grid-cols-1 md:grid-cols-4 gap-8">
            {[
              { step: "01", title: "Create Your Account", desc: "Sign up with your email and create your organisation profile. Add your company logo, contact details, and preferred currency." },
              { step: "02", title: "Add Your Properties", desc: "Import or manually add your properties and units. Specify rental amounts, amenities, and lease terms for each unit." },
              { step: "03", title: "Invite Tenants", desc: "Send digital invitations to your tenants. They activate their accounts, review lease agreements, and gain access to the tenant portal." },
              { step: "04", title: "Automate & Relax", desc: "Sit back while PropManage automates rent reminders, collects payments via M-Pesa or bank transfer, and handles maintenance workflows." },
            ].map((item) => (
              <div key={item.step} className="text-center">
                <div className="w-12 h-12 rounded-full bg-blue-600 text-white flex items-center justify-center text-lg font-bold mx-auto mb-4">
                  {item.step}
                </div>
                <h3 className="font-bold text-gray-900 mb-2">{item.title}</h3>
                <p className="text-gray-600 text-sm leading-relaxed">{item.desc}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Use Cases Overview */}
      <section className="py-20 px-4 bg-blue-50">
        <div className="max-w-7xl mx-auto">
          <div className="text-center mb-12">
            <h2 className="text-3xl sm:text-4xl font-extrabold text-gray-900 mb-4">
              Built for Every Type of Property Professional
            </h2>
            <p className="text-lg text-gray-600 max-w-2xl mx-auto">
              Whether you manage one bedsitter or a thousand commercial units, PropManage scales to fit your needs.
            </p>
          </div>
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            {[
              { title: "Individual Landlords", desc: "Manage your own properties without hiring a full-time agent. Perfect for landlords with 1–20 units.", icon: "🏠" },
              { title: "Property Agencies", desc: "Manage multiple landlord portfolios under one account. White-labeling available for agencies.", icon: "🏬" },
              { title: "Real Estate Developers", desc: "Track pre-sales, manage new tenants, and oversee facilities for newly completed developments.", icon: "🏗️" },
              { title: "Corporate Facilities Managers", desc: "Manage office parks, warehouses, and commercial premises with enterprise-grade controls.", icon: "🏢" },
            ].map((uc) => (
              <div key={uc.title} className="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <div className="text-3xl mb-3">{uc.icon}</div>
                <h3 className="font-bold text-gray-900 mb-2">{uc.title}</h3>
                <p className="text-sm text-gray-600">{uc.desc}</p>
              </div>
            ))}
          </div>
          <div className="text-center mt-8">
            <Link href="/use-cases" className="text-blue-600 font-semibold hover:underline">
              See detailed use cases →
            </Link>
          </div>
        </div>
      </section>

      {/* Testimonials Snippet */}
      <section className="py-20 px-4 bg-white">
        <div className="max-w-7xl mx-auto">
          <div className="text-center mb-12">
            <h2 className="text-3xl sm:text-4xl font-extrabold text-gray-900 mb-4">
              Trusted by Property Professionals Across Kenya
            </h2>
            <p className="text-lg text-gray-600">
              Join thousands of landlords and agents who have transformed their property management.
            </p>
          </div>
          <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
            {testimonials.map((t) => (
              <div key={t.name} className="bg-gray-50 rounded-2xl p-7 border border-gray-100">
                <p className="text-gray-700 italic mb-6 leading-relaxed">"{t.quote}"</p>
                <div className="flex items-center gap-3">
                  <div className="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-sm">
                    {t.avatar}
                  </div>
                  <div>
                    <p className="font-semibold text-gray-900 text-sm">{t.name}</p>
                    <p className="text-xs text-gray-500">{t.role}</p>
                  </div>
                </div>
              </div>
            ))}
          </div>
          <div className="text-center mt-8">
            <Link href="/testimonials" className="text-blue-600 font-semibold hover:underline">
              Read more success stories →
            </Link>
          </div>
        </div>
      </section>

      {/* Pricing Teaser */}
      <section className="py-20 px-4 bg-gray-50">
        <div className="max-w-4xl mx-auto text-center">
          <h2 className="text-3xl sm:text-4xl font-extrabold text-gray-900 mb-4">
            Simple, Transparent Pricing
          </h2>
          <p className="text-lg text-gray-600 mb-10">
            No hidden fees, no per-transaction charges. Choose the plan that matches your portfolio size.
            Upgrade or downgrade any time.
          </p>
          <div className="grid grid-cols-1 sm:grid-cols-3 gap-6">
            {[
              { name: "Starter", price: "KES 2,499", period: "/month", units: "Up to 10 units", cta: "Start Free Trial" },
              { name: "Growth", price: "KES 7,999", period: "/month", units: "Up to 50 units", cta: "Start Free Trial", highlight: true },
              { name: "Enterprise", price: "Custom", period: "", units: "Unlimited units", cta: "Contact Sales" },
            ].map((plan) => (
              <div
                key={plan.name}
                className={`rounded-2xl p-7 border ${
                  plan.highlight
                    ? "bg-blue-600 border-blue-600 text-white shadow-xl"
                    : "bg-white border-gray-200 text-gray-900"
                }`}
              >
                <p className={`text-xs font-bold uppercase tracking-wider mb-2 ${plan.highlight ? "text-blue-200" : "text-gray-500"}`}>
                  {plan.name}
                </p>
                <p className={`text-3xl font-extrabold ${plan.highlight ? "text-white" : "text-gray-900"}`}>
                  {plan.price}
                  <span className={`text-sm font-normal ${plan.highlight ? "text-blue-200" : "text-gray-500"}`}>
                    {plan.period}
                  </span>
                </p>
                <p className={`text-sm mt-2 mb-6 ${plan.highlight ? "text-blue-100" : "text-gray-500"}`}>
                  {plan.units}
                </p>
                <Link
                  href="/pricing"
                  className={`block text-center font-semibold py-2.5 rounded-lg transition-colors ${
                    plan.highlight
                      ? "bg-white text-blue-700 hover:bg-blue-50"
                      : "border border-blue-600 text-blue-600 hover:bg-blue-50"
                  }`}
                >
                  {plan.cta}
                </Link>
              </div>
            ))}
          </div>
          <p className="mt-6 text-sm text-gray-500">
            All plans include a 30-day free trial. No credit card required.
          </p>
        </div>
      </section>

      {/* Security Assurance */}
      <section className="py-16 px-4 bg-white border-y border-gray-100">
        <div className="max-w-7xl mx-auto">
          <div className="flex flex-wrap gap-8 justify-center items-center text-center">
            {[
              { icon: "🔒", label: "256-bit SSL Encryption" },
              { icon: "🛡️", label: "KDPA 2019 Compliant" },
              { icon: "☁️", label: "99.9% Uptime SLA" },
              { icon: "💾", label: "Daily Automated Backups" },
              { icon: "🔍", label: "Regular Security Audits" },
              { icon: "🏦", label: "PCI DSS Compliant Payments" },
            ].map((item) => (
              <div key={item.label} className="flex items-center gap-2 text-gray-700">
                <span className="text-xl">{item.icon}</span>
                <span className="text-sm font-medium">{item.label}</span>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Final CTA */}
      <section className="py-24 px-4 bg-gradient-to-r from-blue-700 to-indigo-700 text-white">
        <div className="max-w-4xl mx-auto text-center">
          <h2 className="text-3xl sm:text-4xl font-extrabold mb-6">
            Ready to Transform Your Property Management?
          </h2>
          <p className="text-xl text-blue-100 mb-10 leading-relaxed">
            Join over 3,500 property professionals who have already streamlined their portfolios with PropManage.
            Start your free 30-day trial today — no credit card, no commitment, no hidden fees.
          </p>
          <div className="flex flex-col sm:flex-row gap-4 justify-center">
            <Link
              href="/demo"
              className="bg-white text-blue-700 font-bold px-10 py-4 rounded-xl hover:bg-blue-50 transition-colors text-lg shadow-lg"
            >
              Get a Free Demo
            </Link>
            <Link
              href="/pricing"
              className="border-2 border-white text-white font-bold px-10 py-4 rounded-xl hover:bg-white/10 transition-colors text-lg"
            >
              See Pricing Plans
            </Link>
          </div>
          <p className="mt-6 text-blue-200 text-sm">
            Questions? Email us at{" "}
            <a href="mailto:hello@propmanage.co.ke" className="underline">
              hello@propmanage.co.ke
            </a>{" "}
            or{" "}
            <Link href="/contact" className="underline">
              visit our contact page
            </Link>
            .
          </p>
        </div>
      </section>
    </>
  );
}
