"use client";

import { useState } from "react";
import { Button, EmptyState, ErrorNote, PageHeader, Table } from "@/components/ui";
import { post } from "@/lib/api";
import { useFetch } from "@/lib/use-fetch";
import type { Paginated, Stage } from "@/lib/types";

export default function ApprovalsPage() {
  const { data, error, loading, reload } = useFetch<Paginated<Stage>>("/stages?status=pending");
  const [actionError, setActionError] = useState<string | null>(null);

  async function act(id: string, action: "approve" | "suspend") {
    setActionError(null);
    try {
      await post(`/stages/${id}/${action}`);
      await reload();
    } catch (e) {
      setActionError(e instanceof Error ? e.message : "Action failed");
    }
  }

  return (
    <>
      <PageHeader title="Approvals" subtitle="New stages waiting for review. Authority officers and organisations come next." />
      {error && <ErrorNote message={error} />}
      {actionError && <ErrorNote message={actionError} />}
      {loading && !data ? (
        <p className="text-sm text-muted">Loading…</p>
      ) : data?.data.length ? (
        <Table head={["Stage", "Location", "Contact", ""]}>
          {data.data.map((s) => (
            <tr key={s.id}>
              <td className="px-4 py-3 font-medium">{s.name}</td>
              <td className="px-4 py-3">
                {s.division}, {s.district}
              </td>
              <td className="px-4 py-3">{s.contact_name ?? "—"}</td>
              <td className="flex justify-end gap-2 px-4 py-3">
                <Button onClick={() => act(s.id, "approve")}>Approve</Button>
                <Button variant="ghost" onClick={() => act(s.id, "suspend")}>
                  Reject
                </Button>
              </td>
            </tr>
          ))}
        </Table>
      ) : (
        <EmptyState title="Nothing to approve" hint="New stage applications will show up here." />
      )}
    </>
  );
}
