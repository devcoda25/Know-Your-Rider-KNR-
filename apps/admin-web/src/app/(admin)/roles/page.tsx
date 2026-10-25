import { ComingSoon } from "@/components/ui";

export default function RolesPage() {
  return (
    <ComingSoon
      title="Roles & permissions"
      bullets={[
        "Matrix of roles (columns) against permissions (rows) with toggles",
        "Field visibility per role (which rider fields each role can see)",
        "Role assignments with scope: stage, parish, division, district",
      ]}
    />
  );
}
