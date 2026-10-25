/**
 * KNR design tokens.
 * TODO: replace the placeholder brand colours with the palette from the Behance
 * reference design. Status colours have fixed meaning across every KNR app.
 */
export const colors = {
  brand: '#1d4ed8',
  brandDark: '#1e3a8a',
  brandSoft: '#dbeafe',
  accent: '#f59e0b',

  bg: '#f6f7fb',
  surface: '#ffffff',
  text: '#0f172a',
  muted: '#64748b',
  border: '#e2e8f0',

  ok: '#15803d',
  okSoft: '#dcfce7',
  warn: '#b45309',
  warnSoft: '#fef3c7',
  expired: '#c2410c',
  expiredSoft: '#ffedd5',
  danger: '#b91c1c',
  dangerSoft: '#fee2e2',
  info: '#475569',
  infoSoft: '#e2e8f0',
} as const;

export const radius = { card: 16, button: 14, pill: 999 } as const;
export const space = { xs: 4, sm: 8, md: 12, lg: 16, xl: 24 } as const;
