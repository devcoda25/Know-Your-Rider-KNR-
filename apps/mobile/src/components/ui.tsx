import { ReactNode } from 'react';
import { ActivityIndicator, Pressable, StyleSheet, Text, TextInput, TextInputProps, View } from 'react-native';
import { colors, radius, space } from '@/theme';
import type { ComplianceStatus } from '@/lib/types';

export function Button({
  title,
  onPress,
  variant = 'primary',
  loading,
  disabled,
}: {
  title: string;
  onPress: () => void;
  variant?: 'primary' | 'ghost' | 'danger';
  loading?: boolean;
  disabled?: boolean;
}) {
  const bg = variant === 'primary' ? colors.brand : variant === 'danger' ? colors.danger : colors.surface;
  const fg = variant === 'ghost' ? colors.text : '#fff';
  return (
    <Pressable
      accessibilityRole="button"
      onPress={onPress}
      disabled={disabled || loading}
      style={({ pressed }) => [
        styles.button,
        { backgroundColor: bg, opacity: disabled ? 0.5 : pressed ? 0.85 : 1 },
        variant === 'ghost' && { borderWidth: 1, borderColor: colors.border },
      ]}
    >
      {loading ? <ActivityIndicator color={fg} /> : <Text style={[styles.buttonText, { color: fg }]}>{title}</Text>}
    </Pressable>
  );
}

export function Field({ label, ...props }: TextInputProps & { label: string }) {
  return (
    <View style={{ gap: 6 }}>
      <Text style={styles.label}>{label}</Text>
      <TextInput placeholderTextColor={colors.muted} style={styles.input} {...props} />
    </View>
  );
}

export function Card({ children }: { children: ReactNode }) {
  return <View style={styles.card}>{children}</View>;
}

export function ErrorNote({ message }: { message: string }) {
  return (
    <View style={styles.error}>
      <Text style={{ color: colors.danger }}>{message}</Text>
    </View>
  );
}

// Status is always icon + text + colour, never colour alone.
const STATUS: Record<ComplianceStatus, { label: string; icon: string; fg: string; bg: string }> = {
  verified: { label: 'Verified', icon: '✓', fg: colors.ok, bg: colors.okSoft },
  expiring_soon: { label: 'Expiring soon', icon: '!', fg: colors.warn, bg: colors.warnSoft },
  expired: { label: 'Expired', icon: '✕', fg: colors.expired, bg: colors.expiredSoft },
  incomplete: { label: 'Documents missing', icon: '?', fg: colors.info, bg: colors.infoSoft },
  suspended: { label: 'Suspended', icon: '⛔', fg: colors.danger, bg: colors.dangerSoft },
  stolen: { label: 'Stolen bike', icon: '⛔', fg: colors.danger, bg: colors.dangerSoft },
};

export function StatusPill({ status }: { status: ComplianceStatus }) {
  const s = STATUS[status];
  return (
    <View style={[styles.pill, { backgroundColor: s.bg }]}>
      <Text style={{ color: s.fg, fontWeight: '700' }}>
        {s.icon} {s.label}
      </Text>
    </View>
  );
}

const styles = StyleSheet.create({
  button: {
    height: 52,
    borderRadius: radius.button,
    alignItems: 'center',
    justifyContent: 'center',
    paddingHorizontal: space.xl,
  },
  buttonText: { fontSize: 16, fontWeight: '700' },
  label: { fontSize: 14, fontWeight: '600', color: colors.text },
  input: {
    height: 52,
    borderWidth: 1,
    borderColor: colors.border,
    borderRadius: radius.button,
    paddingHorizontal: space.lg,
    fontSize: 16,
    backgroundColor: colors.surface,
    color: colors.text,
  },
  card: {
    backgroundColor: colors.surface,
    borderRadius: radius.card,
    padding: space.lg,
    borderWidth: 1,
    borderColor: colors.border,
    gap: space.sm,
  },
  error: { backgroundColor: colors.dangerSoft, borderRadius: radius.button, padding: space.md },
  pill: { alignSelf: 'flex-start', borderRadius: radius.pill, paddingHorizontal: 14, paddingVertical: 6 },
});
