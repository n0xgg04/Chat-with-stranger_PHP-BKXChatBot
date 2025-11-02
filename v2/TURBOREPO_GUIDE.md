# Turborepo Setup Guide

## Tổng quan

Dự án đã được migrate sang Turborepo monorepo structure để quản lý multiple apps và shared packages hiệu quả hơn.

## Cấu trúc Monorepo

```
v2/
├── apps/
│   └── api/                    # NestJS API (@chatbot/api)
│       ├── src/
│       ├── prisma/
│       ├── package.json
│       └── ...
├── packages/
│   ├── ui/                     # Shared UI components
│   ├── eslint-config/          # Shared ESLint config
│   └── typescript-config/      # Shared TypeScript config
├── package.json                # Root package.json
├── turbo.json                  # Turborepo configuration
└── yarn.lock
```

## Turborepo Configuration

### turbo.json

```json
{
  "$schema": "https://turborepo.com/schema.json",
  "ui": "tui",
  "tasks": {
    "build": {
      "dependsOn": ["^build"],
      "outputs": ["dist/**", ".next/**"]
    },
    "dev": {
      "cache": false,
      "persistent": true
    },
    "start:dev": {
      "cache": false,
      "persistent": true
    },
    "prisma:generate": {
      "cache": false
    }
  }
}
```

### Task Types

#### 1. Build Tasks

- **Cacheable**: Outputs được cache để tăng tốc độ
- **Outputs**: `dist/**`, `.next/**`
- **Dependencies**: Chạy sau khi dependencies build xong

#### 2. Dev Tasks

- **Not Cacheable**: Luôn chạy mới
- **Persistent**: Chạy liên tục (không exit)

#### 3. Prisma Tasks

- **Not Cacheable**: Database operations không cache được

## Workspaces

### Root package.json

```json
{
  "name": "chatbot-monorepo",
  "private": true,
  "workspaces": ["apps/*", "packages/*"]
}
```

### App package.json

```json
{
  "name": "@chatbot/api",
  "version": "0.0.1",
  "private": true
}
```

## Commands

### Root Level (v2/)

#### Development

```bash
yarn dev                # Run all apps in dev mode
yarn dev:api           # Run API only
```

#### Build

```bash
yarn build             # Build all apps
turbo run build --force  # Force rebuild (bypass cache)
```

#### Prisma

```bash
yarn prisma:generate   # Generate Prisma Client
yarn prisma:migrate    # Run migrations
yarn prisma:studio     # Open Prisma Studio
```

#### Production

```bash
yarn start:api         # Start API (after build)
yarn start:prod:api    # Start API in production mode
```

### Filtering

Chạy commands cho specific apps:

```bash
# Filter by app name
turbo run build --filter=@chatbot/api
turbo run dev --filter=@chatbot/api

# Filter by directory
turbo run build --filter=./apps/api

# Multiple filters
turbo run build --filter=@chatbot/api --filter=@chatbot/web
```

### Scoping

Chạy commands trong specific workspace:

```bash
# Từ root
yarn workspace @chatbot/api build
yarn workspace @chatbot/api start:dev

# Hoặc cd vào app
cd apps/api
yarn build
yarn start:dev
```

## Caching

### Local Cache

Turborepo cache build outputs locally trong `.turbo/`:

```
.turbo/
├── cache/
│   └── <hash>/
│       └── dist/
└── runs/
```

### Cache Behavior

#### Cacheable Tasks

```json
{
  "build": {
    "outputs": ["dist/**"],
    "inputs": ["src/**", "package.json"]
  }
}
```

Khi inputs không thay đổi → restore từ cache.

#### Non-Cacheable Tasks

```json
{
  "dev": {
    "cache": false
  }
}
```

Luôn chạy mới, không cache.

### Clear Cache

```bash
rm -rf .turbo
turbo run build --force
```

## Dependencies

### Internal Dependencies

Nếu có shared packages:

```json
{
  "dependencies": {
    "@chatbot/shared": "workspace:*"
  }
}
```

Turborepo tự động build dependencies trước:

```json
{
  "build": {
    "dependsOn": ["^build"]
  }
}
```

### External Dependencies

Install cho specific app:

```bash
yarn workspace @chatbot/api add axios
yarn workspace @chatbot/api add -D @types/node
```

Install cho root:

```bash
yarn add -W turbo
```

## Environment Variables

### App-specific .env

```
apps/api/.env
```

### Turbo task inputs

```json
{
  "build": {
    "inputs": ["$TURBO_DEFAULT$", ".env*"]
  }
}
```

## Development Workflow

### 1. Initial Setup

```bash
cd v2
yarn install
yarn prisma:generate
yarn prisma:migrate
```

### 2. Development

```bash
# Terminal 1: Run API
yarn dev:api

# Terminal 2: Prisma Studio
yarn prisma:studio

# Terminal 3: Watch logs
tail -f apps/api/logs/*.log
```

### 3. Adding New Feature

```bash
# Create branch
git checkout -b feature/new-feature

# Make changes in apps/api/src/

# Test
yarn dev:api

# Build
yarn build

# Commit
git add .
git commit -m "feat: add new feature"
```

## Production Deployment

### 1. Build

```bash
yarn build
```

### 2. Deploy API

```bash
cd apps/api
yarn prisma:migrate:prod
yarn start:prod
```

### Docker (Optional)

```dockerfile
FROM node:18-alpine AS base

FROM base AS builder
WORKDIR /app
COPY . .
RUN yarn install --frozen-lockfile
RUN yarn turbo run build --filter=@chatbot/api

FROM base AS runner
WORKDIR /app
COPY --from=builder /app/apps/api/dist ./dist
COPY --from=builder /app/apps/api/node_modules ./node_modules
COPY --from=builder /app/apps/api/package.json ./package.json

EXPOSE 3000
CMD ["yarn", "start:prod"]
```

## Troubleshooting

### Issue: "Cannot find module"

```bash
yarn install
yarn prisma:generate
```

### Issue: Cache problems

```bash
turbo run build --force
```

### Issue: Workspace not found

```bash
# Check workspaces
yarn workspaces info

# Reinstall
rm -rf node_modules
rm yarn.lock
yarn install
```

### Issue: Port already in use

```bash
lsof -ti:3000 | xargs kill -9
```

### Issue: Prisma Client not generated

```bash
yarn prisma:generate
```

## Best Practices

### 1. Task Configuration

- ✅ Cache build tasks
- ✅ Don't cache dev/watch tasks
- ✅ Don't cache database operations
- ✅ Use `persistent: true` for long-running tasks

### 2. Dependencies

- ✅ Use `workspace:*` for internal packages
- ✅ Install shared deps in root when possible
- ✅ Keep app-specific deps in app package.json

### 3. Caching

- ✅ Define proper `outputs` for cacheable tasks
- ✅ Include relevant files in `inputs`
- ✅ Use `.turboignore` for files to ignore

### 4. Scripts

- ✅ Use consistent naming across apps
- ✅ Create shortcuts in root package.json
- ✅ Use filters for app-specific commands

## Advanced Features

### Remote Caching (Optional)

Setup Vercel Remote Cache:

```bash
npx turbo login
npx turbo link
```

### Parallel Execution

```bash
# Run in parallel
turbo run build --parallel

# Limit concurrency
turbo run build --concurrency=2
```

### Dry Run

```bash
turbo run build --dry-run
```

### Graph Visualization

```bash
turbo run build --graph
```

## Migration Checklist

- [x] Move nest → v2/apps/api
- [x] Update package.json name to @chatbot/api
- [x] Configure turbo.json tasks
- [x] Add root scripts for common commands
- [x] Setup workspaces in root package.json
- [x] Install dependencies
- [x] Test dev mode
- [x] Test build
- [x] Test Prisma commands
- [x] Update documentation

## Next Steps

1. ✅ Add more apps (web, admin, etc.)
2. ✅ Create shared packages (@chatbot/shared, @chatbot/types)
3. ✅ Setup remote caching
4. ✅ Add CI/CD pipeline
5. ✅ Configure Docker for production

## Resources

- [Turborepo Docs](https://turbo.build/repo/docs)
- [Yarn Workspaces](https://classic.yarnpkg.com/en/docs/workspaces/)
- [Monorepo Best Practices](https://turbo.build/repo/docs/handbook)
