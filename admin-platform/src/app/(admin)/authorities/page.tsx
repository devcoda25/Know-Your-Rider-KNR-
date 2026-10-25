import { ComingSoon } from "@/components/ui";

export default function AuthoritiesPage() {
  return (
    <ComingSoon
      title="Authorities"
      bullets={[
        "Organisations: police, local councils, KCCA / municipal, insurers, URA, licensing body",
        "Officer applications with ID photo, and approve / reject",
        "Suspend or reactivate officers and whole organisations",
        "Organisation admins who approve their own officers",
      ]}
    />
  );
}
