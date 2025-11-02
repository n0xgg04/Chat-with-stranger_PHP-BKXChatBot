# Vercel Deployment Guide

Hướng dẫn deploy API và Manager lên Vercel.

## Prerequisites

```bash
npm i -g vercel
vercel login
```

## Quick Setup (Recommended)

Run the setup script to configure both projects:

```bash
chmod +x DEPLOY_SETUP.sh
./DEPLOY_SETUP.sh
```

This will:

1. Link both projects to Vercel
2. Prompt you to add all required environment variables
3. Set up for production deployment

## Manual Setup

### Step 1: Link Projects

```bash
# Manager
cd apps/manager
vercel link
# Khi hỏi "Root Directory", nhập: apps/manager

# API
cd ../api
vercel link
# Khi hỏi "Root Directory", nhập: apps/api
```

### Step 2: Add Environment Variables

**Manager (Next.js):**

```bash
cd apps/manager
vercel env add NEXT_PUBLIC_API_URL production
vercel env add NEXT_PUBLIC_SUPABASE_URL production
vercel env add NEXT_PUBLIC_SUPABASE_ANON_KEY production
```

**API (NestJS):**

```bash
cd apps/api
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
```

## Deploy

```bash
# From v2 directory
cd v2
chmod +x deploy-all.sh
./deploy-all.sh
```

**OR deploy individually:**

```bash
# Manager
cd v2/apps/manager
vercel --prod

# API
cd v2/apps/api
vercel --prod
```

## Troubleshooting

### Root Directory Error

**IMPORTANT:** Với monorepo, bạn PHẢI cấu hình Root Directory!

**Cách 1: Qua Vercel Dashboard**

1. Vào https://vercel.com/dashboard
2. Chọn project (manager hoặc api)
3. Settings → General → Root Directory
4. Đảm bảo:
   - Manager: `apps/manager`
   - API: `apps/api`
5. Save và deploy lại

**Cách 2: Fix qua Vercel Dashboard (BẮT BUỘC)**

1. Vào https://vercel.com/dashboard
2. Chọn project **chatbot-api** (hoặc api project của bạn)
3. Settings → General → **Root Directory**
4. Click **Edit** → Nhập: `apps/api`
5. Click **Save**

Làm tương tự cho **chatbot-manager** với Root Directory: `apps/manager`

⚠️ **KHÔNG dùng fix script** - nó sẽ làm hỏng project.json

## Important Notes

### API Serverless Function

- **Cold starts**: Function cache persists across invocations
- **Max duration**: 60 seconds
- **Memory**: 1024 MB
- **Prisma**: Run `yarn prisma:generate` during build

### Database Migrations

Run migrations before first deploy:

```bash
cd v2/apps/api
yarn prisma:migrate:prod
```

### Webhook URLs

After deploying API, update Facebook webhook URL:

- Production: `https://your-api.vercel.app/webhook`
- Preview: `https://your-api-git-branch.vercel.app/webhook`

### Supabase Configuration

**IMPORTANT:** Cấu hình Supabase redirect URLs:

1. Vào Supabase Dashboard → Authentication → URL Configuration
2. **Site URL**: `https://your-manager.vercel.app`
3. **Redirect URLs**: Add:
   - `https://your-manager.vercel.app/auth/callback`
   - `http://localhost:3001/auth/callback` (for local dev)
4. **OAuth Providers** → Google:
   - Authorized redirect URIs: `https://your-manager.vercel.app/auth/callback`

**Nếu không config**, sau khi login sẽ bị redirect về `http://localhost:3000` thay vì domain production!
