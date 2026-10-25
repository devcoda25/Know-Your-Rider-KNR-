"use client";

import Link from "next/link";
import { usePathname } from "next/navigation";

const NAV = [
  { href: "/dashboard", label: "Dashboard" },
  { href: "/approvals", label: "Approvals" },
  { href: "/stages", label: "Stages" },
  { href: "/riders", label: "Riders" },
  { href: "/authorities", label: "Authorities" },
  { href: "/roles", label: "Roles & permissions" },
  { href: "/subscriptions", label: "Subscriptions" },
  { href: "/audit-log", label: "Audit log" },
];

export function Sidebar({ onSignOut }: { onSignOut: () => void }) {
  const pathname = usePathname();

  return (
    <aside className="flex w-60 shrink-0 flex-col border-r border-line bg-surface p-4">
      <p className="px-3 pb-4 text-lg font-bold text-brand">KNR Admin</p>
      <nav className="flex flex-1 flex-col gap-1">
        {NAV.map((item) => {
          const active = pathname === item.href || pathname.startsWith(`${item.href}/`);
          return (
            <Link
              key={item.href}
              href={item.href}
              className={`rounded-xl px-3 py-2.5 text-sm font-medium ${
                active ? "bg-brand-soft text-brand-dark" : "text-muted hover:bg-bg"
              }`}
            >
              {item.label}
            </Link>
          );
        })}
      </nav>
      <button onClick={onSignOut} className="rounded-xl px-3 py-2.5 text-left text-sm text-muted hover:bg-bg">
        Sign out
      </button>
    </aside>
  );
}
