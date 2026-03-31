import type { Metadata } from "next";

export const metadata: Metadata = {
  title: "Cookie Policy – PropManage SaaS",
  description: "PropManage Cookie Policy. How we use cookies and similar tracking technologies on our platform.",
};

export default function CookiesPage() {
  return (
    <>
      <section className="bg-gradient-to-br from-blue-700 to-indigo-700 text-white py-16 px-4 text-center">
        <div className="max-w-3xl mx-auto">
          <h1 className="text-4xl font-extrabold mb-3">Cookie Policy</h1>
          <p className="text-blue-100">Last updated: January 2025</p>
        </div>
      </section>

      <section className="py-16 px-4 bg-white">
        <div className="max-w-4xl mx-auto space-y-8 text-sm text-gray-700 leading-relaxed">
          <div>
            <h2 className="text-xl font-bold text-gray-900 mb-3">1. What Are Cookies?</h2>
            <p>Cookies are small text files placed on your device when you visit a website. They enable the website to remember your actions and preferences over a period of time, so you don't have to re-enter your preferences every time you visit or navigate between pages.</p>
          </div>

          <div>
            <h2 className="text-xl font-bold text-gray-900 mb-3">2. Cookies We Use</h2>
            <div className="overflow-x-auto">
              <table className="w-full border-collapse text-xs">
                <thead>
                  <tr className="bg-gray-50">
                    <th className="border border-gray-200 p-3 text-left font-semibold">Category</th>
                    <th className="border border-gray-200 p-3 text-left font-semibold">Purpose</th>
                    <th className="border border-gray-200 p-3 text-left font-semibold">Duration</th>
                    <th className="border border-gray-200 p-3 text-left font-semibold">Required?</th>
                  </tr>
                </thead>
                <tbody>
                  {[
                    { category: "Strictly Necessary", purpose: "Authentication, session management, CSRF protection, and security features required to operate the Service.", duration: "Session / 30 days", required: "Yes" },
                    { category: "Functional", purpose: "Remember your language preferences, dashboard layout settings, and recent navigation.", duration: "1 year", required: "No" },
                    { category: "Analytics", purpose: "Understand how users navigate the platform to improve usability (using privacy-first analytics with no cross-site tracking).", duration: "1 year", required: "No" },
                    { category: "Performance", purpose: "Monitor platform performance, error rates, and response times to maintain service quality.", duration: "Session", required: "No" },
                  ].map((row) => (
                    <tr key={row.category}>
                      <td className="border border-gray-200 p-3 font-medium">{row.category}</td>
                      <td className="border border-gray-200 p-3">{row.purpose}</td>
                      <td className="border border-gray-200 p-3">{row.duration}</td>
                      <td className="border border-gray-200 p-3">{row.required}</td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </div>

          <div>
            <h2 className="text-xl font-bold text-gray-900 mb-3">3. Managing Cookies</h2>
            <p>You can control non-essential cookies through the Cookie Preferences panel accessible via the "Cookie Settings" link in our website footer. Strictly necessary cookies cannot be disabled as they are required for the Service to function.</p>
            <p>You can also manage cookies through your browser settings. Note that disabling cookies may affect the functionality of the Service. For instructions on managing cookies in your browser, visit your browser's help documentation.</p>
          </div>

          <div>
            <h2 className="text-xl font-bold text-gray-900 mb-3">4. Third-Party Cookies</h2>
            <p>PropManage does not permit third-party advertising or social media tracking cookies on our platform. Any third-party services we integrate (such as payment processors) may set their own cookies, governed by their respective cookie policies.</p>
          </div>

          <div>
            <h2 className="text-xl font-bold text-gray-900 mb-3">5. Contact</h2>
            <p>For questions about our cookie practices, contact us at privacy@propmanage.co.ke.</p>
          </div>
        </div>
      </section>
    </>
  );
}
