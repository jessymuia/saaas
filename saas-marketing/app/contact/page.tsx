import type { Metadata } from "next";

export const metadata: Metadata = {
  title: "Contact Us – PropManage SaaS",
  description: "Get in touch with the PropManage team for sales enquiries, support, or general questions.",
};

export default function ContactPage() {
  return (
    <>
      <section className="bg-gradient-to-br from-blue-700 to-indigo-700 text-white py-20 px-4 text-center">
        <div className="max-w-3xl mx-auto">
          <h1 className="text-4xl sm:text-5xl font-extrabold mb-4">Get in Touch</h1>
          <p className="text-xl text-blue-100">We'd love to hear from you. Reach out for sales, support, or general enquiries.</p>
        </div>
      </section>

      <section className="py-20 px-4 bg-white">
        <div className="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-14">
          <div>
            <h2 className="text-2xl font-bold text-gray-900 mb-6">Send Us a Message</h2>
            <form className="space-y-5">
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                  <input type="text" className="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Jane" />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                  <input type="text" className="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Doe" />
                </div>
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                <input type="email" className="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="jane@example.co.ke" />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                <input type="tel" className="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="+254 7XX XXX XXX" />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                <select className="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                  <option>Sales Enquiry</option>
                  <option>Technical Support</option>
                  <option>Billing Question</option>
                  <option>Partnership</option>
                  <option>Press / Media</option>
                  <option>Other</option>
                </select>
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Message</label>
                <textarea rows={5} className="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Tell us how we can help..."></textarea>
              </div>
              <button type="submit" className="w-full bg-blue-600 text-white font-bold py-3 rounded-xl hover:bg-blue-700 transition-colors">
                Send Message
              </button>
            </form>
          </div>

          <div className="space-y-8">
            <div>
              <h2 className="text-2xl font-bold text-gray-900 mb-6">Contact Information</h2>
              <div className="space-y-5">
                {[
                  { icon: "📧", label: "General Enquiries", value: "info@propmanage.co.ke" },
                  { icon: "💬", label: "Sales", value: "sales@propmanage.co.ke" },
                  { icon: "🛠️", label: "Support", value: "support@propmanage.co.ke" },
                  { icon: "📞", label: "Phone", value: "+254 700 123 456" },
                  { icon: "📍", label: "Address", value: "Westlands, Nairobi, Kenya" },
                ].map((c) => (
                  <div key={c.label} className="flex items-start gap-3">
                    <span className="text-2xl">{c.icon}</span>
                    <div>
                      <p className="text-xs font-semibold text-gray-500 uppercase">{c.label}</p>
                      <p className="text-gray-900 font-medium">{c.value}</p>
                    </div>
                  </div>
                ))}
              </div>
            </div>

            <div className="bg-blue-50 rounded-2xl p-7">
              <h3 className="font-bold text-gray-900 mb-2">Support Hours</h3>
              <div className="space-y-1 text-sm text-gray-600">
                <p>Monday – Friday: 8:00 AM – 6:00 PM EAT</p>
                <p>Saturday: 9:00 AM – 1:00 PM EAT</p>
                <p>Sunday & Public Holidays: Emergency support only</p>
              </div>
              <p className="mt-3 text-xs text-gray-500">Enterprise customers receive 24/7 priority support.</p>
            </div>
          </div>
        </div>
      </section>
    </>
  );
}
