import type { Metadata } from "next";
import Link from "next/link";

export const metadata: Metadata = {
  title: "Features – PropManage SaaS",
  description:
    "Discover PropManage's comprehensive feature set: automated rent collection, lease management, maintenance tracking, financial reporting, and more.",
};

const featureGroups = [
  {
    category: "Property & Portfolio Management",
    items: [
      { title: "Unlimited Properties & Units", desc: "Organise your portfolio into properties, blocks, and individual units. Add custom tags, property types, and geographic zones for easy filtering." },
      { title: "Occupancy Tracking", desc: "Real-time occupancy dashboards showing vacant, occupied, and reserved units across all your properties at a glance." },
      { title: "Portfolio Analytics", desc: "Yield calculations, gross income tracking, expense breakdowns, and ROI reports for every property in your portfolio." },
      { title: "Multi-Currency Support", desc: "Accept and report in KES, USD, GBP, and other major currencies. Ideal for expat landlords and international investors." },
    ],
  },
  {
    category: "Rent Collection & Payments",
    items: [
      { title: "M-Pesa Integration", desc: "Native M-Pesa Paybill and Buy Goods integration. Tenants pay via Safaricom SIM card and payments are reconciled automatically." },
      { title: "Automated Reminders", desc: "Customisable SMS and email reminders sent automatically before and after rent due dates. Reduce late payments by up to 60%." },
      { title: "Digital Receipts", desc: "Branded PDF receipts generated automatically for every payment and delivered instantly to tenants via email and SMS." },
      { title: "Partial Payments & Arrears", desc: "Track partial payments, outstanding arrears, and overpayments. Generate arrears reports and escalation notices with one click." },
    ],
  },
  {
    category: "Lease & Tenancy Management",
    items: [
      { title: "Digital Lease Generation", desc: "Create legally-compliant lease agreements from templates. Customise clauses, escalation rates, and penalty terms." },
      { title: "E-Signatures", desc: "Tenants and landlords sign lease agreements electronically. Fully audited, legally binding under Kenyan law." },
      { title: "Automated Renewals", desc: "Set renewal reminders 60, 30, and 14 days before lease expiry. Send renewal offers with one click." },
      { title: "Tenant Screening", desc: "Integrated credit and background checks. Collect references and employment verification documents directly in the platform." },
    ],
  },
  {
    category: "Maintenance & Work Orders",
    items: [
      { title: "Tenant Maintenance Portal", desc: "Tenants submit maintenance requests via mobile app or web browser with photos and priority descriptions." },
      { title: "Work Order Assignment", desc: "Assign requests to internal staff or external contractors. Track progress, costs, and completion timelines." },
      { title: "Preventive Maintenance Schedules", desc: "Set recurring maintenance tasks (plumbing inspections, generator services, lift checks) with automatic scheduling." },
      { title: "Contractor Management", desc: "Maintain a database of approved contractors with ratings, certifications, and payment terms." },
    ],
  },
  {
    category: "Communication & Collaboration",
    items: [
      { title: "In-App Messaging", desc: "Secure messaging between landlords, property managers, and tenants. Full message history and audit trail." },
      { title: "Broadcast Announcements", desc: "Send community notices, water outage alerts, or policy updates to all tenants in a property with one message." },
      { title: "Notice Board", desc: "Digital notice boards for each property. Post announcements, meeting minutes, and community rules." },
      { title: "Automated SMS & Email", desc: "Trigger automated notifications for rent receipts, maintenance updates, lease renewals, and custom events." },
    ],
  },
  {
    category: "Finance & Reporting",
    items: [
      { title: "Income & Expense Tracking", desc: "Log all income and expenses against specific properties. Categorise by type for clean financial reporting." },
      { title: "KRA-Ready Reports", desc: "Generate income summaries formatted for Kenya Revenue Authority rental income declarations." },
      { title: "Cash Flow Forecasting", desc: "Project future income based on existing leases, upcoming vacancies, and expense schedules." },
      { title: "Export & Accounting Integration", desc: "Export to Excel, PDF, or integrate directly with QuickBooks and Xero accounting software." },
    ],
  },
];

export default function FeaturesPage() {
  return (
    <>
      <section className="bg-gradient-to-br from-blue-700 to-indigo-700 text-white py-20 px-4">
        <div className="max-w-4xl mx-auto text-center">
          <h1 className="text-4xl sm:text-5xl font-extrabold mb-6">
            Everything You Need to Manage Properties Professionally
          </h1>
          <p className="text-xl text-blue-100">
            PropManage brings together every tool a property professional needs into one seamless platform.
            No more juggling spreadsheets, WhatsApp groups, and paper receipts.
          </p>
        </div>
      </section>

      <section className="py-20 px-4 bg-white">
        <div className="max-w-7xl mx-auto space-y-16">
          {featureGroups.map((group) => (
            <div key={group.category}>
              <h2 className="text-2xl font-bold text-gray-900 mb-8 pb-3 border-b border-gray-200">
                {group.category}
              </h2>
              <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                {group.items.map((item) => (
                  <div key={item.title} className="flex gap-4">
                    <div className="mt-1 w-5 h-5 rounded-full bg-blue-100 flex-shrink-0 flex items-center justify-center">
                      <svg className="w-3 h-3 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fillRule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clipRule="evenodd" />
                      </svg>
                    </div>
                    <div>
                      <h3 className="font-semibold text-gray-900 mb-1">{item.title}</h3>
                      <p className="text-sm text-gray-600 leading-relaxed">{item.desc}</p>
                    </div>
                  </div>
                ))}
              </div>
            </div>
          ))}
        </div>
      </section>

      <section className="py-20 px-4 bg-blue-600 text-white text-center">
        <div className="max-w-3xl mx-auto">
          <h2 className="text-3xl font-extrabold mb-4">See PropManage in Action</h2>
          <p className="text-blue-100 mb-8 text-lg">
            Schedule a personalised demo and we'll walk you through every feature relevant to your portfolio.
          </p>
          <Link
            href="/demo"
            className="bg-white text-blue-700 font-bold px-8 py-4 rounded-xl hover:bg-blue-50 transition-colors inline-block"
          >
            Request a Demo
          </Link>
        </div>
      </section>
    </>
  );
}
