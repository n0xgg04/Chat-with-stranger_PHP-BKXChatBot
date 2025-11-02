# Manager App Setup

## Stack đã cài đặt

### State Management
- **Zustand** - Global state management với persist
- **TanStack Query** - Server state management và data fetching

### UI
- **Tailwind CSS** - Utility-first CSS
- **ShadcnUI** (ready to add components)
- **Lucide React** - Icons

### Auth
- **Supabase Auth** - Authentication với Google OAuth

## Cấu trúc thư mục

```
apps/manager/
├── app/                      # Next.js App Router
│   ├── login/               # Login page
│   ├── dashboard/           # Dashboard (danh sách pages)
│   ├── page/[pageId]/       # Chi tiết page
│   └── auth/callback/       # OAuth callback
├── components/              # React components
│   └── ConnectPageModal.tsx
├── lib/
│   ├── stores/              # Zustand stores
│   │   ├── auth-store.ts   # Auth state với persist
│   │   └── page-store.ts   # Page state với persist
│   ├── hooks/               # Custom hooks
│   │   └── use-pages.ts    # TanStack Query hooks
│   ├── api/                 # API client
│   │   └── client.ts       # Axios instance + API functions
│   └── providers/           # Context providers
│       └── query-provider.tsx
└── utils/
    └── supabase/            # Supabase clients
        ├── client.ts
        ├── server.ts
        └── middleware.ts
```

## Zustand Stores

### 1. Auth Store (`lib/stores/auth-store.ts`)
```typescript
interface AuthState {
  user: User | null;
  setUser: (user: User | null) => void;
  clearUser: () => void;
}
```

**Persist**: LocalStorage với key `auth-storage`

**Usage**:
```typescript
import { useAuthStore } from '@/lib/stores/auth-store';

const { user, setUser, clearUser } = useAuthStore();
```

### 2. Page Store (`lib/stores/page-store.ts`)
```typescript
interface PageState {
  pages: Page[];
  selectedPageId: string | null;
  setPages: (pages: Page[]) => void;
  addPage: (page: Page) => void;
  updatePage: (pageId: string, updates: Partial<Page>) => void;
  selectPage: (pageId: string | null) => void;
  clearPages: () => void;
}
```

**Persist**: LocalStorage với key `page-storage`

**Usage**:
```typescript
import { usePageStore } from '@/lib/stores/page-store';

const { pages, addPage, updatePage } = usePageStore();
```

## TanStack Query Hooks

### API Client (`lib/api/client.ts`)
```typescript
export const pageApi = {
  getAll: () => apiClient.get<Page[]>('/setup/pages'),
  getOne: (pageId: string) => apiClient.get<Page>(`/setup/page/${pageId}`),
  create: (data: CreatePageDto) => apiClient.post('/setup/page', data),
  install: (pageId: string) => apiClient.post(`/setup/install/${pageId}`),
  getStatus: () => apiClient.get('/setup/status'),
};
```

### Custom Hooks (`lib/hooks/use-pages.ts`)

#### 1. usePages - Lấy danh sách pages
```typescript
const { data: pages, isLoading, error } = usePages();
```

#### 2. usePage - Lấy chi tiết 1 page
```typescript
const { data: page, isLoading } = usePage(pageId);
```

#### 3. useCreatePage - Tạo page mới
```typescript
const createPage = useCreatePage();

createPage.mutate({
  pageId: 'PAGE001',
  name: 'My Page',
  accessToken: 'EAA...',
});
```

#### 4. useInstallPage - Setup bot cho page
```typescript
const installPage = useInstallPage();

installPage.mutate(pageId);
```

#### 5. usePageStatus - Lấy status tổng quan
```typescript
const { data: status } = usePageStatus();
```

## Query Provider

Đã setup trong `app/layout.tsx`:

```typescript
<QueryProvider>
  {children}
</QueryProvider>
```

**Features**:
- Stale time: 60 seconds
- Refetch on window focus: disabled
- React Query Devtools (development only)

## Environment Variables

Tạo `.env.local`:

```env
NEXT_PUBLIC_SUPABASE_URL=your_supabase_url
NEXT_PUBLIC_SUPABASE_ANON_KEY=your_supabase_anon_key
NEXT_PUBLIC_API_URL=http://localhost:3000
```

## Usage Examples

### 1. Component với TanStack Query

```typescript
'use client';

import { usePages, useCreatePage } from '@/lib/hooks/use-pages';

export default function PageList() {
  const { data: pages, isLoading, error } = usePages();
  const createPage = useCreatePage();

  const handleCreate = async () => {
    await createPage.mutateAsync({
      pageId: 'NEW_PAGE',
      name: 'New Page',
      accessToken: 'token',
    });
  };

  if (isLoading) return <div>Loading...</div>;
  if (error) return <div>Error: {error.message}</div>;

  return (
    <div>
      {pages?.map(page => (
        <div key={page.id}>{page.name}</div>
      ))}
      <button onClick={handleCreate}>Create Page</button>
    </div>
  );
}
```

### 2. Component với Zustand

```typescript
'use client';

import { usePageStore } from '@/lib/stores/page-store';

export default function PageSelector() {
  const { pages, selectedPageId, selectPage } = usePageStore();

  return (
    <select 
      value={selectedPageId || ''} 
      onChange={(e) => selectPage(e.target.value)}
    >
      {pages.map(page => (
        <option key={page.id} value={page.pageId}>
          {page.name}
        </option>
      ))}
    </select>
  );
}
```

### 3. Kết hợp Zustand + TanStack Query

```typescript
'use client';

import { usePages } from '@/lib/hooks/use-pages';
import { usePageStore } from '@/lib/stores/page-store';
import { useEffect } from 'react';

export default function Dashboard() {
  const { data: pages } = usePages();
  const { setPages, selectedPageId } = usePageStore();

  useEffect(() => {
    if (pages) {
      setPages(pages);
    }
  }, [pages, setPages]);

  return <div>Dashboard with {pages?.length} pages</div>;
}
```

## ShadcnUI Components

Để thêm components:

```bash
npx shadcn@latest add button
npx shadcn@latest add card
npx shadcn@latest add dialog
npx shadcn@latest add input
npx shadcn@latest add label
npx shadcn@latest add select
npx shadcn@latest add sonner
```

## Scripts

```bash
# Development
yarn dev                    # Port 3001

# Build
yarn build

# Lint
yarn lint
```

## Next Steps

1. ✅ Setup Zustand stores
2. ✅ Setup TanStack Query
3. ✅ Create API client
4. ✅ Create custom hooks
5. ⏳ Add ShadcnUI components
6. ⏳ Refactor existing components to use new stack
7. ⏳ Add loading states với Suspense
8. ⏳ Add error boundaries
9. ⏳ Add optimistic updates

## Tips

### Zustand Persist
Data được lưu trong LocalStorage và tự động sync giữa tabs.

### TanStack Query Cache
- Query results được cache
- Auto refetch khi stale
- Optimistic updates support
- Devtools để debug

### Type Safety
Tất cả API calls và stores đều có TypeScript types đầy đủ.

