export type ComplianceStatus =
  | "verified"
  | "expiring_soon"
  | "expired"
  | "incomplete"
  | "suspended"
  | "stolen";

export interface Role {
  role: string;
  scope_type: string | null;
  scope_id: string | null;
}

/** Laravel paginator JSON (raw) or an API resource collection (wrapped). */
export interface Paginated<T> {
  data: T[];
  total?: number;
  meta?: { total: number };
}

export const totalOf = <T,>(page: Paginated<T> | null | undefined): number =>
  page?.meta?.total ?? page?.total ?? page?.data.length ?? 0;

export interface Stage {
  id: string;
  name: string;
  district: string;
  division: string;
  parish: string | null;
  status: "pending" | "active" | "suspended";
  subscription_status: string;
  contact_name: string | null;
  contact_phone: string | null;
  riders_count?: number;
}

export interface Rider {
  id: string;
  full_name: string;
  phone: string | null;
  national_id_masked: string;
  status: ComplianceStatus;
  bike: { plate: string | null; make: string | null };
  stage: { id: string; name: string };
  permit_valid_until: string | null;
  insurance_valid_until: string | null;
}

/** Response of GET /public/riders/qr/{token} and /plate/{plate} for passengers. */
export interface PublicRider {
  registered: boolean;
  status?: ComplianceStatus;
  message?: string;
  warning?: string;
  plate?: string | null;
  rider?: { full_name: string; photo_url: string | null };
  bike?: {
    plate: string | null;
    make: string | null;
    model: string | null;
    color: string | null;
    photo_url: string | null;
  };
  stage?: {
    name: string;
    district: string;
    division: string;
    parish: string | null;
    contact_name: string | null;
    contact_phone: string | null;
  };
  permit_valid_until?: string | null;
  insurance_valid_until?: string | null;
}
