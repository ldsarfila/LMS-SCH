/** @type {import('next').NextConfig} */
const nextConfig = {
  images: {
    remotePatterns: [
      {
        protocol: 'http',
        hostname: 'localhost',
        port: '8000',
        pathname: '/storage/**',
      },
    ],
  },
  env: {
    API_URL: process.env.API_URL || 'http://localhost:8000/api/v1',
  },
}

module.exports = nextConfig
