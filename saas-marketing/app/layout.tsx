import type { Metadata } from "next";
import { Geist } from "next/font/google";
import "./globals.css";
import Navigation from "@/components/Navigation";
import Footer from "@/components/Footer";

const geistSans = Geist({
  variable: "--font-geist-sans",
  subsets: ["latin"],
});

export const metadata: Metadata = {
  title: {
    default: "PropManage SaaS – Modern Property Management Platform",
    template: "%s | PropManage SaaS",
  },
  description:
    "PropManage is the all-in-one multi-tenant property management platform for landlords, agents, and property managers across Africa. Streamline rent collection, maintenance, and tenant communication.",
  keywords: ["property management", "SaaS", "Kenya", "real estate", "landlord software", "rent collection"],
  openGraph: {
    type: "website",
    siteName: "PropManage SaaS",
  },
};

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html lang="en" className={`${geistSans.variable} h-full antialiased`}>
      <body className="min-h-full flex flex-col font-sans">
        <Navigation />
        <main className="flex-1">{children}</main>
        <Footer />
      </body>
    </html>
  );
}
