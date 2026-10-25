"use client";

import { PageHeader, StatCard } from "@/components/ui";
import { useFetch } from "@/lib/use-fetch";
import { type Paginated, type Rider, type Stage, totalOf } from "@/lib/types";

export default function DashboardPage() {
  const stages = useFetch<Paginated<Stage>>("/stages");
  const pending = useFetch<Paginated<Stage>>("/stages?status=pending");
  const riders = useFetch<Paginated<Rider>>("/riders");

  const show = (d: Paginated<unknown> | null) => (d ? totalOf(d) : "…");

  return (
    <>
      <PageHeader title="Dashboard" subtitle="Platform overview" />
      <div className="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <StatCard label="Stages" value={show(stages.data)} />
        <StatCard label="Pending approvals" value={show(pending.data)} hint="Stages waiting for review" />
        <StatCard label="Riders" value={show(riders.data)} />
        <StatCard label="Monthly revenue" value="UGX —" hint="Needs the subscriptions service" />
      </div>
    </>
  );
}
