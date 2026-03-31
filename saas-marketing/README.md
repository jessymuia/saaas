# PropManage SaaS — Marketing Site

Public-facing marketing site for [PropManage SaaS](https://propertysasa.com) — Kenya's leading multi-tenant property management platform. Built with **Next.js 14**, **Tailwind CSS 4**, and **TypeScript**.

## Pages

| Route | Description |
|-------|-------------|
| `/` | Landing home page (1,500+ words) |
| `/features` | Full feature breakdown |
| `/pricing` | Subscription plans (KES pricing) |
| `/use-cases` | Use cases by landlord type |
| `/about` | Company story, values, team, milestones (1,500+ words) |
| `/testimonials` | Customer stories & ratings |
| `/security` | Security & KDPA 2019 compliance |
| `/blog` | Blog listing with categories |
| `/blog/[slug]` | Individual blog post |
| `/changelog` | Product release history |
| `/contact` | Contact form |
| `/demo` | Request a demo form |
| `/terms` | Terms of Service (Kenya law) |
| `/privacy` | Privacy Policy (KDPA 2019) |
| `/cookies` | Cookie Policy |
| `/faq` | Frequently Asked Questions |

## Local Development

### Prerequisites

- Node.js 18+  
- npm 9+ (or yarn/pnpm/bun)

### Installation & Running

```bash
# From the repo root
cd saas-marketing

# Install dependencies
npm install

# Start the dev server on http://localhost:3000
npm run dev
```

The marketing site runs on **port 3000** (separate from the Laravel backend on port 8000).

### Run Alongside the Backend

You can run the backend and the marketing site simultaneously:

```bash
# Terminal 1 — Laravel backend (Docker)
docker compose up -d

# Terminal 2 — Next.js marketing site
cd saas-marketing && npm run dev
```

| Service | URL |
|---------|-----|
| Laravel backend | http://localhost:8000 |
| Marketing site | http://localhost:3000 |
| Central admin (Filament) | http://localhost:8000/sysadmin |
| Tenant app | http://{slug}.localhost:8000/app |
| Horizon dashboard | http://localhost:8000/horizon |

### Build for Production

```bash
npm run build
npm start
```

### Lint & Type-Check

```bash
npm run lint
npx tsc --noEmit
```

## Adding Blog Posts

Blog posts are defined in two places:

1. **Listing** — `app/blog/page.tsx`: Add your post metadata to the `posts` array.
2. **Content** — `app/blog/[slug]/page.tsx`: Add the full post content to the `posts` record, keyed by slug.

Both must be updated for a new post to render correctly.

Optionally, save an accompanying Markdown file to `content/blog/<slug>.md` for source control and review purposes.

## Deployment

The marketing site can be deployed independently of the Laravel backend:

- **Vercel** (recommended): Connect the repo and set the root directory to `saas-marketing/`.
- **Static export**: Run `npm run build` then serve the `out/` directory from any CDN.
- **Docker**: A minimal Dockerfile can be added to this directory for container-based deployment.

The site does not call any backend APIs at build time, so no environment variables are required for a basic deployment.

## Tech Stack

- [Next.js 14](https://nextjs.org/) — App Router, SSG
- [Tailwind CSS 4](https://tailwindcss.com/) — Utility-first CSS
- [TypeScript](https://www.typescriptlang.org/) — Type safety
- [React 19](https://react.dev/) — UI framework
