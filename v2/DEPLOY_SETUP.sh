#!/bin/bash

echo "🔧 Setting up Vercel deployment environment"
echo "==========================================="
echo ""

# Check if vercel CLI is installed
if ! command -v vercel &> /dev/null; then
    echo "❌ Vercel CLI not found. Installing..."
    npm i -g vercel
fi

# Check if logged in
if ! vercel whoami &> /dev/null; then
    echo "❌ Not logged in to Vercel. Please run: vercel login"
    exit 1
fi

echo "✅ Vercel CLI is ready"
echo ""

# Setup Manager
echo "📦 Setting up Manager (Next.js)..."
cd apps/manager

echo "Linking project to Vercel..."
vercel link

echo ""
echo "Adding environment variables for Manager:"
vercel env add NEXT_PUBLIC_API_URL production
vercel env add NEXT_PUBLIC_SUPABASE_URL production
vercel env add NEXT_PUBLIC_SUPABASE_ANON_KEY production

cd ../..

# Setup API
echo ""
echo "📦 Setting up API (NestJS)..."
cd apps/api

echo "Linking project to Vercel..."
vercel link

echo ""
echo "Adding environment variables for API:"
vercel env add DATABASE_URL production
vercel env add DIRECT_URL production
vercel env add REDIS_URL production
vercel env add FACEBOOK_VERIFY_TOKEN production
vercel env add ENCRYPT_CODE production
vercel env add CONFIG_DOMAIN production
vercel env add COIN_NEED_TO_FIND_MALE production
vercel env add COIN_NEED_TO_FIND_FEMALE production
vercel env add VIP_USER_PID production
vercel env add PERSONASID_PARTNER production

cd ../..

cd ../..

echo ""
echo "========================================"
echo "✅ Setup complete!"
echo "========================================"
echo ""
echo "🔴 CRITICAL: Configure Root Directory in Vercel Dashboard!"
echo ""
echo "1. Go to https://vercel.com/dashboard"
echo "2. For EACH project (manager and api):"
echo "   - Settings → General → Root Directory"
echo "   - Manager: apps/manager"
echo "   - API: apps/api"
echo "   - Click Save"
echo ""
echo "Then deploy with:"
echo "  cd v2 && ./deploy-all.sh"
echo ""

