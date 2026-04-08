import type { Metadata } from "next";

export const metadata: Metadata = {
  title: "Privacy Policy – PropManage SaaS",
  description: "PropManage Privacy Policy. How we collect, use, and protect your personal data in compliance with the Kenya Data Protection Act 2019.",
};

export default function PrivacyPage() {
  return (
    <>
      <section className="bg-gradient-to-br from-blue-700 to-indigo-700 text-white py-16 px-4 text-center">
        <div className="max-w-3xl mx-auto">
          <h1 className="text-4xl font-extrabold mb-3">Privacy Policy</h1>
          <p className="text-blue-100">Last updated: January 2025 · Compliant with the Kenya Data Protection Act 2019</p>
        </div>
      </section>

      <section className="py-16 px-4 bg-white">
        <div className="max-w-4xl mx-auto space-y-8 text-sm text-gray-700 leading-relaxed">

          <div>
            <h2 className="text-xl font-bold text-gray-900 mb-3">1. Who We Are</h2>
            <p>PropManage SaaS Ltd ("PropManage", "we", "us", "our") is a company incorporated in Kenya and registered with the Office of the Data Protection Commissioner. We operate the PropManage property management platform. Our registered address is in Westlands, Nairobi, Kenya.</p>
            <p>Our Data Protection Officer can be contacted at: dpo@propmanage.co.ke</p>
          </div>

          <div>
            <h2 className="text-xl font-bold text-gray-900 mb-3">2. Data We Collect</h2>
            <p><strong>Account Information:</strong> Name, email address, phone number, company name, and billing information when you register.</p>
            <p><strong>Property & Tenant Data:</strong> Information about your properties, units, lease terms, rental amounts, and tenant details (name, contact, national ID, employment details) as entered by you into the platform.</p>
            <p><strong>Payment Data:</strong> Transaction records, M-Pesa confirmation numbers, and payment history. We do not store raw card numbers.</p>
            <p><strong>Usage Data:</strong> Log files, page views, feature usage, and interaction data used to improve the Service and provide support.</p>
            <p><strong>Communication Data:</strong> Messages sent through the platform's communication features and correspondence with our support team.</p>
          </div>

          <div>
            <h2 className="text-xl font-bold text-gray-900 mb-3">3. How We Use Your Data</h2>
            <p>We use your data to: (a) provide and operate the Service; (b) process payments; (c) send service-related notifications; (d) provide customer support; (e) improve the Service based on usage patterns; (f) comply with legal obligations; and (g) prevent fraud and ensure security.</p>
            <p>We will not sell your personal data to third parties. We do not use your data for advertising purposes on third-party platforms.</p>
          </div>

          <div>
            <h2 className="text-xl font-bold text-gray-900 mb-3">4. Lawful Basis for Processing</h2>
            <p>Under the Kenya Data Protection Act 2019, we process your data on the following bases: contract performance (to deliver the Service you have subscribed to); legal obligation (to comply with tax, financial, and data protection laws); legitimate interests (for security, fraud prevention, and service improvement); and consent (for optional communications such as newsletters, which you can withdraw at any time).</p>
          </div>

          <div>
            <h2 className="text-xl font-bold text-gray-900 mb-3">5. Your Data Rights</h2>
            <p>Under the Kenya Data Protection Act 2019, you have the right to: access your personal data; correct inaccurate data; request deletion of your data (subject to legal retention requirements); object to processing; request data portability; and withdraw consent at any time without affecting the lawfulness of prior processing.</p>
            <p>To exercise any of these rights, contact us at dpo@propmanage.co.ke. We will respond within 21 days as required by the KDPA 2019.</p>
          </div>

          <div>
            <h2 className="text-xl font-bold text-gray-900 mb-3">6. Data Retention</h2>
            <p>We retain account data for the duration of your subscription plus 7 years (to comply with Kenyan tax and company law requirements). Tenant personal data is retained for the duration of the tenancy plus 3 years. Usage and log data is retained for 12 months. You may request earlier deletion of personal data where no legal obligation to retain it exists.</p>
          </div>

          <div>
            <h2 className="text-xl font-bold text-gray-900 mb-3">7. Third-Party Sharing</h2>
            <p>We share data only with: (a) sub-processors necessary to provide the Service (cloud providers, payment processors, email services), all bound by Data Processing Agreements; (b) regulatory authorities as required by Kenyan law; and (c) professional advisers (lawyers, accountants) under confidentiality obligations.</p>
          </div>

          <div>
            <h2 className="text-xl font-bold text-gray-900 mb-3">8. Contact & Complaints</h2>
            <p>For privacy enquiries: dpo@propmanage.co.ke. If unsatisfied with our response, you have the right to lodge a complaint with the Office of the Data Protection Commissioner (ODPC) at www.odpc.go.ke.</p>
          </div>

        </div>
      </section>
    </>
  );
}
