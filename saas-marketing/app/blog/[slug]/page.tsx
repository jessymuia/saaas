import type { Metadata } from "next";
import Link from "next/link";

const posts: Record<string, { title: string; date: string; author: string; category: string; content: string }> = {
  "mpesa-rent-collection-guide": {
    title: "The Complete Guide to M-Pesa Rent Collection for Kenyan Landlords",
    date: "January 15, 2025",
    author: "Brian Omondi",
    category: "How-To Guide",
    content: `Collecting rent in Kenya has traditionally been a manual, time-consuming process. Tenants pay via M-Pesa and screenshot their confirmation messages. Landlords spend hours every month matching screenshots to units, chasing missing payments, and manually issuing receipts. PropManage's M-Pesa integration changes this entirely.

## Setting Up M-Pesa Integration

PropManage connects to Safaricom's Daraja API to provide real-time payment reconciliation. When you configure your M-Pesa settings, you have two options: use a dedicated Paybill number (recommended for agencies and larger landlords) or use PropManage's shared Paybill with unique account codes for each tenant.

The dedicated Paybill option gives your tenants a branded payment experience—they pay to "PropManage Rentals" or your agency name. The shared Paybill works immediately with no additional Safaricom setup required and is ideal for landlords who want to start quickly.

## How Automatic Reconciliation Works

Every time a tenant pays via M-Pesa, Safaricom's system sends an instant notification to PropManage's servers. PropManage matches the payment to the correct tenant and unit, marks the rent as paid (or partially paid, if the amount is less than the monthly rent), and sends an automatic receipt to the tenant via SMS and email.

The entire process takes under five seconds. You never need to check an M-Pesa statement or manually update a spreadsheet again.

## Handling Late and Partial Payments

PropManage's automated reminder system sends SMS and email reminders to tenants on configurable schedules—for example, 7 days before the due date, on the due date, and 3 days after. You can customise the reminder message tone and content.

When partial payments are received, PropManage records the payment and tracks the outstanding balance. Automated follow-up reminders for the remaining amount are sent according to your configured schedule.

## Generating Reports

At month-end, PropManage automatically generates an income summary showing all rent received, outstanding balances, and a bank reconciliation. For agencies managing multiple landlords, individual landlord disbursement reports are generated showing amounts collected, management fees deducted, and the net disbursement amount.`,
  },

  "kenya-data-protection-act-landlords": {
    title: "What the Kenya Data Protection Act 2019 Means for Landlords",
    date: "January 8, 2025",
    author: "Samuel Kipchoge",
    category: "Legal & Compliance",
    content: `The Kenya Data Protection Act 2019 (KDPA) came into force in November 2019 and is enforced by the Office of the Data Protection Commissioner (ODPC). If you collect, store, or process personal information about your tenants—their names, ID numbers, phone numbers, payment records, employment details—you are a data controller under Kenyan law and have binding legal obligations.

## Who Does the KDPA Apply To?

The KDPA applies to any individual or organisation that determines the purpose and means of processing personal data about Kenyan residents. As a landlord, when you collect a tenant's national ID number for a lease agreement, record their employer information for vetting, or store their M-Pesa transaction history, you are processing personal data.

The Act applies regardless of whether you manage one property or one hundred. There is no minimum threshold. Small landlords who store tenant details in a spreadsheet are technically data controllers under the same law as a large property management company.

## What the KDPA Requires

The Act establishes several core principles that data controllers must follow. Data must be collected for a specified, explicit, and legitimate purpose—and you cannot use it for other purposes without the tenant's consent. You must only collect the minimum personal data necessary for your stated purpose.

Personal data must be kept accurate and up to date. You must implement appropriate security measures to protect it. You cannot transfer personal data outside Kenya unless the recipient country offers equivalent protection, or the tenant has given explicit consent to the transfer.

## Tenant Rights Under the KDPA

Tenants have the right to know what personal data you hold about them, and to request a copy. They have the right to request correction of inaccurate data, and in some circumstances the right to request deletion of their data. If you are processing data based on consent, they have the right to withdraw that consent at any time.

Tenants can also object to processing their data for direct marketing purposes. If you send promotional SMS messages to tenants or former tenants without their explicit opt-in consent, you are likely in breach of the KDPA.

## Practical Steps for Landlords

Register with the ODPC if you process personal data on a significant scale. Publish a plain-language privacy notice that explains what data you collect, why you collect it, how long you keep it, and who you share it with. Include this notice in your lease agreement or display it prominently if you collect data digitally.

Use a secure platform like PropManage to store tenant data rather than unencrypted spreadsheets. Limit access to tenant data to only those staff members who genuinely need it. Have a clear process for responding to tenant data access requests within the 21-day statutory deadline.

## Consequences of Non-Compliance

The ODPC can issue enforcement notices requiring you to change your data processing practices. Fines for serious violations can reach KES 5,000,000 (five million Kenyan shillings) or three times the value of the data processed—whichever is higher. Individuals can also bring civil claims for damages caused by unlawful data processing.

In practice, most landlords are not going to be prosecuted for inadvertent non-compliance in the short term. However, the regulatory environment is tightening. The ODPC has been actively investigating complaints and has issued public guidance. Getting your data protection practices right now avoids costly remediation later.`,
  },

  "reducing-tenant-turnover-kenya": {
    title: "5 Proven Strategies to Reduce Tenant Turnover in Kenya",
    date: "December 20, 2024",
    author: "Winnie Njeri",
    category: "Property Management Tips",
    content: `Tenant turnover is one of the biggest hidden costs in property management. When a tenant moves out, the typical Nairobi landlord faces one to three months of vacancy, repainting and repairing the unit, advertising costs, agent fees, and the time cost of vetting new tenants. For a unit renting at KES 25,000 per month, a two-month vacancy plus KES 30,000 in refurbishment costs means a loss of KES 80,000—before accounting for the landlord's time.

## 1. Respond to Maintenance Requests Quickly

The number one reason tenants leave before their lease expires is unaddressed maintenance issues. A dripping tap, a broken window latch, or a faulty water heater that sits unrepaired for weeks signals to tenants that their comfort is not a priority. Research consistently shows that tenants who feel their maintenance requests are handled promptly and professionally are far more likely to renew.

Set a target response time for different maintenance categories. Urgent issues—no water, gas leak, security breach—should be responded to within four hours. Routine maintenance like appliance repairs should be addressed within 48 hours. Use PropManage's maintenance tracking module to log every request, assign it to the right contractor, and notify the tenant when it is resolved.

## 2. Communicate Rent Increases Early and Fairly

Nothing destroys tenant goodwill faster than a surprise rent increase, especially one that arrives with only one month's notice. Tenants who feel blindsided by a significant rent increase will begin looking for alternative accommodation immediately, even if they might have accepted the increase given more time to adjust.

Give tenants at least three months' notice of any rent increase. Explain the basis for the increase—whether it is aligned with inflation, comparable market rents in the neighbourhood, or improvements to the property. Where possible, offer a longer-term lease at a fixed rent in exchange for the tenant's commitment to stay. Many tenants will accept a modest rent increase if it comes with certainty and fair advance notice.

## 3. Build a Positive Relationship with Tenants

The landlord-tenant relationship in Kenya is often purely transactional. Landlords who make an effort to build a respectful, professional relationship with their tenants create loyalty that directly reduces turnover. This does not mean becoming friends—it means treating tenants as customers whose long-term business you value.

Remember tenants' names. Respond to messages promptly. If a good tenant is going through a temporary financial difficulty, consider offering a payment plan rather than immediately threatening legal action. When tenants renew their leases, acknowledge it—a simple message saying "Thank you for renewing, we value having you as a tenant" costs nothing and builds loyalty.

## 4. Keep the Property Well-Maintained and Modern

A unit that looks dated and tired will see higher turnover than one that is clean, well-maintained, and periodically upgraded. You do not need to renovate the entire unit every three years, but strategic investments in upgrades—new kitchen cabinets, modern bathroom fittings, reliable water heating, fast internet infrastructure—can dramatically extend the average tenancy length.

Conduct an annual property review. Walk through each unit and common area and make a list of improvements that would make the property more attractive. Prioritise repairs that affect tenant comfort and safety. Small investments—a fresh coat of paint, new curtain rods, a reliable intercom system—signal to tenants that the property is cared for.

## 5. Offer Flexible Lease Terms

Many tenants in Kenya are hesitant to commit to a two-year lease because of job mobility, family changes, or uncertainty about their long-term plans. By offering flexible lease options—annual leases with mutual renewal options, or six-month leases at a slight premium—you can attract tenants who would otherwise avoid properties with inflexible terms.

PropManage's lease management module makes it easy to manage multiple lease types across your portfolio. Set renewal reminders 90 days before each lease expires, and prompt your property manager to contact the tenant early to discuss renewal. A proactive renewal conversation, before the tenant has started looking elsewhere, is far more likely to result in a successful renewal than one initiated only weeks before the lease expires.`,
  },

  "digital-lease-agreements-kenya": {
    title: "Are Digital Lease Agreements Legally Valid in Kenya?",
    date: "December 10, 2024",
    author: "Samuel Kipchoge",
    category: "Legal & Compliance",
    content: `Electronic signatures and digital lease agreements have become standard practice in many parts of the world, but many Kenyan landlords and property managers remain uncertain about their legal validity. Can you sign a lease agreement digitally in Kenya? Is a PDF signed with a typed name legally binding? This article breaks down the relevant Kenyan law and explains what you need to know before moving your lease process online.

## The Legal Framework for Electronic Contracts in Kenya

Kenya's Information Communication Technology (ICT) Act and the Kenya Information and Communications Act provide the primary legal framework for electronic transactions. Under these laws, a contract cannot be denied legal validity solely because it was concluded electronically.

More specifically, the Electronic Transactions Act—which draws on principles established in the UNCITRAL Model Law on Electronic Commerce—establishes that an electronic signature satisfies any legal requirement for a signature, provided that it reliably identifies the signatory and indicates their intention to be bound by the document.

## What Counts as a Valid Electronic Signature?

Not all digital signatures offer the same level of legal assurance. There is a spectrum from simple to advanced to qualified electronic signatures. A typed name at the bottom of a PDF email attachment is the most basic form of electronic signature. While technically valid under Kenyan law if the parties intended to sign, it provides very little evidence of identity or intent in a dispute.

A more legally robust approach involves using a dedicated e-signature platform that records the signatory's IP address, the time and date of signing, a unique hash of the signed document, and in some cases identity verification. These platforms produce a detailed audit trail that would be admissible as evidence in Kenyan courts under the Evidence Act.

## Practical Considerations for Landlords

For residential leases, the key risk is not legal validity—it is enforceability in a dispute. If a tenant later denies having signed a lease, or claims they did not understand the terms, you need to be able to prove exactly what they signed and when. A digital lease signed on PropManage's platform creates a clear, time-stamped record that is far easier to rely on in a dispute than a paper lease that may have been lost or altered.

For commercial leases, the stakes are higher and legal advice is advisable before moving entirely to digital execution. Some financiers and banks still require wet-ink signatures on commercial property documents, though this is changing.

## The Landlord and Tenant Act Considerations

Kenya's Landlord and Tenant (Shops, Hotels and Catering Establishments) Act and the Rent Restriction Act apply to specific categories of tenancy. Neither Act specifically requires wet-ink signatures on lease agreements, but both require that certain terms be in writing. A digital lease agreement clearly satisfies the "in writing" requirement—the dispute around electronic leases in Kenya is about signature, not the written form requirement.

## Our Recommendation

Use a reputable e-signature platform for all lease agreements. Store signed copies in a secure cloud environment with a clear audit trail. Include a clause in your lease agreement explicitly acknowledging that the parties have agreed to execute the agreement electronically. This removes any ambiguity about consent to electronic execution.

PropManage's lease management module includes digital signature support and stores signed agreements with a full audit trail. Tenants receive an emailed copy of the signed agreement automatically, providing both parties with a reliable record.`,
  },

  "property-management-agency-scaling": {
    title: "How to Scale Your Property Management Agency from 50 to 500 Units",
    date: "November 28, 2024",
    author: "Kelvin Mwangi",
    category: "Agency Growth",
    content: `Running a property management agency with 50 units is a very different business from running one with 500. At 50 units, a skilled property manager can keep everything in their head—they know every landlord, every tenant, and the history of every unit. Systems are simple because the volume of information is manageable by one or two people.

At 500 units, this approach breaks down completely. Information overload, communication failures, missed maintenance issues, delayed rent disbursements, and staff burnout become constant problems. Agencies that fail to build the right systems before they scale typically plateau—or collapse—well before reaching 500 units.

## The Systems That Got You to 50 Won't Get You to 500

The most common scaling mistake made by Kenyan property management agencies is trying to manage 500 units the same way they managed 50—by adding more people rather than better systems. Hiring an extra property manager to handle 100 more units works for a while, but it creates a fragile, person-dependent operation that cannot function if key staff leave.

Sustainable scaling requires systematising every repeatable process: rent collection, payment reconciliation, maintenance request handling, lease renewals, landlord disbursements, tenant vetting. Each process needs to be documented, automated where possible, and auditable—meaning you can see at a glance whether it ran correctly, not just whether it ran.

## Stage 1: Automate Rent Collection and Reconciliation (50–150 Units)

At this stage, the biggest lever is eliminating manual M-Pesa reconciliation. Agencies handling this manually typically spend eight to fifteen hours per month per property manager just matching M-Pesa messages to tenant records. At 150 units across two managers, that is up to 30 staff-hours per month on a task that software can do in seconds.

Implement PropManage's M-Pesa integration and direct Paybill setup. When every tenant pays to a unique account reference—typically their unit code—reconciliation happens automatically. Your property managers are freed to focus on relationship management and maintenance coordination instead of spreadsheet work.

## Stage 2: Build a Maintenance Tracking System (150–300 Units)

Between 150 and 300 units, maintenance becomes the second major operational bottleneck. Without a formal system, maintenance requests come in via WhatsApp, phone calls, and verbal messages to caretakers. Requests get lost. Tenants chase up the same issue multiple times. Landlords receive inflated maintenance invoices with no job history to check against.

Implement a formal maintenance request system where every request is logged, assigned to a contractor, tracked to completion, and closed with a tenant sign-off. PropManage's maintenance module does this. The result is a complete maintenance history for every unit—essential for end-of-tenancy disputes and for managing contractor relationships based on performance data.

## Stage 3: Systematise Landlord Reporting and Disbursements (300–500 Units)

At scale, the quality of your landlord reporting becomes a major competitive differentiator. Landlords who receive a monthly statement showing every transaction for every unit—rent collected, maintenance costs, management fees, net disbursement—with the money in their account on a predictable date each month, have no reason to move their portfolio elsewhere.

Build a disbursement workflow that runs like clockwork: reconciliation on the first of the month, statements generated by the third, disbursements processed by the fifth. PropManage's financial reporting module generates individual landlord statements automatically. Combined with M-Pesa integration, the entire end-of-month process can be completed in a fraction of the time it takes manually.

## Hiring and Team Structure at Scale

At 500 units, a well-run agency typically has three property managers (each handling 150–180 units), one finance and reconciliation specialist, one maintenance coordinator, and one administrator. This is a significant reduction from the staffing levels required for manual operations at the same scale.

The key hiring principle: hire people who are good at managing relationships and solving problems—the parts of property management that genuinely require human judgment. Let software handle the data processing, reminders, reporting, and reconciliation.`,
  },

  "nairobi-rental-market-2025": {
    title: "Nairobi Rental Market Outlook 2025: Trends and Opportunities",
    date: "November 15, 2024",
    author: "Faith Wambua",
    category: "Market Insights",
    content: `The Nairobi residential rental market enters 2025 in a state of cautious recovery. After several years of oversupply in the mid-to-upper market segments and declining real rents in many neighbourhoods, there are early signs of stabilisation—and in some areas, genuine upward pressure on rents. Here is our analysis of the key trends shaping the market and where the opportunities lie for property investors in 2025.

## Supply and Demand Dynamics

Nairobi's residential rental market has been characterised by oversupply in the KES 30,000–80,000 per month segment since approximately 2019. A large number of developments completed in the 2016–2020 period came to market at the same time as economic pressures were reducing tenants' disposable income. The result was sustained vacancy rates in many apartment developments, particularly in Kilimani, Westlands, and parts of Kasarani.

This supply overhang is gradually being absorbed. New completions in the mid-market segment have slowed, partly due to financing challenges and rising construction costs. At the same time, Nairobi's population and the formal employment base continue to grow, creating steady demand for well-located rental accommodation. The equilibrium point—where supply and demand balance—is approaching faster in well-established neighbourhoods than in peripheral areas with weaker infrastructure.

## The Satellite Town Opportunity

The most significant structural shift in the Nairobi rental market over the past five years has been the growth of satellite towns as genuine rental markets in their own right. Kiambu, Thika, Rongai, Kitengela, and Athi River have all seen substantial increases in rental demand as infrastructure improvements—particularly road upgrades and the expansion of ride-hailing services—have reduced commute times to Nairobi's commercial centres.

Rents in these areas remain significantly lower than comparable properties in Nairobi proper, but yields are often higher. A two-bedroom apartment in Rongai that rents for KES 18,000 per month might cost KES 4.5 million to develop, representing a gross yield of approximately 4.8%. A comparable apartment in Kilimani might cost KES 9 million to develop and rent for KES 45,000—a yield of 6%, but with substantially higher development risk and vacancy exposure.

## The Affordable Housing Premium

Kenya's affordable housing programme, combined with the 1.5% housing levy on formal sector employees, has created a significant shift in tenant expectations. A growing cohort of lower-income formal sector workers who previously rented in informal settlements are now seeking formal rental accommodation with basic amenities—reliable water, security, proper sanitation—at price points below KES 15,000 per month.

Developers and landlords who can serve this market efficiently—whether through purpose-built affordable rental housing or through well-managed bedsitters and single rooms in established neighbourhoods—are seeing very low vacancy rates and strong demand. The challenge is managing large numbers of low-rent units profitably, which requires excellent systems for rent collection, maintenance, and tenant management.

## Technology as a Competitive Advantage

Across all market segments, property managers who are leveraging technology effectively are outperforming those who are not. The gap is particularly visible in rent collection efficiency, maintenance responsiveness, and reporting quality. Landlords in 2025 expect professional-grade monthly reports, reliable M-Pesa payment processing, and prompt responses to maintenance requests. Agencies that can consistently deliver these are winning portfolio mandates from landlords who are frustrated with less capable competitors.

PropManage is seeing the strongest growth among agencies in the 50–300 unit range—large enough to need proper systems, small enough that the decision-maker is typically the owner-operator who directly feels the pain of manual processes. In 2025, the technology adoption curve in Kenyan property management is still in its early stages. Agencies that invest in the right platforms now will have a significant operational advantage as the market becomes more competitive.`,
  },
};

export async function generateStaticParams() {
  return Object.keys(posts).map((slug) => ({ slug }));
}

export async function generateMetadata({ params }: { params: Promise<{ slug: string }> }): Promise<Metadata> {
  const { slug } = await params;
  const post = posts[slug];
  return {
    title: post ? `${post.title} – PropManage Blog` : "Blog Post – PropManage",
    description: post ? post.content.slice(0, 155) : "",
  };
}

export default async function BlogPostPage({ params }: { params: Promise<{ slug: string }> }) {
  const { slug } = await params;
  const post = posts[slug];

  if (!post) {
    return (
      <div className="py-20 text-center">
        <h1 className="text-2xl font-bold text-gray-900 mb-4">Post Not Found</h1>
        <Link href="/blog" className="text-blue-600 hover:underline">← Back to Blog</Link>
      </div>
    );
  }

  const paragraphs = post.content.split("\n\n");

  return (
    <article className="py-16 px-4">
      <div className="max-w-3xl mx-auto">
        <Link href="/blog" className="text-blue-600 text-sm hover:underline mb-8 block">← Back to Blog</Link>
        <span className="text-xs font-semibold text-blue-600 bg-blue-50 px-3 py-1 rounded-full">{post.category}</span>
        <h1 className="text-3xl sm:text-4xl font-extrabold text-gray-900 mt-4 mb-4 leading-tight">{post.title}</h1>
        <div className="flex items-center gap-3 text-sm text-gray-500 mb-10 pb-8 border-b border-gray-200">
          <span>By {post.author}</span>
          <span>·</span>
          <span>{post.date}</span>
        </div>
        <div className="prose prose-gray max-w-none space-y-5 text-gray-700 leading-relaxed">
          {paragraphs.map((p, i) => {
            if (p.startsWith("## ")) {
              return <h2 key={i} className="text-xl font-bold text-gray-900 mt-8 mb-3">{p.replace("## ", "")}</h2>;
            }
            return <p key={i}>{p}</p>;
          })}
        </div>
      </div>
    </article>
  );
}
