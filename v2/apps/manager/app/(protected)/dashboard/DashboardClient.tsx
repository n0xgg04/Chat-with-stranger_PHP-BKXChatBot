'use client';

import { useState } from 'react';
import { Plus, LogOut, MessageCircle, Users, TrendingUp } from 'lucide-react';
import { useRouter } from 'next/navigation';
import { useAuth, useSignOut } from '@/lib/hooks/use-auth';
import { usePages } from '@/lib/hooks/use-pages';
import ConnectPageModal from '@/components/ConnectPageModal';

export default function DashboardClient() {
  const router = useRouter();
  const { user } = useAuth();
  const signOut = useSignOut();
  const { data: pages, isLoading } = usePages();
  const [showConnectModal, setShowConnectModal] = useState(false);

  const handleLogout = () => {
    signOut.mutate();
  };

  const handlePageClick = (pageId: string) => {
    router.push(`/page/${pageId}`);
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

  return (
    <div className="min-h-screen bg-gray-50">
      <nav className="bg-white shadow-sm border-b border-gray-200">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex justify-between h-16">
            <div className="flex items-center">
              <MessageCircle className="w-8 h-8 text-indigo-600" />
              <span className="ml-2 text-xl font-bold text-gray-900">
                Chat Manager
              </span>
            </div>
            <div className="flex items-center gap-4">
              {user && (
                <>
                  <div className="flex items-center gap-2">
                    <img
                      src={user.user_metadata.avatar_url}
                      alt={user.user_metadata.full_name}
                      className="w-8 h-8 rounded-full"
                    />
                    <span className="text-sm text-gray-700">
                      {user.user_metadata.full_name}
                    </span>
                  </div>
                  <button
                    onClick={handleLogout}
                    disabled={signOut.isPending}
                    className="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:text-gray-900 disabled:opacity-50"
                  >
                    <LogOut className="w-4 h-4" />
                    {signOut.isPending ? 'Đang đăng xuất...' : 'Đăng xuất'}
                  </button>
                </>
              )}
            </div>
          </div>
        </div>
      </nav>

      <main className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div className="mb-8">
          <div className="flex justify-between items-center">
            <div>
              <h1 className="text-3xl font-bold text-gray-900">
                Danh sách Pages
              </h1>
              <p className="mt-1 text-sm text-gray-600">
                Quản lý các Facebook Page của bạn
              </p>
            </div>
            <button
              onClick={() => setShowConnectModal(true)}
              className="flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors"
            >
              <Plus className="w-5 h-5" />
              Kết nối Page mới
            </button>
          </div>
        </div>

        {!pages || pages.length === 0 ? (
          <div className="text-center py-12">
            <MessageCircle className="w-16 h-16 text-gray-400 mx-auto mb-4" />
            <h3 className="text-lg font-medium text-gray-900 mb-2">
              Chưa có Page nào
            </h3>
            <p className="text-gray-600 mb-6">
              Bắt đầu bằng cách kết nối Facebook Page của bạn
            </p>
            <button
              onClick={() => setShowConnectModal(true)}
              className="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors"
            >
              <Plus className="w-5 h-5" />
              Kết nối Page đầu tiên
            </button>
          </div>
        ) : (
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {pages.map((page) => (
              <div
                key={page.id}
                onClick={() => handlePageClick(page.pageId)}
                className="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow cursor-pointer"
              >
                <div className="flex items-start justify-between mb-4">
                  <div className="flex-1">
                    <h3 className="text-lg font-semibold text-gray-900 mb-1">
                      {page.name}
                    </h3>
                    <p className="text-sm text-gray-500">ID: {page.pageId}</p>
                  </div>
                  <div className="flex flex-col gap-2">
                    {page.active && (
                      <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        Active
                      </span>
                    )}
                    {page.installed && (
                      <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        Installed
                      </span>
                    )}
                  </div>
                </div>

                <div className="grid grid-cols-2 gap-4 pt-4 border-t border-gray-100">
                  <div className="flex items-center gap-2">
                    <Users className="w-4 h-4 text-gray-400" />
                    <div>
                      <p className="text-xs text-gray-500">Users</p>
                      <p className="text-sm font-semibold text-gray-900">-</p>
                    </div>
                  </div>
                  <div className="flex items-center gap-2">
                    <TrendingUp className="w-4 h-4 text-gray-400" />
                    <div>
                      <p className="text-xs text-gray-500">Chats</p>
                      <p className="text-sm font-semibold text-gray-900">-</p>
                    </div>
                  </div>
                </div>

                <div className="mt-4 pt-4 border-t border-gray-100">
                  <p className="text-xs text-gray-500">
                    Tạo: {new Date(page.createdAt).toLocaleDateString('vi-VN')}
                  </p>
                </div>
              </div>
            ))}
          </div>
        )}
      </main>

      {showConnectModal && (
        <ConnectPageModal onClose={() => setShowConnectModal(false)} />
      )}
    </div>
  );
}
