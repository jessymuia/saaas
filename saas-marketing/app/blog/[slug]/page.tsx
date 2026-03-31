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
