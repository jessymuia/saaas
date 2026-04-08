import type { Metadata } from "next";
import Link from "next/link";

export const metadata: Metadata = {
  title: "Blog – PropManage SaaS",
  description: "Property management tips, industry insights, platform updates, and guides for Kenyan landlords and property professionals.",
};

const posts = [
  {
    slug: "mpesa-rent-collection-guide",
    title: "The Complete Guide to M-Pesa Rent Collection for Kenyan Landlords",
    excerpt: "Learn how to set up automated M-Pesa rent collection, eliminate manual reconciliation, and reduce late payments using PropManage's integrated payment system.",
    date: "January 15, 2025",
    category: "How-To Guide",
    readTime: "8 min read",
    author: "Brian Omondi",
  },
  {
    slug: "kenya-data-protection-act-landlords",
    title: "What the Kenya Data Protection Act 2019 Means for Landlords",
    excerpt: "The KDPA 2019 creates obligations for anyone who collects and processes personal data—including landlords who hold tenant information. Here's what you need to know.",
    date: "January 8, 2025",
    category: "Legal & Compliance",
    readTime: "6 min read",
    author: "Samuel Kipchoge",
  },
  {
    slug: "reducing-tenant-turnover-kenya",
    title: "5 Proven Strategies to Reduce Tenant Turnover in Kenya",
    excerpt: "High tenant turnover is expensive—vacancy periods, re-letting fees, and unit refurbishment add up fast. Here's how top Kenyan landlords keep great tenants for longer.",
    date: "December 20, 2024",
    category: "Property Management Tips",
    readTime: "7 min read",
    author: "Winnie Njeri",
  },
  {
    slug: "digital-lease-agreements-kenya",
    title: "Are Digital Lease Agreements Legally Valid in Kenya?",
    excerpt: "Electronic signatures and digital lease agreements are becoming standard practice, but many Kenyan landlords are unsure about their legal standing. We break down the law.",
    date: "December 10, 2024",
    category: "Legal & Compliance",
    readTime: "5 min read",
    author: "Samuel Kipchoge",
  },
  {
    slug: "property-management-agency-scaling",
    title: "How to Scale Your Property Management Agency from 50 to 500 Units",
    excerpt: "The systems that got you to 50 units won't get you to 500. Here's how successful Kenyan property management agencies structure their operations to scale.",
    date: "November 28, 2024",
    category: "Agency Growth",
    readTime: "10 min read",
    author: "Kelvin Mwangi",
  },
  {
    slug: "nairobi-rental-market-2025",
    title: "Nairobi Rental Market Outlook 2025: Trends and Opportunities",
    excerpt: "Analysis of rental demand trends across Nairobi's key neighbourhoods, expected rental escalations, and where the growth opportunities lie for property investors.",
    date: "November 15, 2024",
    category: "Market Insights",
    readTime: "9 min read",
    author: "Faith Wambua",
  },
];

const categories = ["All", "How-To Guide", "Legal & Compliance", "Property Management Tips", "Agency Growth", "Market Insights"];

export default function BlogPage() {
  return (
    <>
      <section className="bg-gradient-to-br from-blue-700 to-indigo-700 text-white py-20 px-4 text-center">
        <div className="max-w-4xl mx-auto">
          <h1 className="text-4xl sm:text-5xl font-extrabold mb-4">Property Management Insights</h1>
          <p className="text-xl text-blue-100">
            Tips, guides, legal updates, and market insights for property professionals across Kenya.
          </p>
        </div>
      </section>

      <section className="py-8 px-4 bg-white border-b border-gray-100">
        <div className="max-w-7xl mx-auto">
          <div className="flex gap-3 flex-wrap">
            {categories.map((cat) => (
              <button
                key={cat}
                className={`px-4 py-1.5 rounded-full text-sm font-medium transition-colors ${
                  cat === "All"
                    ? "bg-blue-600 text-white"
                    : "bg-gray-100 text-gray-600 hover:bg-gray-200"
                }`}
              >
                {cat}
              </button>
            ))}
          </div>
        </div>
      </section>

      <section className="py-16 px-4 bg-gray-50">
        <div className="max-w-7xl mx-auto">
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            {posts.map((post) => (
              <Link
                key={post.slug}
                href={`/blog/${post.slug}`}
                className="bg-white rounded-2xl border border-gray-100 overflow-hidden hover:shadow-md transition-shadow group"
              >
                <div className="h-40 bg-gradient-to-br from-blue-100 to-indigo-100 flex items-center justify-center">
                  <span className="text-5xl">📝</span>
                </div>
                <div className="p-6">
                  <div className="flex items-center gap-2 mb-3">
                    <span className="text-xs font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">
                      {post.category}
                    </span>
                    <span className="text-xs text-gray-400">{post.readTime}</span>
                  </div>
                  <h2 className="font-bold text-gray-900 mb-2 group-hover:text-blue-600 transition-colors leading-snug">
                    {post.title}
                  </h2>
                  <p className="text-sm text-gray-600 mb-4 leading-relaxed line-clamp-3">{post.excerpt}</p>
                  <div className="flex items-center gap-2 text-xs text-gray-400">
                    <div className="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 font-bold text-xs">
                      {post.author.split(" ").map(n => n[0]).join("")}
                    </div>
                    <span>{post.author}</span>
                    <span>·</span>
                    <span>{post.date}</span>
                  </div>
                </div>
              </Link>
            ))}
          </div>
        </div>
      </section>
    </>
  );
}
