import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { pageApi, statsApi, type CreatePageDto } from '../api/client';
import { usePageStore } from '../stores/page-store';

export const usePages = () => {
  const { setPages } = usePageStore();

  return useQuery({
    queryKey: ['pages'],
    queryFn: async () => {
      const response = await pageApi.getAll();
      setPages(response.data);
      return response.data;
    },
  });
};

export const usePage = (pageId: string) => {
  return useQuery({
    queryKey: ['page', pageId],
    queryFn: async () => {
      const response = await pageApi.getOne(pageId);
      return response.data;
    },
    enabled: !!pageId,
  });
};

export const useCreatePage = () => {
  const queryClient = useQueryClient();
  const { addPage } = usePageStore();

  return useMutation({
    mutationFn: async (data: CreatePageDto) => {
      const response = await pageApi.create(data);
      return response.data;
    },
    onSuccess: (data) => {
      if (data.success && data.page) {
        addPage(data.page);
        queryClient.invalidateQueries({ queryKey: ['pages'] });
      }
    },
  });
};

export const useInstallPage = () => {
  const queryClient = useQueryClient();
  const { updatePage } = usePageStore();

  return useMutation({
    mutationFn: async (pageId: string) => {
      const response = await pageApi.install(pageId);
      return response.data;
    },
    onSuccess: (data, pageId) => {
      if (data.success) {
        updatePage(pageId, { installed: true });
        queryClient.invalidateQueries({ queryKey: ['pages'] });
        queryClient.invalidateQueries({ queryKey: ['page', pageId] });
      }
    },
  });
};

export const usePageStatus = () => {
  return useQuery({
    queryKey: ['page-status'],
    queryFn: async () => {
      const response = await pageApi.getStatus();
      return response.data;
    },
  });
};

export const useStats = (pageId?: string) => {
  return useQuery({
    queryKey: ['stats', pageId],
    queryFn: async () => {
      const response = await statsApi.getStats(pageId);
      return response.data;
    },
    refetchInterval: 30000,
  });
};

