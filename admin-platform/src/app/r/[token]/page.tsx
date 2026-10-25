import { RiderResult } from "@/components/RiderResult";
import { API_URL } from "@/lib/api";
import type { PublicRider } from "@/lib/types";

export const dynamic = "force-dynamic";

/** Public page opened by scanning a rider's QR code. No login, no app install. */
export default async function ScanPage({ params }: { params: Promise<{ token: string }> }) {
  const { token } = await params;

  let data: PublicRider;
  try {
    const res = await fetch(`${API_URL}/public/riders/qr/${encodeURIComponent(token)}`, {
      cache: "no-store",
      headers: { Accept: "application/json" },
    });
    data = res.ok || res.status === 404 ? await res.json() : { registered: false, message: "Could not check this rider. Try again." };
  } catch {
    data = { registered: false, message: "Could not reach KNR. Check your connection and try again." };
  }

  return (
    <main className="mx-auto max-w-md p-4">
      <RiderResult data={data} />
      <p className="mt-6 text-center text-xs text-muted">Know Your Rider: ride with confidence.</p>
    </main>
  );
}
