import type { Metadata } from "next";
import Link from "next/link";

export const metadata: Metadata = {
  title: "Use Cases – PropManage SaaS",
  description: "See how landlords, agencies, developers, and corporate managers use PropManage to run their property portfolios.",
};

const useCases = [
  {
    icon: "🏠",
    title: "Individual Landlords",
    subtitle: "1–20 Units",
    problem: "Managing rental properties alongside a full-time job is exhausting. Chasing rent payments via M-Pesa statement screenshots, handling maintenance calls at midnight, and keeping track of lease renewals manually leads to costly mistakes and missed income.",
    solution: "PropManage gives individual landlords a professional management system without needing a full-time agent. Automated rent collection via M-Pesa, digital lease storage, tenant self-service maintenance requests, and automatic renewal alerts mean landlords spend hours—not days—managing their properties each month.",
    outcomes: ["60% reduction in late rent payments", "Average 4 hours saved per month per property", "Zero missed lease renewals", "Tenant satisfaction scores improve by 40%"],
  },
  {
    icon: "🏬",
    title: "Property Management Agencies",
    subtitle: "Managing Multiple Landlord Portfolios",
    problem: "Agencies managing dozens of landlords and hundreds of units struggle with disorganised reporting, inconsistent processes, and slow landlord disbursements. Keeping every landlord happy while coordinating maintenance contractors becomes a full-time administrative burden.",
    solution: "PropManage's multi-landlord architecture lets agencies manage separate portfolios under one account. Generate branded reports for each landlord, process disbursements with one click, and maintain a single contractor database. Role-based access controls ensure the right staff see only the properties they manage.",
    outcomes: ["Handle 3x more landlords with the same team", "Automated landlord disbursement reports", "Branded client portals for each landlord", "99% on-time disbursement record"],
  },
  {
    icon: "🏗️",
    title: "Real Estate Developers",
    subtitle: "New Developments & Off-Plan Management",
    problem: "Developers launching new residential or commercial projects face the challenge of managing pre-sales, transitioning buyers to tenants or owners, and maintaining common facilities once occupation begins. Managing this through spreadsheets is error-prone and projects an unprofessional image to investors.",
    solution: "From the moment a project is listed, PropManage handles pre-sales interest tracking, buyer communication, handover documentation, and facility management post-occupation. Residents access the tenant portal for maintenance requests and community notices, while the developer's team manages everything centrally.",
    outcomes: ["Streamlined buyer-to-tenant onboarding", "Digital handover documentation", "Professional facilities management from day one", "Increased buyer confidence and referrals"],
  },
  {
    icon: "🏢",
    title: "Corporate Facilities Managers",
    subtitle: "Office Parks & Commercial Premises",
    problem: "Managing commercial tenants—especially in multi-tenant office parks—requires sophisticated service charge calculations, complex lease structures, and rapid response to maintenance issues that affect business operations. Standard residential tools fall short.",
    solution: "PropManage's enterprise tier supports complex commercial lease structures including stepped rents, service charge reconciliation, parking allocation, and multi-level approval workflows for maintenance and expenditure. Integration with accounting systems ensures financial compliance.",
    outcomes: ["Automated service charge reconciliation", "SLA-tracked maintenance for commercial tenants", "Expenditure approval workflows", "Full accounting software integration"],
  },
  {
    icon: "🏘️",
    title: "Residential Estate Managers",
    subtitle: "Gated Communities & Housing Estates",
    problem: "Running a gated community involves managing residents, service charges, visitor management, communal facility bookings, and a complex web of estate rules. Keeping hundreds of homeowners informed and resolving disputes quickly is a constant challenge.",
    solution: "PropManage's estate management module provides digital notice boards, broadcast messaging to all residents, service charge management, visitor log tracking, and dispute resolution workflows. Residents access everything through a clean mobile-friendly portal.",
    outcomes: ["100% digital communication with residents", "Transparent service charge reporting", "Reduced estate management disputes", "Improved resident retention"],
  },
  {
    icon: "📦",
    title: "Warehousing & Industrial Properties",
    subtitle: "Logistics Hubs & Industrial Parks",
    problem: "Industrial property landlords deal with complex lease structures, significant maintenance requirements (dock doors, racking systems, power infrastructure), and tenants who demand rapid response to operational issues that could halt their business.",
    solution: "PropManage handles large-format industrial leases with break clauses, dilapidation schedules, and scheduled preventive maintenance. Maintenance escalation paths ensure operational issues are prioritised and tracked to resolution with full audit trails.",
    outcomes: ["Structured dilapidation management", "Preventive maintenance scheduling", "Rapid escalation for operational emergencies", "Full lease compliance tracking"],
  },
];

export default function UseCasesPage() {
  return (
    <>
      <section className="bg-gradient-to-br from-blue-700 to-indigo-700 text-white py-20 px-4 text-center">
        <div className="max-w-4xl mx-auto">
          <h1 className="text-4xl sm:text-5xl font-extrabold mb-6">
            PropManage Works for Every Property Professional
          </h1>
          <p className="text-xl text-blue-100">
            From individual landlords to large corporate facilities teams, PropManage adapts to your unique
            property management challenges.
          </p>
        </div>
      </section>

      <section className="py-20 px-4 bg-white">
        <div className="max-w-5xl mx-auto space-y-20">
          {useCases.map((uc, i) => (
            <div key={uc.title} className={`flex flex-col md:flex-row gap-10 ${i % 2 === 1 ? "md:flex-row-reverse" : ""}`}>
              <div className="md:w-1/3 flex-shrink-0">
                <div className="bg-blue-50 rounded-2xl p-8 text-center">
                  <div className="text-6xl mb-4">{uc.icon}</div>
                  <h2 className="text-xl font-bold text-gray-900">{uc.title}</h2>
                  <p className="text-sm text-blue-600 font-medium mt-1">{uc.subtitle}</p>
                </div>
              </div>
              <div className="md:w-2/3">
                <div className="mb-4">
                  <h3 className="text-sm font-bold text-red-500 uppercase tracking-wider mb-1">The Challenge</h3>
                  <p className="text-gray-600 leading-relaxed">{uc.problem}</p>
                </div>
                <div className="mb-4">
                  <h3 className="text-sm font-bold text-green-600 uppercase tracking-wider mb-1">The PropManage Solution</h3>
                  <p className="text-gray-600 leading-relaxed">{uc.solution}</p>
                </div>
                <div>
                  <h3 className="text-sm font-bold text-blue-600 uppercase tracking-wider mb-2">Key Outcomes</h3>
                  <ul className="space-y-1">
                    {uc.outcomes.map((o) => (
                      <li key={o} className="flex items-center gap-2 text-sm text-gray-700">
                        <svg className="w-4 h-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                          <path fillRule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clipRule="evenodd" />
                        </svg>
                        {o}
                      </li>
                    ))}
                  </ul>
                </div>
              </div>
            </div>
          ))}
        </div>
      </section>

      <section className="py-20 px-4 bg-blue-600 text-white text-center">
        <div className="max-w-3xl mx-auto">
          <h2 className="text-3xl font-extrabold mb-4">Find Out How PropManage Fits Your Portfolio</h2>
          <p className="text-blue-100 mb-8 text-lg">
            Book a personalised demo and our team will show you exactly how PropManage addresses your specific property management challenges.
          </p>
          <Link href="/demo" className="bg-white text-blue-700 font-bold px-8 py-4 rounded-xl hover:bg-blue-50 transition-colors inline-block">
            Request a Demo
          </Link>
        </div>
      </section>
    </>
  );
}
