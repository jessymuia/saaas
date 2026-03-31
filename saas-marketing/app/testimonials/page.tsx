import type { Metadata } from "next";

export const metadata: Metadata = {
  title: "Customer Testimonials – PropManage SaaS",
  description: "Hear from property managers, landlords, and agencies across Kenya who use PropManage to run their portfolios.",
};

const testimonials = [
  { name: "Amina Wanjiku", role: "Portfolio Landlord", location: "Nairobi", units: "18 units", avatar: "AW", rating: 5, quote: "PropManage cut my rent collection time from three weeks to three days. The M-Pesa integration alone paid for the entire year's subscription in the first month. I used to spend every first week of the month chasing tenants on WhatsApp. Now the system does it automatically." },
  { name: "David Otieno", role: "Property Agent", location: "Mombasa", units: "214 units across 12 landlords", avatar: "DO", rating: 5, quote: "Managing 14 different landlords and 200+ units used to require a team of five. With PropManage, my team of two handles everything seamlessly. The multi-landlord dashboard is a game-changer—I can see the health of every portfolio in one screen." },
  { name: "Grace Kamau", role: "Real Estate Developer", location: "Nakuru", units: "85 units", avatar: "GK", rating: 5, quote: "The lease management and automated renewal reminders have saved us from so many costly oversights. We used to miss renewals and lose good tenants. Now we get alerts 60 days out and can negotiate proactively. Our retention rate has improved dramatically." },
  { name: "James Mwangi", role: "Commercial Property Manager", location: "Westlands, Nairobi", units: "3 office blocks, 47 tenants", avatar: "JM", rating: 5, quote: "Service charge reconciliation used to take us two weeks every quarter. PropManage does it in minutes. Our tenants appreciate the transparent breakdown of charges and we've had almost no disputes since switching." },
  { name: "Fatuma Hassan", role: "Individual Landlord", location: "Kisumu", units: "6 units", avatar: "FH", rating: 5, quote: "I was skeptical about paying for software to manage only 6 units, but I calculated that PropManage has saved me at least KES 15,000 per month in lost rent from late payers and 10+ hours of administrative time. It's the best investment I've made in my properties." },
  { name: "Robert Kariuki", role: "Estate Manager", location: "Karen, Nairobi", units: "120 townhouses", avatar: "RK", rating: 5, quote: "Managing a gated community is about communication as much as finances. PropManage's broadcast messaging and notice board have transformed how we communicate with residents. Service charge collection is now 95%+ on time every month." },
  { name: "Priscilla Auma", role: "Property Management Firm CEO", location: "Nairobi CBD", units: "600+ units", avatar: "PA", rating: 5, quote: "We manage over 600 units for 45 different landlords. Before PropManage, our month-end reporting took an entire week and was still full of errors. Now we generate accurate disbursement reports in minutes. Our landlords trust us more, which means better retention for our business." },
  { name: "Ahmed Sharif", role: "Industrial Property Landlord", location: "Industrial Area, Nairobi", units: "8 warehouses", avatar: "AS", rating: 4, quote: "The maintenance tracking for our warehouse tenants is excellent. When a dock door breaks, tenants log it in PropManage and we have a contractor on-site within hours. Our tenants feel well-served and that's why they keep renewing." },
  { name: "Mary Njoroge", role: "Letting Agent", location: "Kiambu", units: "55 units", avatar: "MN", rating: 5, quote: "The tenant screening integration saved us from a nightmare tenant last year. Running background checks and verifying employment directly in the platform gives us the information we need to make good decisions. Highly recommend for any serious agent." },
];

export default function TestimonialsPage() {
  return (
    <>
      <section className="bg-gradient-to-br from-blue-700 to-indigo-700 text-white py-20 px-4 text-center">
        <div className="max-w-4xl mx-auto">
          <h1 className="text-4xl sm:text-5xl font-extrabold mb-4">What Our Customers Say</h1>
          <p className="text-xl text-blue-100">
            Real stories from real property professionals across Kenya. See how PropManage is transforming property management.
          </p>
        </div>
      </section>

      <section className="py-20 px-4 bg-white">
        <div className="max-w-7xl mx-auto">
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            {testimonials.map((t) => (
              <div key={t.name} className="bg-gray-50 rounded-2xl p-7 border border-gray-100 flex flex-col">
                <div className="flex gap-1 mb-4">
                  {Array.from({ length: 5 }).map((_, i) => (
                    <svg
                      key={i}
                      className={`w-4 h-4 ${i < t.rating ? "text-yellow-400" : "text-gray-300"}`}
                      fill="currentColor"
                      viewBox="0 0 20 20"
                    >
                      <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                  ))}
                </div>
                <p className="text-gray-700 italic mb-6 leading-relaxed flex-1">"{t.quote}"</p>
                <div className="flex items-center gap-3">
                  <div className="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-sm flex-shrink-0">
                    {t.avatar}
                  </div>
                  <div>
                    <p className="font-semibold text-gray-900 text-sm">{t.name}</p>
                    <p className="text-xs text-gray-500">{t.role} · {t.location}</p>
                    <p className="text-xs text-blue-600">{t.units}</p>
                  </div>
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>
    </>
  );
}
