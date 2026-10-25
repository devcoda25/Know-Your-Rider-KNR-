import { ComingSoon } from "@/components/ui";

export default function AuditLogPage() {
  return (
    <ComingSoon
      title="Audit log"
      bullets={[
        "Every QR scan and plate search: who, role, rider, time and location",
        "Filters by officer, organisation, date and rider",
        "Flag suspicious activity and export",
      ]}
    />
  );
}
