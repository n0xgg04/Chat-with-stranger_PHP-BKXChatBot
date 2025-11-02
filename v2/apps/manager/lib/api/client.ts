import axios from 'axios';

const apiClient = axios.create({
  baseURL: process.env.NEXT_PUBLIC_API_URL || 'http://localhost:3000',
  headers: {
    'Content-Type': 'application/json',
  },
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

