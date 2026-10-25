"use client";

import { useState } from "react";
import { RiderResult } from "@/components/RiderResult";
import { Button } from "@/components/ui";
import { API_URL } from "@/lib/api";
import type { PublicRider } from "@/lib/types";

/** Public plate search for passengers. Exact match only. */
export default function CheckPage() {
  const [plate, setPlate] = useState("");
  const [result, setResult] = useState<PublicRider | null>(null);
  const [busy, setBusy] = useState(false);

  async function search() {
    setBusy(true);
    try {
      const res = await fetch(`${API_URL}/public/riders/plate/${encodeURIComponent(plate.trim())}`, {
        headers: { Accept: "application/json" },
      });
      if (res.status === 429) {
        setResult({ registered: false, message: "Too many searches. Please wait a moment." });
      } else {
        setResult(await res.json());
      }
    } catch {
      setResult({ registered: false, message: "Could not reach KNR. Check your connection." });
    } finally {
      setBusy(false);
    }
  }

  return (
    <main className="mx-auto max-w-md space-y-4 p-4">
      <h1 className="text-2xl font-bold">Check a rider</h1>
      <p className="text-sm text-muted">Type the number plate, or scan the QR code on the rider&apos;s ID.</p>
      <input
        className="h-12 w-full rounded-xl border border-line px-4 text-lg uppercase"
        placeholder="UBK 482X"
        value={plate}
        onChange={(e) => setPlate(e.target.value)}
      />
      <Button className="w-full" disabled={busy || plate.trim().length < 3} onClick={search}>
        {busy ? "Checking…" : "Check rider"}
      </Button>
      {result && <RiderResult data={result} />}
    </main>
  );
}
