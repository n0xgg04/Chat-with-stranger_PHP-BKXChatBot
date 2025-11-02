# Vercel Deployment Guide

Hướng dẫn deploy 2 services (API và Manager) lên Vercel trong monorepo.

## Chuẩn bị

### 1. Cài đặt Vercel CLI

```bash
npm i -g vercel
```

### 2. Login vào Vercel

```bash
vercel login
```

## Deploy Manager (Next.js Frontend)

### Bước 1: Di chuyển vào thư mục manager

```bash
cd v2/apps/manager
```

### Bước 2: Link project với Vercel

```bash
vercel link
```

Chọn:
- Set up and deploy: Yes
- Scope: Chọn team/account của bạn
- Link to existing project: No (nếu lần đầu)
- Project name: `chatbot-manager` (hoặc tên khác)
- Root directory: `v2/apps/manager`

### Bước 3: Cấu hình Environment Variables

```bash
vercel env add NEXT_PUBLIC_API_URL
vercel env add NEXT_PUBLIC_SUPABASE_URL
vercel env add NEXT_PUBLIC_SUPABASE_ANON_KEY
```

Nhập giá trị cho từng biến khi được yêu cầu.

### Bước 4: Deploy

```bash
vercel --prod
```

## Deploy API (NestJS Backend)

### Bước 1: Di chuyển vào thư mục api

```bash
cd v2/apps/api
```

### Bước 2: Link project với Vercel

```bash
vercel link
```

Chọn:
- Set up and deploy: Yes
- Scope: Chọn team/account của bạn
- Link to existing project: No (nếu lần đầu)
- Project name: `chatbot-api` (hoặc tên khác)
- Root directory: `v2/apps/api`

### Bước 3: Cấu hình Environment Variables

```bash
vercel env add DATABASE_URL
vercel env add REDIS_HOST
vercel env add REDIS_PORT
vercel env add REDIS_PASSWORD
vercel env add FB_PAGE_ACCESS_TOKEN
vercel env add FB_VERIFY_TOKEN
vercel env add FB_APP_SECRET
```

### Bước 4: Deploy

```bash
vercel --prod
```

## Lưu ý quan trọng

### 1. NestJS trên Vercel (Serverless)

Vercel chạy serverless functions, nên:
- API sẽ "cold start" khi không có traffic
- WebSocket không được hỗ trợ
- Long-running processes bị giới hạn (max 10s cho Hobby plan, 60s cho Pro)

Nếu cần WebSocket hoặc long-running processes, nên deploy API lên:
- Railway
- Render
- DigitalOcean App Platform
- AWS/GCP/Azure

### 2. Database Migrations

Chạy migrations trước khi deploy:

```bash
cd v2/apps/api
yarn prisma:migrate:prod
```

### 3. Monorepo Settings

Khi setup trên Vercel Dashboard:
- Root Directory: `v2/apps/manager` hoặc `v2/apps/api`
- Build Command: Đã được cấu hình trong `vercel.json`
- Output Directory: Đã được cấu hình trong `vercel.json`

### 4. Automatic Deployments

Khi đã link project, mỗi lần push code lên GitHub:
- Branch `main` → Production deployment
- Branches khác → Preview deployment

## Kiểm tra Deployment

### Manager (Frontend)

```bash
curl https://chatbot-manager.vercel.app
```

### API (Backend)

```bash
curl https://chatbot-api.vercel.app/health
```

## Xem Logs

```bash
vercel logs [deployment-url]
```

## Deploy từ Root Directory

Nếu muốn deploy từ root của monorepo:

```bash
cd v2

vercel --scope=your-team --cwd apps/manager
vercel --scope=your-team --cwd apps/api
```

## Alternative: Deploy Script

Tạo script để deploy cả 2 services:

```bash
chmod +x deploy-all.sh
./deploy-all.sh
```

## Troubleshooting

### Build fails

Kiểm tra:
1. Dependencies đã được install đúng chưa
2. Environment variables đã được set chưa
3. Build command có chạy thành công local không

### Cold start quá lâu (API)

Cân nhắc:
1. Optimize bundle size
2. Lazy load modules
3. Deploy lên platform khác (Railway, Render)

### Database connection timeout

1. Sử dụng connection pooling (PgBouncer)
2. Giảm connection timeout
3. Sử dụng Prisma Data Proxy hoặc serverless-friendly database (PlanetScale, Neon)

