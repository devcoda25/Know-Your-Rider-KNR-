import { StatusPill } from "@/components/StatusPill";
import type { PublicRider } from "@/lib/types";

const fmt = (d?: string | null) =>
  d ? new Date(d).toLocaleDateString("en-GB", { day: "numeric", month: "short", year: "numeric" }) : "Not recorded";

/** Passenger-facing result of a QR scan or plate search (the public view). */
export function RiderResult({ data }: { data: PublicRider }) {
  if (!data.registered) {
    return (
      <div className="rounded-2xl bg-info-soft p-6 text-center">
        <p className="text-lg font-semibold">Not registered on KNR</p>
        <p className="mt-2 text-sm text-muted">
          {data.message ?? "This plate is not registered on KNR. This does not mean the rider is illegal."}
        </p>
      </div>
    );
  }

  if (data.warning) {
    return (
      <div className="rounded-2xl bg-danger-soft p-6 text-center text-danger">
        <p className="mb-3">{data.status && <StatusPill status={data.status} />}</p>
        <p className="text-xl font-bold">Do not ride</p>
        <p className="mt-2 text-sm">{data.warning}</p>
        {data.plate && <p className="mt-3 font-mono text-lg">{data.plate}</p>}
        <a href="tel:999" className="mt-5 inline-flex h-12 items-center rounded-xl bg-danger px-6 font-semibold text-white">
          Call police (999)
        </a>
      </div>
    );
  }

  return (
    <div className="space-y-4">
      <div className="rounded-2xl border border-line bg-surface p-6 text-center shadow-sm">
        {data.rider?.photo_url ? (
          // eslint-disable-next-line @next/next/no-img-element
          <img src={data.rider.photo_url} alt="" className="mx-auto h-32 w-32 rounded-full object-cover" />
        ) : (
          <div className="mx-auto flex h-32 w-32 items-center justify-center rounded-full bg-brand-soft text-4xl font-bold text-brand">
            {data.rider?.full_name?.[0] ?? "?"}
          </div>
        )}
        <h1 className="mt-4 text-2xl font-bold">{data.rider?.full_name}</h1>
        <p className="mt-1 font-mono text-lg">{data.bike?.plate}</p>
        <div className="mt-3">{data.status && <StatusPill status={data.status} />}</div>
        <p className="mt-4 text-sm text-muted">Check that the person in front of you matches this photo.</p>
      </div>

      <div className="rounded-2xl border border-line bg-surface p-5 text-sm shadow-sm">
        <dl className="grid grid-cols-2 gap-y-3">
          <dt className="text-muted">Bike</dt>
          <dd>{[data.bike?.make, data.bike?.model, data.bike?.color].filter(Boolean).join(" ") || "—"}</dd>
          <dt className="text-muted">Stage</dt>
          <dd>
            {data.stage?.name}, {data.stage?.division}
          </dd>
          <dt className="text-muted">Permit valid until</dt>
          <dd>{fmt(data.permit_valid_until)}</dd>
          <dt className="text-muted">Insurance valid until</dt>
          <dd>{fmt(data.insurance_valid_until)}</dd>
        </dl>
      </div>

      {data.stage?.contact_phone && (
        <a
          href={`tel:${data.stage.contact_phone}`}
          className="flex h-12 items-center justify-center rounded-xl bg-brand font-semibold text-white"
        >
          Call stage head{data.stage.contact_name ? ` (${data.stage.contact_name})` : ""}
        </a>
      )}
    </div>
  );
}
