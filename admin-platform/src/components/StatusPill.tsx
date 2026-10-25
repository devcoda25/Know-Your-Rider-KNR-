import type { ComplianceStatus } from "@/lib/types";

// Status is always icon + text + colour, never colour alone.
const STATUS: Record<ComplianceStatus, { label: string; icon: string; className: string }> = {
  verified: { label: "Verified", icon: "✓", className: "bg-ok-soft text-ok" },
  expiring_soon: { label: "Expiring soon", icon: "!", className: "bg-warn-soft text-warn" },
  expired: { label: "Expired", icon: "✕", className: "bg-expired-soft text-expired" },
  incomplete: { label: "Documents missing", icon: "?", className: "bg-info-soft text-info" },
  suspended: { label: "Suspended", icon: "⛔", className: "bg-danger-soft text-danger" },
  stolen: { label: "Stolen bike", icon: "⛔", className: "bg-danger-soft text-danger" },
};

export function StatusPill({ status }: { status: ComplianceStatus }) {
  const s = STATUS[status];
  return (
    <span className={`inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-sm font-semibold ${s.className}`}>
      <span aria-hidden>{s.icon}</span>
      {s.label}
    </span>
  );
}
