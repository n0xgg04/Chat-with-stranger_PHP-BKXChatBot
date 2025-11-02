import { useMutation, useQuery } from '@tanstack/react-query';
import { createClient } from '@/utils/supabase/client';
import { useAuthStore } from '@/lib/stores/auth-store';
import { useRouter } from 'next/navigation';

export const useAuth = () => {
  const { user, setUser, clearUser } = useAuthStore();
  const router = useRouter();
  const supabase = createClient();

  return {
    user,
    isAuthenticated: !!user,
  };
};

export const useSignOut = () => {
  const { clearUser } = useAuthStore();
  const router = useRouter();
  const supabase = createClient();

  return useMutation({
    mutationFn: async () => {
      const { error } = await supabase.auth.signOut();
      if (error) throw error;
    },
    onSuccess: () => {
      clearUser();
      router.push('/login');
    },
  });
};

export const useSignInWithGoogle = () => {
  const supabase = createClient();

  return useMutation({
    mutationFn: async () => {
      const { error } = await supabase.auth.signInWithOAuth({
        provider: 'google',
        options: {
          redirectTo: `${window.location.origin}/auth/callback`,
        },
      });
      if (error) throw error;
    },
  });
};

export const useSession = () => {
  const supabase = createClient();

  return useQuery({
    queryKey: ['session'],
    queryFn: async () => {
      const {
        data: { session },
      } = await supabase.auth.getSession();
      return session;
    },
  });
};

