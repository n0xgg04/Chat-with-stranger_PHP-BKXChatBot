'use client';

import { AuthProvider } from '@/lib/providers/auth-provider';

export default function ProtectedLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return <AuthProvider>{children}</AuthProvider>;
}

