"use client";

import { StatusPill } from "@/components/StatusPill";
import { EmptyState, ErrorNote, PageHeader, Table } from "@/components/ui";
import { useFetch } from "@/lib/use-fetch";
import type { Paginated, Rider } from "@/lib/types";

export default function RidersPage() {
  const { data, error, loading } = useFetch<Paginated<Rider>>("/riders");

  return (
    <>
      <PageHeader title="Riders" subtitle="All registered riders. National IDs are masked." />
      {error && <ErrorNote message={error} />}
      {loading && !data ? (
        <p className="text-sm text-muted">Loading…</p>
      ) : data?.data.length ? (
        <Table head={["Rider", "Plate", "Stage", "National ID", "Status"]}>
          {data.data.map((r) => (
            <tr key={r.id}>
              <td className="px-4 py-3 font-medium">{r.full_name}</td>
              <td className="px-4 py-3">{r.bike.plate ?? "—"}</td>
              <td className="px-4 py-3">{r.stage.name}</td>
              <td className="px-4 py-3 font-mono text-xs">{r.national_id_masked}</td>
              <td className="px-4 py-3">
                <StatusPill status={r.status} />
              </td>
            </tr>
          ))}
        </Table>
      ) : (
        <EmptyState title="No riders yet" />
      )}
    </>
  );
}
