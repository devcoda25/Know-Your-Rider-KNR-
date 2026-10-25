"use client";

import { useRouter } from "next/navigation";
import { useEffect, useState } from "react";
import { Sidebar } from "@/components/Sidebar";
import { clearSession, isSuperAdmin, loadSession } from "@/lib/session";

/** Client-side guard: only a signed-in super admin may see these screens. The API enforces access too. */
export default function AdminLayout({ children }: { children: React.ReactNode }) {
  const router = useRouter();
  const [ready, setReady] = useState(false);

  useEffect(() => {
    if (!isSuperAdmin(loadSession())) {
      router.replace("/login");
      return;
    }
    setReady(true);
  }, [router]);

  if (!ready) return null;

  return (
    <div className="flex min-h-screen">
      <Sidebar
        onSignOut={() => {
          clearSession();
          router.replace("/login");
        }}
      />
      <main className="flex-1 overflow-x-auto p-8">{children}</main>
    </div>
  );
}
