import type { Metadata } from "next";

export const metadata: Metadata = {
  title: "Security & Compliance – PropManage SaaS",
  description: "PropManage security practices, data protection compliance including the Kenya Data Protection Act 2019, encryption standards, and infrastructure security.",
};

const sections = [
  {
    title: "Kenya Data Protection Act 2019 Compliance",
    content: `PropManage is fully compliant with the Kenya Data Protection Act 2019 (KDPA 2019), which was enacted to regulate the processing of personal data in Kenya. We have implemented comprehensive policies and technical measures to meet every requirement of the Act.

The KDPA 2019 applies to the processing of personal data of data subjects located in Kenya, and as a Kenyan company collecting and processing personal data of landlords, tenants, contractors, and other individuals, we take these obligations seriously.

Under the KDPA 2019, PropManage acts as both a Data Controller (when we collect and determine the purpose of data processing) and a Data Processor (when we process data on behalf of our landlord and agency customers). We have registered with the Office of the Data Protection Commissioner (ODPC) as required by law.

Our commitments under the KDPA 2019 include:
- Lawful basis for all data processing activities
- Data minimisation—we only collect what we need
- Purpose limitation—data is only used for stated purposes
- Storage limitation—data is not retained longer than necessary
- Data subject rights implementation (access, correction, deletion, portability)
- Data breach notification within 72 hours of discovery
- Data Protection Impact Assessments for high-risk processing activities
- Appointment of a dedicated Data Protection Officer (DPO)

For any data protection enquiries, please contact our Data Protection Officer at dpo@propmanage.co.ke.`,
  },
  {
    title: "Data Encryption",
    content: `All data transmitted between your browser, mobile app, and the PropManage platform is encrypted using Transport Layer Security (TLS) 1.2 or higher, with 256-bit AES encryption. We enforce HTTPS for all connections and use HTTP Strict Transport Security (HSTS) to prevent downgrade attacks.

Data stored in our database is encrypted at rest using AES-256 encryption. This means that even in the highly unlikely event that an unauthorised party gained physical access to our storage infrastructure, the data would be completely unreadable without the encryption keys.

Sensitive data—including financial information, national identification numbers, and contact details—is additionally encrypted at the field level before storage in our database. Encryption keys are managed through a dedicated key management service and are rotated regularly.

Our payment processing is handled by PCI DSS-compliant payment processors. PropManage itself never stores card numbers, CVV codes, or other sensitive payment credentials on our servers.`,
  },
  {
    title: "Infrastructure Security",
    content: `PropManage is hosted on enterprise-grade cloud infrastructure with multiple layers of security controls. Our infrastructure is housed in data centres located in Kenya and the European Union, selected for their physical security measures, compliance certifications, and network redundancy.

Our infrastructure security measures include:
- Virtual Private Cloud (VPC) with network segmentation
- Web Application Firewall (WAF) to filter malicious traffic
- Distributed Denial of Service (DDoS) protection
- Intrusion Detection and Prevention Systems (IDS/IPS)
- Regular vulnerability scans and penetration testing
- 24/7 infrastructure monitoring with automated alerting
- Database access controls with least-privilege principles
- Jump server architecture for administrative access

All infrastructure changes go through a change management process, and production environments are strictly separated from development and testing environments.`,
  },
  {
    title: "Access Controls & Authentication",
    content: `PropManage enforces strong access controls at every level of the platform. Every user account is protected by password policies requiring a minimum complexity, and we strongly encourage the use of Two-Factor Authentication (2FA) which is available on all plans.

Within organisations, PropManage's role-based access control (RBAC) system allows administrators to define exactly what each staff member can see and do. A maintenance coordinator can see maintenance requests without accessing financial data. A reporting-only user can generate reports without making changes to tenant records.

Internally, PropManage staff access to customer data is strictly controlled on a need-to-know basis. All internal access is logged, monitored, and subject to regular access reviews. No PropManage employee can access customer data without a legitimate business reason, and all such accesses are auditable.

Session management includes automatic logout after periods of inactivity, device tracking, and the ability for users to remotely terminate active sessions from any trusted device.`,
  },
  {
    title: "Data Backups & Business Continuity",
    content: `PropManage performs automated database backups every 6 hours, with full daily backups retained for 30 days and monthly backups retained for 12 months. Backups are encrypted, compressed, and stored in geographically separate locations from the primary database to ensure recoverability in the event of a regional incident.

Our Recovery Time Objective (RTO) is 4 hours for a full system restoration following a catastrophic failure, and our Recovery Point Objective (RPO) is 6 hours, meaning you would never lose more than 6 hours of data in even the most severe scenario.

We regularly test our backup restoration procedures to verify that backups are complete and recoverable. Test restoration reports are maintained and available to Enterprise customers on request.

Our platform is designed with high availability in mind. Critical components are deployed across multiple availability zones, and we use health checks with automatic failover to minimise downtime. Our contractual SLA guarantees 99.9% monthly uptime for all paid plans.`,
  },
  {
    title: "Security Audits & Vulnerability Management",
    content: `PropManage engages independent cybersecurity firms to conduct annual penetration tests of our platform and infrastructure. The findings of these tests are actioned systematically, with critical vulnerabilities patched within 24 hours and high-severity issues resolved within 7 days.

We run automated vulnerability scanning on our codebase as part of our continuous integration pipeline. This includes dependency scanning (to catch known vulnerabilities in third-party libraries), static application security testing (SAST), and dynamic application security testing (DAST).

PropManage operates a responsible disclosure programme. If you believe you have found a security vulnerability in our platform, please report it to security@propmanage.co.ke. We will acknowledge your report within 24 hours and keep you updated on our investigation and remediation timeline.

Our development team follows secure coding practices, with mandatory security training for all engineers. Code reviews include security considerations, and our development guidelines are aligned with the OWASP Top Ten web application security risks.`,
  },
  {
    title: "Data Residency & Third-Party Processors",
    content: `In compliance with the Kenya Data Protection Act 2019, PropManage ensures that personal data of Kenyan data subjects is processed in accordance with the Act's provisions on cross-border data transfers. Where data is transferred outside Kenya, we ensure adequate protections are in place through standard contractual clauses and processor agreements.

We maintain a comprehensive register of all third-party sub-processors who may process customer data on our behalf. These include cloud infrastructure providers, email delivery services, and payment processors. All sub-processors are vetted for their security practices and compliance posture, and are subject to Data Processing Agreements (DPAs) that bind them to appropriate data protection standards.

Our current list of key sub-processors, along with their purpose and data processing locations, is available to customers on request. We will notify customers of any significant changes to our sub-processor list with at least 30 days advance notice.`,
  },
];

export default function SecurityPage() {
  return (
    <>
      <section className="bg-gradient-to-br from-blue-700 to-indigo-700 text-white py-20 px-4">
        <div className="max-w-4xl mx-auto text-center">
          <h1 className="text-4xl sm:text-5xl font-extrabold mb-6">Security & Compliance</h1>
          <p className="text-xl text-blue-100 leading-relaxed">
            We protect your data and your tenants' data with enterprise-grade security. Full compliance with
            the Kenya Data Protection Act 2019 and international security standards.
          </p>
        </div>
      </section>

      <section className="py-16 px-4 bg-white border-b border-gray-100">
        <div className="max-w-7xl mx-auto">
          <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6 text-center">
            {[
              { icon: "🔒", label: "KDPA 2019 Compliant" },
              { icon: "🛡️", label: "256-bit Encryption" },
              { icon: "☁️", label: "99.9% Uptime SLA" },
              { icon: "💾", label: "6-hour Backups" },
              { icon: "🔍", label: "Annual Pen Tests" },
              { icon: "🏦", label: "PCI DSS Payments" },
            ].map((item) => (
              <div key={item.label} className="bg-gray-50 rounded-xl p-4">
                <div className="text-3xl mb-2">{item.icon}</div>
                <p className="text-xs font-semibold text-gray-700">{item.label}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      <section className="py-20 px-4 bg-white">
        <div className="max-w-4xl mx-auto space-y-14">
          {sections.map((s) => (
            <div key={s.title}>
              <h2 className="text-2xl font-bold text-gray-900 mb-5 pb-3 border-b border-gray-200">
                {s.title}
              </h2>
              <div className="text-gray-600 leading-relaxed whitespace-pre-line text-sm space-y-4">
                {s.content.split("\n\n").map((para, i) => (
                  <p key={i}>{para}</p>
                ))}
              </div>
            </div>
          ))}
        </div>
      </section>

      <section className="py-12 px-4 bg-gray-50 border-t border-gray-200">
        <div className="max-w-4xl mx-auto text-center">
          <p className="text-sm text-gray-600">
            For security reports: <a href="mailto:security@propmanage.co.ke" className="text-blue-600 underline">security@propmanage.co.ke</a>
            {" "}· Data Protection Officer: <a href="mailto:dpo@propmanage.co.ke" className="text-blue-600 underline">dpo@propmanage.co.ke</a>
          </p>
          <p className="text-xs text-gray-400 mt-2">Last updated: January 2025</p>
        </div>
      </section>
    </>
  );
}
