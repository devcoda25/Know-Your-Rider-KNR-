export type ComplianceStatus =
  | 'verified'
  | 'expiring_soon'
  | 'expired'
  | 'incomplete'
  | 'suspended'
  | 'stolen';

export interface RoleClaim {
  role: string;
  scope_type: string | null;
  scope_id: string | null;
}

export interface Stage {
  id: string;
  name: string;
  division: string;
  district: string;
  status: 'pending' | 'active' | 'suspended';
}

/** Response of the public QR/plate lookup. Staff roles get extra fields (see RiderFullResource). */
export interface RiderLookup {
  registered: boolean;
  status?: ComplianceStatus;
  message?: string;
  warning?: string;
  plate?: string | null;
  full_name?: string;
  phone?: string | null;
  national_id_masked?: string;
  rider?: { full_name: string; photo_url: string | null };
  photo_url?: string | null;
  bike?: { plate: string | null; make: string | null; model: string | null; color: string | null };
  stage?: {
    name: string;
    division: string;
    district: string;
    contact_name: string | null;
    contact_phone: string | null;
  };
  permit_valid_until?: string | null;
  insurance_valid_until?: string | null;
  open_incidents?: number;
}

export interface RiderListItem {
  id: string;
  full_name: string;
  status: ComplianceStatus;
  bike: { plate: string | null };
  stage: { name: string };
}
