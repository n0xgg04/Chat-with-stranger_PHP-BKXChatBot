'use client';

import { useState } from 'react';
import { X, Check, Copy } from 'lucide-react';
import { useCreatePage, useInstallPage } from '@/lib/hooks/use-pages';

interface ConnectPageModalProps {
  onClose: () => void;
}

export default function ConnectPageModal({ onClose }: ConnectPageModalProps) {
  const [step, setStep] = useState<'input' | 'success'>('input');
  const [accessToken, setAccessToken] = useState('');
  const [pageId, setPageId] = useState('');
  const [pageName, setPageName] = useState('');
  const [webhookUrl, setWebhookUrl] = useState('');
  const [copied, setCopied] = useState(false);

  const createPage = useCreatePage();
  const installPage = useInstallPage();

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();

    try {
      await createPage.mutateAsync({
        pageId: pageId,
        name: pageName || 'My Page',
        accessToken: accessToken,
      });

      await installPage.mutateAsync(pageId);

      const apiUrl =
        process.env.NEXT_PUBLIC_API_URL || 'http://localhost:3000';
      setWebhookUrl(`${apiUrl}/webhook?pageId=${pageId}`);
      setStep('success');
    } catch (error: any) {
      console.error('Error connecting page:', error);
    }
  };

  const copyWebhookUrl = () => {
    navigator.clipboard.writeText(webhookUrl);
    setCopied(true);
    setTimeout(() => setCopied(false), 2000);
  };

  const handleFinish = () => {
    onClose();
  };

  return (
    <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div className="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div className="p-6 border-b border-gray-200">
          <div className="flex items-center justify-between">
            <h2 className="text-2xl font-bold text-gray-900">
              {step === 'input' ? 'Kết nối Facebook Page' : 'Hoàn tất!'}
            </h2>
            <button
              onClick={onClose}
              className="text-gray-400 hover:text-gray-600"
            >
              <X className="w-6 h-6" />
            </button>
          </div>
        </div>

        {step === 'input' ? (
          <form onSubmit={handleSubmit} className="p-6">
            <div className="space-y-6">
              <div className="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h3 className="font-semibold text-blue-900 mb-2">
                  Hướng dẫn lấy Access Token
                </h3>
                <ol className="list-decimal list-inside space-y-1 text-sm text-blue-800">
                  <li>Truy cập Facebook Developers Console</li>
                  <li>Chọn App của bạn → Tools → Graph API Explorer</li>
                  <li>Chọn Page và quyền cần thiết</li>
                  <li>Generate Access Token và copy vào đây</li>
                </ol>
              </div>

              {createPage.isError && (
                <div className="bg-red-50 border border-red-200 rounded-lg p-4">
                  <p className="text-sm text-red-800">
                    {createPage.error instanceof Error
                      ? createPage.error.message
                      : 'Có lỗi xảy ra'}
                  </p>
                </div>
              )}

              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  Page ID <span className="text-red-500">*</span>
                </label>
                <input
                  type="text"
                  value={pageId}
                  onChange={(e) => setPageId(e.target.value)}
                  placeholder="Ví dụ: PAGE001"
                  required
                  className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                />
                <p className="mt-1 text-xs text-gray-500">
                  ID duy nhất để phân biệt page (tự đặt hoặc dùng Page ID từ
                  Facebook)
                </p>
              </div>

              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  Tên Page
                </label>
                <input
                  type="text"
                  value={pageName}
                  onChange={(e) => setPageName(e.target.value)}
                  placeholder="Ví dụ: My Chatbot Page"
                  className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                />
              </div>

              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  Page Access Token <span className="text-red-500">*</span>
                </label>
                <textarea
                  value={accessToken}
                  onChange={(e) => setAccessToken(e.target.value)}
                  placeholder="EAA..."
                  required
                  rows={4}
                  className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent font-mono text-sm"
                />
                <p className="mt-1 text-xs text-gray-500">
                  Access Token của Facebook Page
                </p>
              </div>
            </div>

            <div className="mt-6 flex gap-3">
              <button
                type="button"
                onClick={onClose}
                disabled={createPage.isPending || installPage.isPending}
                className="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors disabled:opacity-50"
              >
                Hủy
              </button>
              <button
                type="submit"
                disabled={
                  createPage.isPending ||
                  installPage.isPending ||
                  !accessToken ||
                  !pageId
                }
                className="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
              >
                {createPage.isPending || installPage.isPending
                  ? 'Đang kết nối...'
                  : 'Kết nối Page'}
              </button>
            </div>
          </form>
        ) : (
          <div className="p-6">
            <div className="text-center mb-6">
              <div className="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-4">
                <Check className="w-8 h-8 text-green-600" />
              </div>
              <h3 className="text-xl font-semibold text-gray-900 mb-2">
                Kết nối thành công!
              </h3>
              <p className="text-gray-600">
                Page của bạn đã được kết nối với hệ thống
              </p>
            </div>

            <div className="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
              <h4 className="font-semibold text-yellow-900 mb-2">
                Bước tiếp theo: Cấu hình Webhook
              </h4>
              <p className="text-sm text-yellow-800 mb-4">
                Để chatbot hoạt động, bạn cần cấu hình webhook trên Facebook
                Developers Console:
              </p>
              <ol className="list-decimal list-inside space-y-2 text-sm text-yellow-800 mb-4">
                <li>Truy cập Facebook Developers Console</li>
                <li>Chọn App → Messenger → Settings</li>
                <li>Trong phần Webhooks, nhấn "Add Callback URL"</li>
                <li>Paste URL bên dưới vào Callback URL</li>
                <li>
                  Verify Token:{' '}
                  <code className="bg-yellow-100 px-1 py-0.5 rounded">
                    n0xgg04_chatb0t
                  </code>
                </li>
                <li>
                  Subscribe to: messages, messaging_postbacks, messaging_reads
                </li>
              </ol>

              <div className="bg-white rounded border border-yellow-300 p-3">
                <label className="block text-xs font-medium text-gray-700 mb-1">
                  Webhook URL
                </label>
                <div className="flex gap-2">
                  <input
                    type="text"
                    value={webhookUrl}
                    readOnly
                    className="flex-1 px-3 py-2 text-sm bg-gray-50 border border-gray-300 rounded font-mono"
                  />
                  <button
                    onClick={copyWebhookUrl}
                    className="px-3 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-700 transition-colors"
                  >
                    {copied ? (
                      <Check className="w-4 h-4" />
                    ) : (
                      <Copy className="w-4 h-4" />
                    )}
                  </button>
                </div>
              </div>
            </div>

            <button
              onClick={handleFinish}
              className="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors"
            >
              Hoàn tất
            </button>
          </div>
        )}
      </div>
    </div>
  );
}
