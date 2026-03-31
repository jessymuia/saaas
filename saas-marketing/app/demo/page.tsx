import type { Metadata } from "next";

export const metadata: Metadata = {
  title: "Request a Demo – PropManage SaaS",
  description: "See PropManage in action. Request a personalised demo and we'll walk you through every feature relevant to your portfolio.",
};

export default function DemoPage() {
  return (
    <>
      <section className="bg-gradient-to-br from-blue-700 to-indigo-700 text-white py-20 px-4 text-center">
        <div className="max-w-3xl mx-auto">
          <h1 className="text-4xl sm:text-5xl font-extrabold mb-4">Request a Free Demo</h1>
          <p className="text-xl text-blue-100">
            See exactly how PropManage can transform your property portfolio. A 30-minute personalised demo tailored to your use case.
          </p>
        </div>
      </section>

      <section className="py-20 px-4 bg-white">
        <div className="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-14">
          <div>
            <h2 className="text-2xl font-bold text-gray-900 mb-6">Book Your Demo</h2>
            <form className="space-y-5">
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">First Name *</label>
                  <input type="text" required className="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Last Name *</label>
                  <input type="text" required className="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Work Email *</label>
                <input type="email" required className="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Phone Number *</label>
                <input type="tel" required className="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="+254 7XX XXX XXX" />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Organisation / Company</label>
                <input type="text" className="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">I am a… *</label>
                <select required className="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                  <option value="">Select your role</option>
                  <option>Individual Landlord</option>
                  <option>Property Management Agency</option>
                  <option>Real Estate Developer</option>
                  <option>Corporate Facilities Manager</option>
                  <option>Estate Manager</option>
                  <option>Other</option>
                </select>
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Number of Units</label>
                <select className="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                  <option>1–10</option>
                  <option>11–50</option>
                  <option>51–200</option>
                  <option>200+</option>
                </select>
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">What would you most like to see in the demo?</label>
                <textarea rows={3} className="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g. M-Pesa rent collection, lease management, maintenance tracking..." />
              </div>
              <button type="submit" className="w-full bg-blue-600 text-white font-bold py-3 rounded-xl hover:bg-blue-700 transition-colors">
                Request My Demo
              </button>
              <p className="text-xs text-gray-400 text-center">
                We'll be in touch within 1 business day to schedule your demo at a time that suits you.
              </p>
            </form>
          </div>

          <div className="space-y-8">
            <div>
              <h2 className="text-2xl font-bold text-gray-900 mb-6">What to Expect</h2>
              <div className="space-y-5">
                {[
                  { step: "1", title: "Submit Your Request", desc: "Fill in the form and tell us a bit about your portfolio and what you'd like to see." },
                  { step: "2", title: "We Prepare", desc: "Our team reviews your request and tailors the demo to your specific use case and portfolio size." },
                  { step: "3", title: "30-Minute Live Demo", desc: "Join a video call with one of our product specialists for a personalised walkthrough of the platform." },
                  { step: "4", title: "Q&A & Next Steps", desc: "Ask any questions you have. We'll also explain trial options, onboarding, and pricing." },
                ].map((s) => (
                  <div key={s.step} className="flex gap-4">
                    <div className="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-sm flex-shrink-0">
                      {s.step}
                    </div>
                    <div>
                      <p className="font-semibold text-gray-900">{s.title}</p>
                      <p className="text-sm text-gray-600">{s.desc}</p>
                    </div>
                  </div>
                ))}
              </div>
            </div>

            <div className="bg-blue-50 rounded-2xl p-7">
              <h3 className="font-bold text-gray-900 mb-3">Demo Includes</h3>
              <ul className="space-y-2">
                {[
                  "Live walkthrough of the dashboard",
                  "M-Pesa rent collection demonstration",
                  "Tenant portal and maintenance requests",
                  "Lease generation and e-signatures",
                  "Financial reports and analytics",
                  "Your specific questions answered",
                ].map((item) => (
                  <li key={item} className="flex items-center gap-2 text-sm text-gray-700">
                    <svg className="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                      <path fillRule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clipRule="evenodd" />
                    </svg>
                    {item}
                  </li>
                ))}
              </ul>
            </div>
          </div>
        </div>
      </section>
    </>
  );
}
