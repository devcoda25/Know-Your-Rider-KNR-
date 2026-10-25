import { ComingSoon } from "@/components/ui";

export default function SubscriptionsPage() {
  return (
    <ComingSoon
      title="Subscriptions"
      bullets={[
        "Plans editor in UGX (free tier, per-stage monthly or yearly, per-rider fee)",
        "Payments table: MTN Mobile Money and Airtel Money, status and failures",
        "Stages with payment due",
      ]}
    />
  );
}
