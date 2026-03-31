import type { Metadata } from "next";

export const metadata: Metadata = {
  title: "Changelog – PropManage SaaS",
  description: "Stay up to date with the latest features, improvements, and bug fixes in PropManage.",
};

const releases = [
  {
    version: "v2.8.0",
    date: "January 2025",
    type: "major",
    title: "Enterprise Commercial Leasing Module",
    changes: [
      { type: "new", text: "Commercial lease management with stepped rents and break clauses" },
      { type: "new", text: "Service charge reconciliation with automated calculations" },
      { type: "new", text: "Dilapidation schedule tracking and deposit management" },
      { type: "new", text: "Multi-currency invoicing for international tenants" },
      { type: "improvement", text: "Improved financial report export with Excel formatting" },
      { type: "fix", text: "Fixed edge case in M-Pesa reconciliation for transactions over KES 150,000" },
    ],
  },
  {
    version: "v2.7.0",
    date: "November 2024",
    type: "major",
    title: "Tenant Screening Integration",
    changes: [
      { type: "new", text: "Integrated credit check and background verification for prospective tenants" },
      { type: "new", text: "Digital reference collection with automated employer verification" },
      { type: "new", text: "Applicant scoring dashboard for agencies managing multiple applications" },
      { type: "improvement", text: "Redesigned tenant onboarding flow—40% faster completion rate" },
      { type: "improvement", text: "Mobile app performance improvements on lower-end Android devices" },
    ],
  },
  {
    version: "v2.6.2",
    date: "October 2024",
    type: "patch",
    title: "Stability & Performance",
    changes: [
      { type: "fix", text: "Resolved issue with duplicate M-Pesa payment notifications" },
      { type: "fix", text: "Fixed lease PDF generation on Safari 17" },
      { type: "fix", text: "Corrected VAT calculation for commercial properties in financial reports" },
      { type: "improvement", text: "Dashboard load time reduced by 35%" },
    ],
  },
  {
    version: "v2.6.0",
    date: "September 2024",
    type: "major",
    title: "Preventive Maintenance Scheduling",
    changes: [
      { type: "new", text: "Create recurring maintenance schedules for generators, lifts, plumbing, and more" },
      { type: "new", text: "Automated work order creation from maintenance schedules" },
      { type: "new", text: "Contractor performance ratings and review system" },
      { type: "new", text: "Maintenance cost tracking and analytics per property" },
      { type: "improvement", text: "Improved contractor assignment interface with availability calendar" },
    ],
  },
  {
    version: "v2.5.0",
    date: "July 2024",
    type: "major",
    title: "Advanced Analytics Dashboard",
    changes: [
      { type: "new", text: "Portfolio-level performance dashboard with yield calculations" },
      { type: "new", text: "Cash flow forecasting based on existing leases and scheduled expenses" },
      { type: "new", text: "Occupancy trend charts with vacancy days tracking" },
      { type: "new", text: "Comparative property performance analysis" },
      { type: "improvement", text: "KRA-ready income summary reports with quarterly breakdowns" },
    ],
  },
  {
    version: "v2.4.0",
    date: "May 2024",
    type: "major",
    title: "E-Signatures for Lease Agreements",
    changes: [
      { type: "new", text: "Legally binding e-signatures for lease agreements under Kenyan law" },
      { type: "new", text: "Full audit trail with IP address, timestamp, and device information" },
      { type: "new", text: "Witness signature support for qualifying lease types" },
      { type: "new", text: "Bulk lease renewal with templated renewal offer letters" },
    ],
  },
];

const typeColors: Record<string, string> = {
  new: "bg-green-100 text-green-700",
  improvement: "bg-blue-100 text-blue-700",
  fix: "bg-orange-100 text-orange-700",
};

const releaseColors: Record<string, string> = {
  major: "bg-blue-600 text-white",
  minor: "bg-blue-100 text-blue-700",
  patch: "bg-gray-100 text-gray-600",
};

export default function ChangelogPage() {
  return (
    <>
      <section className="bg-gradient-to-br from-blue-700 to-indigo-700 text-white py-20 px-4 text-center">
        <div className="max-w-3xl mx-auto">
          <h1 className="text-4xl sm:text-5xl font-extrabold mb-4">Changelog</h1>
          <p className="text-xl text-blue-100">New features, improvements, and fixes—shipped regularly.</p>
        </div>
      </section>

      <section className="py-20 px-4 bg-white">
        <div className="max-w-3xl mx-auto space-y-10">
          {releases.map((release) => (
            <div key={release.version} className="border border-gray-200 rounded-2xl overflow-hidden">
              <div className="bg-gray-50 px-6 py-4 flex items-center justify-between border-b border-gray-200">
                <div className="flex items-center gap-3">
                  <span className={`px-2.5 py-1 rounded-full text-xs font-bold ${releaseColors[release.type]}`}>
                    {release.version}
                  </span>
                  <h2 className="font-bold text-gray-900">{release.title}</h2>
                </div>
                <span className="text-sm text-gray-500">{release.date}</span>
              </div>
              <div className="p-6 space-y-2">
                {release.changes.map((change, i) => (
                  <div key={i} className="flex items-start gap-3 text-sm">
                    <span className={`px-2 py-0.5 rounded text-xs font-semibold uppercase flex-shrink-0 ${typeColors[change.type]}`}>
                      {change.type}
                    </span>
                    <span className="text-gray-700">{change.text}</span>
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
