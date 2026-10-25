import type { Role } from "./types";

const KEY = "knr_admin_session";

export interface Session {
  token: string;
  userId: string;
  roles: Role[];
  exp: number; // unix seconds
}

function decode(token: string): { sub: string; roles?: Role[]; exp: number } {
  const payload = token.split(".")[1].replace(/-/g, "+").replace(/_/g, "/");
  return JSON.parse(atob(payload));
}

export function saveSession(token: string): Session {
  const claims = decode(token);
  const session: Session = {
    token,
    userId: claims.sub,
    roles: claims.roles ?? [],
    exp: claims.exp,
  };
  localStorage.setItem(KEY, JSON.stringify(session));
  return session;
}

export function loadSession(): Session | null {
  if (typeof window === "undefined") return null;
  try {
    const raw = localStorage.getItem(KEY);
    if (!raw) return null;
    const session = JSON.parse(raw) as Session;
    return session.exp * 1000 > Date.now() ? session : null;
  } catch {
    return null;
  }
}

export function clearSession() {
  localStorage.removeItem(KEY);
}

export const isSuperAdmin = (s: Session | null) => !!s?.roles.some((r) => r.role === "super_admin");
