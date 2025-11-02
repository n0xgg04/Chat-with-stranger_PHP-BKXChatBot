import axios from 'axios';
import { createClient } from '@/utils/supabase/client';

const apiClient = axios.create({
  baseURL: process.env.NEXT_PUBLIC_API_URL || 'http://localhost:3000',
  headers: {
    'Content-Type': 'application/json',
  },
});

apiClient.interceptors.request.use(async (config) => {
  try {
    const supabase = createClient();
    const {
      data: { session },
    } = await supabase.auth.getSession();
    
    if (session?.access_token) {
      config.headers.Authorization = `Bearer ${session.access_token}`;
    }
  } catch (error) {
    console.error('Interceptor error:', error);
  }
  
  return config;
});

export default apiClient;

export interface Page {
  id: number;
  pageId: string;
  name: string;
  accessToken?: string;
  verifyToken?: string;
  cfs: string;
  active: boolean;
  installed: boolean;
  createdAt: string;
  updatedAt: string;
}

export interface CreatePageDto {
  pageId: string;
  name: string;
  accessToken: string;
  verifyToken?: string;
  ownerId?: string;
}

export interface PageStats {
  totalUsers: number;
  activePairs: number;
  waitingUsers: number;
  totalChats: number;
}

export const pageApi = {
  getAll: () => apiClient.get<Page[]>('/setup/pages'),
  
  getOne: (pageId: string) => apiClient.get<Page>(`/setup/page/${pageId}`),
  
  create: (data: CreatePageDto) =>
    apiClient.post<{ success: boolean; message: string; page: Page }>(
      '/setup/page',
      data
    ),
  
  install: (pageId: string) =>
    apiClient.post<{ success: boolean; message: string; page: Page }>(
      `/setup/install/${pageId}`
    ),
  
  getStatus: () =>
    apiClient.get<{
      installed: boolean;
      totalPages: number;
      activePages: number;
      installedPages: number;
      pages: Array<{
        pageId: string;
        name: string;
        active: boolean;
        installed: boolean;
      }>;
    }>('/setup/status'),
};

export const statsApi = {
  getStats: (pageId?: string) => 
    pageId 
      ? apiClient.get<PageStats>(`/stats?pageId=${pageId}`)
      : apiClient.get<PageStats>('/stats'),
};

