import type { Metadata } from "next";
import Link from "next/link";

export const metadata: Metadata = {
  title: "About Us – PropManage SaaS",
  description: "Learn about PropManage's mission to modernise property management across Africa, our story, values, and the team behind the platform.",
};

const values = [
  { icon: "🎯", title: "Simplicity First", desc: "Property management is complex enough. We build every feature to be simple and intuitive, reducing the learning curve so professionals can focus on their portfolios, not on figuring out software." },
  { icon: "🤝", title: "Trust & Transparency", desc: "Landlords trust us with their most valuable assets. We maintain that trust through transparent pricing, honest communication, clear data handling practices, and a no-hidden-fees policy." },
  { icon: "🌍", title: "Built for Africa", desc: "Our platform is built with African real estate in mind—M-Pesa integration, Kenya data protection compliance, Swahili language support, and features tailored to local leasing practices." },
  { icon: "🔒", title: "Security by Default", desc: "We treat our customers' data with the same care they treat their properties. Security is not a feature we add later—it is built into every layer of our architecture from day one." },
  { icon: "📈", title: "Customer Success", desc: "We only succeed when our customers succeed. Our support team is available to help, our onboarding is thorough, and we measure our own performance by our customers' outcomes." },
  { icon: "⚡", title: "Continuous Improvement", desc: "Property management evolves, and so do we. We ship meaningful updates regularly, listen to customer feedback, and continuously invest in making PropManage the best platform on the market." },
];

const team = [
  { name: "Kelvin Mwangi", role: "CEO & Co-Founder", bio: "Former real estate developer who spent a decade manually managing properties before deciding to build the software he always wished existed. Kelvin leads PropManage's vision and strategy.", initials: "KM" },
  { name: "Eunice Akinyi", role: "CTO & Co-Founder", bio: "Software engineer with 12 years of experience building fintech and proptech solutions. Eunice architected PropManage's multi-tenant platform and oversees all technology decisions.", initials: "EA" },
  { name: "Brian Omondi", role: "Head of Product", bio: "Former property manager turned product manager. Brian spent five years in the field before joining PropManage to ensure every feature solves a real problem that property professionals face daily.", initials: "BO" },
  { name: "Winnie Njeri", role: "Head of Customer Success", bio: "Winnie leads our customer success team, ensuring every new customer gets up and running quickly and every existing customer continues to get maximum value from the platform.", initials: "WN" },
  { name: "Samuel Kipchoge", role: "Head of Finance & Legal", bio: "Chartered accountant and qualified advocate who ensures PropManage remains fully compliant with Kenyan regulations, including the Data Protection Act 2019 and real estate sector laws.", initials: "SK" },
  { name: "Faith Wambua", role: "Head of Marketing", bio: "With a background in real estate and B2B marketing, Faith communicates PropManage's value to property professionals across Kenya and the broader African market.", initials: "FW" },
];

const milestones = [
  { year: "2019", event: "PropManage is founded in Nairobi by Kelvin Mwangi and Eunice Akinyi after recognising the lack of professional property management tools tailored for African markets." },
  { year: "2020", event: "First version of the platform launches with core features: rent tracking, tenant communication, and basic lease management. First 50 customers onboard within three months." },
  { year: "2021", event: "M-Pesa integration goes live, transforming rent collection for Kenyan landlords. Customer base grows to 500 organisations. Seed funding round closed." },
  { year: "2022", event: "Multi-landlord agency portal launches, enabling property management companies to manage multiple portfolios under one account. Series A funding secured." },
  { year: "2023", event: "PropManage reaches 10,000 managed units milestone. Maintenance management module and contractor marketplace launch. Expansion into Uganda and Tanzania." },
  { year: "2024", event: "Platform serves over 12,000 properties and 47,000 tenants. Enterprise tier launches for commercial and industrial property managers. Continued East African expansion." },
];

export default function AboutPage() {
  return (
    <>
      {/* Hero */}
      <section className="bg-gradient-to-br from-blue-700 to-indigo-700 text-white py-20 px-4">
        <div className="max-w-4xl mx-auto text-center">
          <h1 className="text-4xl sm:text-5xl font-extrabold mb-6">
            Built by Property People,<br />for Property People
          </h1>
          <p className="text-xl text-blue-100 leading-relaxed">
            PropManage was born out of frustration. Our founders spent years managing properties with spreadsheets,
            WhatsApp groups, and paper receipts—and decided that African landlords deserved better. Today, we're
            the leading property management platform in East Africa.
          </p>
        </div>
      </section>

      {/* Our Story */}
      <section className="py-20 px-4 bg-white">
        <div className="max-w-4xl mx-auto">
          <h2 className="text-3xl font-extrabold text-gray-900 mb-6">Our Story</h2>
          <div className="prose prose-lg text-gray-600 space-y-5">
            <p>
              PropManage was founded in 2019 in Nairobi, Kenya, by Kelvin Mwangi and Eunice Akinyi. The idea
              came from Kelvin's experience managing a portfolio of fifteen residential units across three
              Nairobi neighbourhoods. Despite being a software engineer by training, he found himself drowning
              in WhatsApp messages from tenants, M-Pesa statement screenshots that didn't balance, paper lease
              agreements stored in a filing cabinet, and maintenance requests that fell through the cracks.
            </p>
            <p>
              "I looked at what was available in the market," Kelvin recalls, "and everything was either built
              for the US or UK market—where M-Pesa doesn't exist, where leasing practices are completely
              different—or it was a basic spreadsheet template dressed up as software. I decided to build what
              I actually needed."
            </p>
            <p>
              Kelvin partnered with Eunice, a seasoned fintech engineer who had built payment systems for
              some of Kenya's leading financial institutions. Together, they spent six months in 2019 talking
              to landlords, property agents, and tenants across Nairobi, Mombasa, and Kisumu to understand the
              true shape of the problem. The insights they gathered were clear: property management in Kenya
              was being held back not by lack of effort, but by the absence of tools designed for local realities.
            </p>
            <p>
              The first version of PropManage launched in early 2020, just weeks before the COVID-19 pandemic
              took hold. Despite the difficult timing, the pandemic actually accelerated adoption: landlords
              who had always relied on in-person rent collection suddenly needed digital tools urgently. Within
              three months, PropManage had fifty paying customers and a waiting list three times as long.
            </p>
            <p>
              The pivotal moment came in mid-2021 when PropManage launched native M-Pesa integration. For the
              first time, Kenyan landlords could see every M-Pesa payment automatically reconciled against the
              correct unit and tenant—eliminating hours of manual matching. The feature spread by word of mouth,
              and the company grew from 200 to over 800 customers within six months.
            </p>
            <p>
              By 2022, property management agencies had begun adopting PropManage to manage multiple landlord
              portfolios. The team built a dedicated multi-landlord architecture that allowed agencies to
              manage hundreds of landlords and thousands of units through a single login, generating branded
              monthly reports for each client. This feature transformed PropManage from a tool for individual
              landlords into a platform for the entire property management industry.
            </p>
            <p>
              Today, PropManage serves over 3,500 organisations—from individual landlords with one rental unit
              to large corporate real estate firms managing commercial office parks. The platform manages over
              12,000 properties and facilitates the collection of billions of Kenya Shillings in rent every
              year. The team has grown from two co-founders to over thirty full-time employees based primarily
              in Nairobi, with expansion teams in Kampala and Dar es Salaam.
            </p>
            <p>
              Our mission remains unchanged from day one: to give every property professional in Africa—
              regardless of the size of their portfolio—access to world-class property management tools that
              are built for their specific context, priced fairly, and supported exceptionally well.
            </p>
          </div>
        </div>
      </section>

      {/* Mission & Vision */}
      <section className="py-20 px-4 bg-gray-50">
        <div className="max-w-4xl mx-auto">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-10">
            <div className="bg-white rounded-2xl p-8 border border-gray-100 shadow-sm">
              <div className="text-4xl mb-4">🎯</div>
              <h2 className="text-2xl font-bold text-gray-900 mb-4">Our Mission</h2>
              <p className="text-gray-600 leading-relaxed">
                To empower every property professional in Africa with world-class management tools—built for
                local realities, priced fairly, and backed by exceptional support—so they can spend less time
                on administration and more time growing their portfolios and serving their tenants well.
              </p>
            </div>
            <div className="bg-white rounded-2xl p-8 border border-gray-100 shadow-sm">
              <div className="text-4xl mb-4">🌟</div>
              <h2 className="text-2xl font-bold text-gray-900 mb-4">Our Vision</h2>
              <p className="text-gray-600 leading-relaxed">
                A future where every rental relationship in Africa—between landlord and tenant, agent and
                landlord, developer and occupier—is managed transparently, efficiently, and professionally.
                Where no tenant loses their deposit unjustly, no landlord misses a renewal, and no maintenance
                request falls through the cracks.
              </p>
            </div>
          </div>
        </div>
      </section>

      {/* Values */}
      <section className="py-20 px-4 bg-white">
        <div className="max-w-7xl mx-auto">
          <div className="text-center mb-12">
            <h2 className="text-3xl font-extrabold text-gray-900 mb-4">Our Values</h2>
            <p className="text-lg text-gray-600 max-w-2xl mx-auto">
              These principles guide every product decision, every customer interaction, and every line of code we write.
            </p>
          </div>
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            {values.map((v) => (
              <div key={v.title} className="bg-gray-50 rounded-2xl p-7 border border-gray-100">
                <div className="text-4xl mb-4">{v.icon}</div>
                <h3 className="font-bold text-gray-900 mb-2">{v.title}</h3>
                <p className="text-sm text-gray-600 leading-relaxed">{v.desc}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Timeline */}
      <section className="py-20 px-4 bg-gray-50">
        <div className="max-w-3xl mx-auto">
          <h2 className="text-3xl font-extrabold text-gray-900 mb-12 text-center">Our Journey</h2>
          <div className="relative">
            <div className="absolute left-12 top-0 bottom-0 w-0.5 bg-blue-200" />
            <div className="space-y-10">
              {milestones.map((m) => (
                <div key={m.year} className="flex gap-6">
                  <div className="w-24 flex-shrink-0 text-right">
                    <span className="text-sm font-bold text-blue-600">{m.year}</span>
                  </div>
                  <div className="relative">
                    <div className="w-4 h-4 rounded-full bg-blue-600 border-2 border-white shadow absolute -left-8 top-0.5" />
                    <p className="text-gray-600 text-sm leading-relaxed">{m.event}</p>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </div>
      </section>

      {/* Team */}
      <section className="py-20 px-4 bg-white">
        <div className="max-w-7xl mx-auto">
          <div className="text-center mb-12">
            <h2 className="text-3xl font-extrabold text-gray-900 mb-4">Meet the Team</h2>
            <p className="text-lg text-gray-600 max-w-2xl mx-auto">
              A dedicated team of property professionals, engineers, and customer success experts committed
              to making property management better across Africa.
            </p>
          </div>
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            {team.map((member) => (
              <div key={member.name} className="bg-gray-50 rounded-2xl p-7 border border-gray-100 text-center">
                <div className="w-20 h-20 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-2xl mx-auto mb-4">
                  {member.initials}
                </div>
                <h3 className="font-bold text-gray-900">{member.name}</h3>
                <p className="text-blue-600 text-sm font-medium mb-3">{member.role}</p>
                <p className="text-xs text-gray-600 leading-relaxed">{member.bio}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Contact CTA */}
      <section className="py-20 px-4 bg-blue-600 text-white text-center">
        <div className="max-w-3xl mx-auto">
          <h2 className="text-3xl font-extrabold mb-4">Want to Work With Us?</h2>
          <p className="text-blue-100 mb-8 text-lg">
            We're always looking for talented people who are passionate about property technology and making a
            difference in the African real estate sector.
          </p>
          <div className="flex gap-4 justify-center flex-wrap">
            <Link href="/contact" className="bg-white text-blue-700 font-bold px-8 py-3 rounded-xl hover:bg-blue-50 transition-colors">
              Contact Us
            </Link>
            <Link href="/demo" className="border-2 border-white text-white font-bold px-8 py-3 rounded-xl hover:bg-white/10 transition-colors">
              Request a Demo
            </Link>
          </div>
        </div>
      </section>
    </>
  );
}
