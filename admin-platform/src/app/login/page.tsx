"use client";

import { useRouter } from "next/navigation";
import { useState } from "react";
import { Button, ErrorNote } from "@/components/ui";
import { post } from "@/lib/api";
import { isSuperAdmin, saveSession } from "@/lib/session";

type Step = "phone" | "code";

export default function LoginPage() {
  const router = useRouter();
  const [step, setStep] = useState<Step>("phone");
  const [phone, setPhone] = useState("+256");
  const [code, setCode] = useState("");
  const [debugCode, setDebugCode] = useState<string | null>(null);
  const [error, setError] = useState<string | null>(null);
  const [busy, setBusy] = useState(false);

  async function run(fn: () => Promise<void>) {
    setBusy(true);
    setError(null);
    try {
      await fn();
    } catch (e) {
      setError(e instanceof Error ? e.message : "Something went wrong");
    } finally {
      setBusy(false);
    }
  }

  const requestCode = () =>
    run(async () => {
      const res = await post<{ debug_code?: string }>("/auth/otp/request", { phone });
      setDebugCode(res.debug_code ?? null); // only present when OTP_DEBUG_RETURN=true
      setStep("code");
    });

  const verify = () =>
    run(async () => {
      const res = await post<{ token: string }>("/auth/otp/verify", { phone, code });
      const session = saveSession(res.token);
      if (!isSuperAdmin(session)) {
        localStorage.clear();
        throw new Error("This account is not a platform admin.");
      }
      router.replace("/dashboard");
    });

  return (
    <main className="flex min-h-screen items-center justify-center p-4">
      <div className="w-full max-w-sm rounded-3xl border border-line bg-surface p-8 shadow-sm">
        <h1 className="text-2xl font-bold">Know Your Rider</h1>
        <p className="mt-1 text-sm text-muted">Platform admin sign in</p>

        <div className="mt-6 space-y-4">
          {step === "phone" ? (
            <>
              <label className="block text-sm font-medium">
                Phone number
                <input
                  className="mt-1 h-12 w-full rounded-xl border border-line px-4 text-base"
                  inputMode="tel"
                  value={phone}
                  onChange={(e) => setPhone(e.target.value)}
                />
              </label>
              <Button className="w-full" disabled={busy} onClick={requestCode}>
                Send code
              </Button>
            </>
          ) : (
            <>
              <label className="block text-sm font-medium">
                6-digit code sent to {phone}
                <input
                  className="mt-1 h-12 w-full rounded-xl border border-line px-4 text-center text-xl tracking-[0.5em]"
                  inputMode="numeric"
                  maxLength={6}
                  value={code}
                  onChange={(e) => setCode(e.target.value.replace(/\D/g, ""))}
                />
              </label>
              {debugCode && <p className="text-xs text-muted">Dev code: {debugCode}</p>}
              <Button className="w-full" disabled={busy || code.length !== 6} onClick={verify}>
                Sign in
              </Button>
              <Button variant="ghost" className="w-full" onClick={() => setStep("phone")}>
                Use a different number
              </Button>
            </>
          )}
          {error && <ErrorNote message={error} />}
        </div>
      </div>
    </main>
  );
}
