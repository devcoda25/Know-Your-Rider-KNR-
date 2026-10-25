import { createContext, useCallback, useContext, useEffect, useMemo, useState } from 'react';
import { post, tokenStore } from './api';
import type { RoleClaim } from './types';

interface Claims {
  sub: string;
  exp: number;
  roles?: RoleClaim[];
}

/** Decodes the JWT payload (not verified here; the gateway verifies every request). */
function decode(token: string): Claims | null {
  try {
    const payload = token.split('.')[1].replace(/-/g, '+').replace(/_/g, '/');
    const json = decodeURIComponent(
      atob(payload)
        .split('')
        .map((c) => '%' + c.charCodeAt(0).toString(16).padStart(2, '0'))
        .join(''),
    );
    return JSON.parse(json) as Claims;
  } catch {
    return null;
  }
}

interface AuthState {
  loading: boolean;
  signedIn: boolean;
  userId: string | null;
  roles: RoleClaim[];
  hasRole: (...keys: string[]) => boolean;
  requestCode: (phone: string) => Promise<{ debugCode?: string }>;
  verifyCode: (phone: string, code: string) => Promise<void>;
  signOut: () => Promise<void>;
}

const AuthContext = createContext<AuthState | null>(null);

export function AuthProvider({ children }: { children: React.ReactNode }) {
  const [loading, setLoading] = useState(true);
  const [claims, setClaims] = useState<Claims | null>(null);

  const applyToken = useCallback((token: string | null) => {
    const c = token ? decode(token) : null;
    setClaims(c && c.exp * 1000 > Date.now() ? c : null);
  }, []);

  useEffect(() => {
    tokenStore
      .get()
      .then(applyToken)
      .finally(() => setLoading(false));
  }, [applyToken]);

  const value = useMemo<AuthState>(
    () => ({
      loading,
      signedIn: !!claims,
      userId: claims?.sub ?? null,
      roles: claims?.roles ?? [],
      hasRole: (...keys) => (claims?.roles ?? []).some((r) => keys.includes(r.role)),
      requestCode: async (phone) => {
        const res = await post<{ debug_code?: string }>('/auth/otp/request', { phone });
        return { debugCode: res.debug_code };
      },
      verifyCode: async (phone, code) => {
        const res = await post<{ token: string }>('/auth/otp/verify', { phone, code });
        await tokenStore.set(res.token);
        applyToken(res.token);
      },
      signOut: async () => {
        await tokenStore.clear();
        setClaims(null);
      },
    }),
    [loading, claims, applyToken],
  );

  return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>;
}

export function useAuth(): AuthState {
  const ctx = useContext(AuthContext);
  if (!ctx) throw new Error('useAuth must be used inside AuthProvider');
  return ctx;
}
