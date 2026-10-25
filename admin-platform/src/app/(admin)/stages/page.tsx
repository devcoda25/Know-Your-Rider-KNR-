"use client";

import { EmptyState, ErrorNote, PageHeader, Table } from "@/components/ui";
import { useFetch } from "@/lib/use-fetch";
import type { Paginated, Stage } from "@/lib/types";

export default function StagesPage() {
  const { data, error, loading } = useFetch<Paginated<Stage>>("/stages");

  return (
    <>
      <PageHeader title="Stages" subtitle="All boda boda stages on the platform" />
      {error && <ErrorNote message={error} />}
      {loading && !data ? (
        <p className="text-sm text-muted">Loading…</p>
      ) : data?.data.length ? (
        <Table head={["Stage", "Location", "Status", "Subscription", "Riders"]}>
          {data.data.map((s) => (
            <tr key={s.id}>
              <td className="px-4 py-3 font-medium">{s.name}</td>
              <td className="px-4 py-3">
                {s.division}, {s.district}
              </td>
              <td className="px-4 py-3 capitalize">{s.status}</td>
              <td className="px-4 py-3 capitalize">{s.subscription_status}</td>
              <td className="px-4 py-3">{s.riders_count ?? 0}</td>
            </tr>
          ))}
        </Table>
      ) : (
        <EmptyState title="No stages yet" />
      )}
    </>
  );
}
