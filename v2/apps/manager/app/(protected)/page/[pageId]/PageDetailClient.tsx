'use client';

import { useState } from 'react';
import { useRouter } from 'next/navigation';
import {
  ArrowLeft,
  Users,
  MessageCircle,
  TrendingUp,
  Clock,
  Settings,
  RefreshCw,
} from 'lucide-react';
import { usePage } from '@/lib/hooks/use-pages';

interface PageDetailClientProps {
  pageId: string;
}

interface Stats {
  totalUsers: number;
  activePairs: number;
  waitingUsers: number;
  totalChats: number;
}

export default function PageDetailClient({ pageId }: PageDetailClientProps) {
  const router = useRouter();
  const { data: page, isLoading, refetch } = usePage(pageId);
  const [stats] = useState<Stats>({
    totalUsers: Math.floor(Math.random() * 1000),
    activePairs: Math.floor(Math.random() * 50),
    waitingUsers: Math.floor(Math.random() * 20),
    totalChats: Math.floor(Math.random() * 5000),
  });
  const [refreshing, setRefreshing] = useState(false);

  const handleRefresh = async () => {
    setRefreshing(true);
    await refetch();
    setTimeout(() => setRefreshing(false), 500);
  };

  if (isLoading) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <div className="text-center">
          <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mx-auto"></div>
          <p className="mt-4 text-gray-600">Đang tải...</p>
        </div>
      </div>
    );
  }

  if (!page) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <div className="text-center">
          <p className="text-gray-600">Page không tồn tại</p>
          <button
            onClick={() => router.push('/dashboard')}
            className="mt-4 text-indigo-600 hover:text-indigo-700"
          >
            Quay lại Dashboard
          </button>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gray-50">
      <nav className="bg-white shadow-sm border-b border-gray-200">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex justify-between h-16">
            <div className="flex items-center gap-4">
              <button
                onClick={() => router.push('/dashboard')}
                className="text-gray-600 hover:text-gray-900"
              >
                <ArrowLeft className="w-6 h-6" />
              </button>
              <div>
                <h1 className="text-xl font-bold text-gray-900">{page.name}</h1>
                <p className="text-sm text-gray-500">ID: {page.pageId}</p>
              </div>
            </div>
            <div className="flex items-center gap-3">
              <button
                onClick={handleRefresh}
                disabled={refreshing}
                className="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 hover:text-gray-900 disabled:opacity-50"
              >
                <RefreshCw
                  className={`w-4 h-4 ${refreshing ? 'animate-spin' : ''}`}
                />
                Refresh
              </button>
              <button className="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:text-gray-900">
                <Settings className="w-4 h-4" />
                Cài đặt
              </button>
            </div>
          </div>
        </div>
      </nav>

      <main className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm font-medium text-gray-600">
                  Tổng người dùng
                </p>
                <p className="text-3xl font-bold text-gray-900 mt-2">
                  {stats.totalUsers.toLocaleString()}
                </p>
              </div>
              <div className="p-3 bg-blue-100 rounded-lg">
                <Users className="w-6 h-6 text-blue-600" />
              </div>
            </div>
          </div>

          <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm font-medium text-gray-600">Đang chat</p>
                <p className="text-3xl font-bold text-gray-900 mt-2">
                  {stats.activePairs.toLocaleString()}
                </p>
              </div>
              <div className="p-3 bg-green-100 rounded-lg">
                <MessageCircle className="w-6 h-6 text-green-600" />
              </div>
            </div>
          </div>

          <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm font-medium text-gray-600">Đang chờ</p>
                <p className="text-3xl font-bold text-gray-900 mt-2">
                  {stats.waitingUsers.toLocaleString()}
                </p>
              </div>
              <div className="p-3 bg-yellow-100 rounded-lg">
                <Clock className="w-6 h-6 text-yellow-600" />
              </div>
            </div>
          </div>

          <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm font-medium text-gray-600">
                  Tổng cuộc chat
                </p>
                <p className="text-3xl font-bold text-gray-900 mt-2">
                  {stats.totalChats.toLocaleString()}
                </p>
              </div>
              <div className="p-3 bg-purple-100 rounded-lg">
                <TrendingUp className="w-6 h-6 text-purple-600" />
              </div>
            </div>
          </div>
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 className="text-lg font-semibold text-gray-900 mb-4">
              Thông tin Page
            </h2>
            <div className="space-y-3">
              <div className="flex justify-between py-2 border-b border-gray-100">
                <span className="text-sm text-gray-600">Trạng thái</span>
                <span
                  className={`text-sm font-medium ${
                    page.active ? 'text-green-600' : 'text-red-600'
                  }`}
                >
                  {page.active ? 'Đang hoạt động' : 'Tạm dừng'}
                </span>
              </div>
              <div className="flex justify-between py-2 border-b border-gray-100">
                <span className="text-sm text-gray-600">Cài đặt</span>
                <span
                  className={`text-sm font-medium ${
                    page.installed ? 'text-green-600' : 'text-yellow-600'
                  }`}
                >
                  {page.installed ? 'Đã cài đặt' : 'Chưa cài đặt'}
                </span>
              </div>
              <div className="flex justify-between py-2 border-b border-gray-100">
                <span className="text-sm text-gray-600">Ngày tạo</span>
                <span className="text-sm font-medium text-gray-900">
                  {new Date(page.createdAt).toLocaleDateString('vi-VN', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                  })}
                </span>
              </div>
            </div>
          </div>

          <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 className="text-lg font-semibold text-gray-900 mb-4">
              Webhook URL
            </h2>
            <div className="bg-gray-50 rounded border border-gray-200 p-3">
              <code className="text-sm text-gray-800 break-all">
                {process.env.NEXT_PUBLIC_API_URL || 'http://localhost:3000'}
                /webhook?pageId={pageId}
              </code>
            </div>
            <p className="mt-3 text-xs text-gray-500">
              Sử dụng URL này để cấu hình webhook trên Facebook Developers
              Console
            </p>
          </div>
        </div>

        <div className="mt-6 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <h2 className="text-lg font-semibold text-gray-900 mb-4">
            Người dùng gần đây
          </h2>
          <div className="text-center py-8 text-gray-500">
            <Users className="w-12 h-12 mx-auto mb-2 text-gray-400" />
            <p>Chức năng đang được phát triển</p>
          </div>
        </div>
      </main>
    </div>
  );
}
