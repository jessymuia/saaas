import type { Metadata } from "next";

export const metadata: Metadata = {
  title: "Terms of Service – PropManage SaaS",
  description: "PropManage Terms of Service. Governed by Kenya law including the Contract Act, Consumer Protection Act, and relevant regulations.",
};

export default function TermsPage() {
  return (
    <>
      <section className="bg-gradient-to-br from-blue-700 to-indigo-700 text-white py-16 px-4 text-center">
        <div className="max-w-3xl mx-auto">
          <h1 className="text-4xl font-extrabold mb-3">Terms of Service</h1>
          <p className="text-blue-100">Last updated: January 2025 · Governed by the laws of Kenya</p>
        </div>
      </section>

      <section className="py-16 px-4 bg-white">
        <div className="max-w-4xl mx-auto prose prose-gray max-w-none text-sm text-gray-700 space-y-8 leading-relaxed">

          <div>
            <h2 className="text-xl font-bold text-gray-900 mb-3">1. Introduction and Acceptance</h2>
            <p>These Terms of Service ("Terms") constitute a legally binding agreement between you ("Customer", "you") and PropManage SaaS Ltd, a company incorporated in Kenya ("PropManage", "we", "us"), regarding your use of the PropManage property management platform and associated services ("Service").</p>
            <p>By registering for, accessing, or using the Service, you confirm that you have read, understood, and agree to be bound by these Terms, our Privacy Policy, and our Cookie Policy. These Terms are subject to and governed by the laws of Kenya, including the Law of Contract Act (Cap 23), the Consumer Protection Act 2012, the Kenya Information and Communications Act, the Data Protection Act 2019, and all other applicable Kenyan legislation.</p>
            <p>If you do not agree to these Terms, you must immediately cease using the Service. If you are accepting these Terms on behalf of a company or other legal entity, you represent that you have the authority to bind that entity to these Terms.</p>
          </div>

          <div>
            <h2 className="text-xl font-bold text-gray-900 mb-3">2. Service Description</h2>
            <p>PropManage provides a cloud-based, multi-tenant software-as-a-service (SaaS) platform designed for property management. The Service includes features for rent collection, lease management, maintenance tracking, tenant communication, financial reporting, and related functionality as described on our website and updated from time to time.</p>
            <p>PropManage reserves the right to modify, enhance, or discontinue any feature of the Service with reasonable notice to Customers. We will provide at least 30 days' advance notice of any material changes that adversely affect your use of the Service.</p>
          </div>

          <div>
            <h2 className="text-xl font-bold text-gray-900 mb-3">3. Account Registration and Security</h2>
            <p>To use the Service, you must register for an account by providing accurate, complete, and current information. You are responsible for maintaining the confidentiality of your login credentials and for all activities that occur under your account.</p>
            <p>You must notify PropManage immediately at security@propmanage.co.ke if you suspect unauthorised access to your account. PropManage will not be liable for any loss resulting from unauthorised use of your account where you have failed to safeguard your credentials.</p>
            <p>You must not share your account credentials with any person who is not an authorised user of your organisation. The use of automated tools or bots to access the Service is prohibited without prior written consent from PropManage.</p>
          </div>

          <div>
            <h2 className="text-xl font-bold text-gray-900 mb-3">4. Subscription Plans and Payment</h2>
            <p>The Service is offered on a subscription basis. Available plans, pricing, and included features are described on our Pricing page and are subject to change. We will provide at least 30 days' notice before increasing prices for existing subscribers.</p>
            <p>Subscription fees are billed in advance, on a monthly or annual basis as selected by you. All fees are exclusive of applicable taxes, including VAT as required by Kenya Revenue Authority regulations.</p>
            <p>Payment must be made by the due date to maintain uninterrupted access to the Service. If payment is not received within 14 days of the due date, PropManage reserves the right to suspend or restrict access to the Service. Your data will be retained for 90 days following suspension, after which it may be deleted.</p>
            <p>Refunds are available within 14 days of a new subscription commencing ("cooling-off period") in accordance with the Consumer Protection Act 2012. After this period, fees are non-refundable except in cases of material service failure attributable to PropManage.</p>
          </div>

          <div>
            <h2 className="text-xl font-bold text-gray-900 mb-3">5. Acceptable Use</h2>
            <p>You agree to use the Service only for lawful purposes and in compliance with all applicable Kenyan laws and regulations, including but not limited to the Landlord and Tenant (Shops, Hotels and Catering Establishments) Act, the Rent Restriction Act, the Land Act 2012, and the Land Registration Act 2012.</p>
            <p>You must not use the Service to: (a) process personal data without a lawful basis under the Data Protection Act 2019; (b) engage in fraudulent rent collection or misrepresentation to tenants; (c) discriminate against tenants on unlawful grounds; (d) infringe intellectual property rights; (e) attempt to gain unauthorised access to any system; or (f) transmit malware or harmful code.</p>
          </div>

          <div>
            <h2 className="text-xl font-bold text-gray-900 mb-3">6. Data Protection and Privacy</h2>
            <p>Both parties shall comply with their respective obligations under the Kenya Data Protection Act 2019 and any regulations made thereunder. PropManage acts as a Data Processor in relation to personal data processed on your behalf, and as a Data Controller for its own operational data.</p>
            <p>You, as the Customer, are the Data Controller for all personal data (tenant, contractor, and employee data) that you input into the Service. You warrant that you have a lawful basis under the KDPA 2019 for processing all personal data uploaded to the Service, and that you have provided appropriate privacy notices to data subjects.</p>
            <p>Data Processing Agreement terms are incorporated by reference from our Privacy Policy. PropManage's obligations as Data Processor include implementing appropriate technical and organisational security measures, not processing data for purposes other than those instructed by you, and assisting you in meeting your obligations to data subjects.</p>
          </div>

          <div>
            <h2 className="text-xl font-bold text-gray-900 mb-3">7. Intellectual Property</h2>
            <p>PropManage and its licensors retain all intellectual property rights in the Service, including software, databases, trademarks, and content. These Terms do not grant you any ownership interest in the Service.</p>
            <p>You retain ownership of all data, content, and information ("Customer Data") that you upload to the Service. You grant PropManage a limited, non-exclusive licence to process Customer Data solely for the purpose of providing the Service to you.</p>
          </div>

          <div>
            <h2 className="text-xl font-bold text-gray-900 mb-3">8. Service Availability and SLA</h2>
            <p>PropManage targets 99.9% monthly uptime for the core platform, excluding scheduled maintenance. Scheduled maintenance windows will be communicated at least 48 hours in advance. Unplanned outages will be communicated as quickly as practicable via our status page at status.propmanage.co.ke.</p>
            <p>In the event of service unavailability exceeding the SLA threshold, PropManage will provide service credits as described in the SLA documentation available on request.</p>
          </div>

          <div>
            <h2 className="text-xl font-bold text-gray-900 mb-3">9. Limitation of Liability</h2>
            <p>To the maximum extent permitted by Kenyan law, PropManage's total liability to you for any claim arising from these Terms or the Service shall not exceed the fees paid by you in the 12 months preceding the claim.</p>
            <p>PropManage shall not be liable for indirect, incidental, special, consequential, or punitive damages, including lost profits or revenue, arising from your use of the Service, even if PropManage has been advised of the possibility of such damages. Nothing in these Terms limits liability for death or personal injury caused by negligence, fraud, or any other matter that cannot be excluded by law in Kenya.</p>
          </div>

          <div>
            <h2 className="text-xl font-bold text-gray-900 mb-3">10. Termination</h2>
            <p>You may terminate your subscription at any time by providing 30 days' written notice to PropManage. PropManage may terminate or suspend your access immediately if you materially breach these Terms and fail to remedy the breach within 14 days of written notice.</p>
            <p>Upon termination, you may request a full data export within 30 days of the effective termination date. After that period, PropManage will delete your data in accordance with its data retention policy and the requirements of the Kenya Data Protection Act 2019.</p>
          </div>

          <div>
            <h2 className="text-xl font-bold text-gray-900 mb-3">11. Dispute Resolution</h2>
            <p>These Terms are governed by the laws of Kenya. Any dispute arising from these Terms shall first be submitted to good-faith negotiation between the parties. If unresolved after 30 days, disputes shall be referred to mediation under the Nairobi Centre for International Arbitration (NCIA) rules. If mediation fails, disputes shall be resolved by arbitration seated in Nairobi, conducted in English.</p>
            <p>Notwithstanding the above, either party may seek urgent interim relief from the Kenyan courts where necessary to protect their rights pending resolution of a dispute.</p>
          </div>

          <div>
            <h2 className="text-xl font-bold text-gray-900 mb-3">12. Contact</h2>
            <p>For any questions about these Terms, please contact:</p>
            <p>PropManage SaaS Ltd<br />Westlands, Nairobi, Kenya<br />legal@propmanage.co.ke<br />+254 700 123 456</p>
          </div>

        </div>
      </section>
    </>
  );
}
