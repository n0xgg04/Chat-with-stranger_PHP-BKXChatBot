import { create } from 'zustand';
import { persist } from 'zustand/middleware';

interface Page {
  id: number;
  pageId: string;
  name: string;
  active: boolean;
  installed: boolean;
  createdAt: string;
}

interface PageState {
  pages: Page[];
  selectedPageId: string | null;
  setPages: (pages: Page[]) => void;
  addPage: (page: Page) => void;
  updatePage: (pageId: string, updates: Partial<Page>) => void;
  selectPage: (pageId: string | null) => void;
  clearPages: () => void;
}

export const usePageStore = create<PageState>()(
  persist(
    (set) => ({
      pages: [],
      selectedPageId: null,
      setPages: (pages) => set({ pages }),
      addPage: (page) =>
        set((state) => ({ pages: [...state.pages, page] })),
      updatePage: (pageId, updates) =>
        set((state) => ({
          pages: state.pages.map((p) =>
            p.pageId === pageId ? { ...p, ...updates } : p
          ),
        })),
      selectPage: (pageId) => set({ selectedPageId: pageId }),
      clearPages: () => set({ pages: [], selectedPageId: null }),
    }),
    {
      name: 'page-storage',
    }
  )
);

