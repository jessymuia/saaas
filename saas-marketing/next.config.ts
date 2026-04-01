import type { NextConfig } from "next";

const LARAVEL_BACKEND = "http://localhost:8000";

const nextConfig: NextConfig = {
  async rewrites() {
    return [
      {
        source: "/app/:path*",
        destination: `${LARAVEL_BACKEND}/app/:path*`,
      },
      {
        source: "/admin/:path*",
        destination: `${LARAVEL_BACKEND}/admin/:path*`,
      },
      {
        source: "/livewire/:path*",
        destination: `${LARAVEL_BACKEND}/livewire/:path*`,
      },
      {
        source: "/css/filament/:path*",
        destination: `${LARAVEL_BACKEND}/css/filament/:path*`,
      },
      {
        source: "/js/filament/:path*",
        destination: `${LARAVEL_BACKEND}/js/filament/:path*`,
      },
      {
        source: "/fonts/filament/:path*",
        destination: `${LARAVEL_BACKEND}/fonts/filament/:path*`,
      },
      {
        source: "/storage/:path*",
        destination: `${LARAVEL_BACKEND}/storage/:path*`,
      },
      {
        source: "/up",
        destination: `${LARAVEL_BACKEND}/up`,
      },
    ];
  },
};

export default nextConfig;
