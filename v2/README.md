# Chat with Stranger - Monorepo

Monorepo cho dự án Chat with Stranger Bot sử dụng Turborepo.

## Cấu trúc

```
v2/
├── apps/
│   └── api/          # NestJS API Backend
├── packages/
│   ├── ui/           # Shared UI components
│   ├── eslint-config/
│   └── typescript-config/
├── package.json
└── turbo.json
```

## Yêu cầu

- Node.js >= 18
- Yarn 1.22.22
- PostgreSQL (cho API)

## Cài đặt

```bash
cd v2
yarn install
```

## Development

### Chạy tất cả apps

```bash
yarn dev
```

### Chạy riêng API

```bash
yarn dev:api
```

### Build tất cả

```bash
yarn build
```

## API Commands

### Prisma

```bash
yarn prisma:generate    # Generate Prisma Client
yarn prisma:migrate     # Run migrations
yarn prisma:studio      # Open Prisma Studio
```

### Migration

```bash
yarn migrate:pages      # Migrate pages từ JSON sang DB
```

### Production

```bash
yarn build
yarn start:prod:api
```

## Apps

### @chatbot/api

NestJS API backend cho Facebook Messenger chatbot.

**Features:**

- Facebook Messenger webhook integration
- Multi-page support
- PostgreSQL với Prisma ORM
- User matching và chat system
- Coin system

**Docs:**

- [Migration Guide](./apps/api/MIGRATION_GUIDE.md)
- [Multi-Page Setup](./apps/api/MULTI_PAGE_SETUP.md)
- [Database Migration](./apps/api/DATABASE_MIGRATION.md)

**Endpoints:**

- `GET /webhook` - Webhook verification
- `POST /webhook` - Webhook events
- `GET /setup/pages` - List pages
- `POST /setup/page` - Create page
- `POST /setup/install/:pageId` - Setup bot

## Packages

### @chatbot/ui

Shared React components (future use).

### @chatbot/eslint-config

Shared ESLint configuration.

### @chatbot/typescript-config

Shared TypeScript configuration.

## Environment Variables

Tạo file `.env` trong `apps/api/`:

```env
DATABASE_URL="postgresql://user:password@localhost:5432/chatbot?schema=public"
DIRECT_URL="postgresql://user:password@localhost:5432/chatbot?schema=public"

FACEBOOK_VERIFY_TOKEN="your_verify_token"
FACEBOOK_PAGE_ACCESS_TOKEN="your_page_access_token"

ENCRYPT_CODE="n0xgg04"
CONFIG_DOMAIN="http://localhost:3000"

COIN_NEED_TO_FIND_MALE=5
COIN_NEED_TO_FIND_FEMALE=5

VIP_USER_PID="578389133835364"
PERSONASID_PARTNER="578389133835364"
```

## Turborepo

### Tasks

- `build` - Build tất cả apps và packages
- `dev` - Run development mode
- `lint` - Lint code
- `check-types` - Type checking
- `start:dev` - Start API in dev mode
- `start:prod` - Start API in production mode
- `prisma:*` - Prisma commands

### Caching

Turborepo tự động cache build outputs để tăng tốc độ build.

### Filtering

Chạy commands cho specific apps:

```bash
turbo run build --filter=@chatbot/api
turbo run dev --filter=@chatbot/api
```

## Development Workflow

1. **Setup database:**

```bash
cd v2
yarn prisma:generate
yarn prisma:migrate
```

2. **Migrate data (nếu cần):**

```bash
yarn migrate:pages
```

3. **Start development:**

```bash
yarn dev:api
```

4. **Open Prisma Studio:**

```bash
yarn prisma:studio
```

## Production Deployment

1. **Build:**

```bash
yarn build
```

2. **Run migrations:**

```bash
cd apps/api
yarn prisma:migrate:prod
```

3. **Start:**

```bash
yarn start:prod:api
```

## Adding New Apps

1. Create trong `apps/`:

```bash
cd apps
mkdir my-new-app
cd my-new-app
yarn init
```

2. Update `package.json`:

```json
{
  "name": "@chatbot/my-new-app",
  "version": "0.0.1"
}
```

3. App sẽ tự động được detect bởi Turborepo.

## Troubleshooting

### Lỗi: "Cannot find module"

```bash
yarn install
yarn prisma:generate
```

### Lỗi: Turbo cache issues

```bash
turbo run build --force
```

### Lỗi: Port already in use

```bash
lsof -ti:3000 | xargs kill -9
```

## Scripts Reference

| Command                | Description               |
| ---------------------- | ------------------------- |
| `yarn dev`             | Run all apps in dev mode  |
| `yarn dev:api`         | Run API only              |
| `yarn build`           | Build all apps            |
| `yarn lint`            | Lint all code             |
| `yarn format`          | Format code with Prettier |
| `yarn prisma:generate` | Generate Prisma Client    |
| `yarn prisma:migrate`  | Run database migrations   |
| `yarn prisma:studio`   | Open Prisma Studio        |
| `yarn start:prod:api`  | Start API in production   |

## License

Private project
