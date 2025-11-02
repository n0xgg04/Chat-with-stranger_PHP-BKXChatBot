'use client';

import PageDetailClient from './PageDetailClient';
import { use } from 'react';

export default function PageDetailPage({
  params,
}: {
  params: Promise<{ pageId: string }>;
}) {
  const { pageId } = use(params);
  return <PageDetailClient pageId={pageId} />;
}
