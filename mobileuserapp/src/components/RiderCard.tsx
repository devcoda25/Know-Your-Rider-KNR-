import { Linking, Pressable, StyleSheet, Text, View } from 'react-native';
import { Card, StatusPill } from './ui';
import { colors } from '@/theme';
import type { RiderLookup } from '@/lib/types';

const fmt = (d?: string | null) =>
  d ? new Date(d).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' }) : 'Not recorded';

/** Result of a QR scan or plate search. Staff roles also get phone and masked National ID. */
export function RiderCard({ data }: { data: RiderLookup }) {
  if (!data.registered) {
    return (
      <Card>
        <Text style={styles.title}>Not registered on KNR</Text>
        <Text style={styles.muted}>
          {data.message ?? 'This plate is not registered on KNR. This does not mean the rider is illegal.'}
        </Text>
      </Card>
    );
  }

  if (data.warning) {
    return (
      <View style={styles.warning}>
        {data.status && <StatusPill status={data.status} />}
        <Text style={styles.warningTitle}>Do not ride</Text>
        <Text style={{ color: colors.danger }}>{data.warning}</Text>
        {data.plate ? <Text style={styles.plate}>{data.plate}</Text> : null}
        <Pressable onPress={() => Linking.openURL('tel:999')} style={styles.callPolice}>
          <Text style={{ color: '#fff', fontWeight: '700' }}>Call police (999)</Text>
        </Pressable>
      </View>
    );
  }

  const name = data.full_name ?? data.rider?.full_name ?? '';
  const plate = data.bike?.plate ?? '';
  const contact = data.stage?.contact_phone;

  return (
    <View style={{ gap: 12 }}>
      <Card>
        <Text style={styles.name}>{name}</Text>
        <Text style={styles.plate}>{plate}</Text>
        {data.status && <StatusPill status={data.status} />}
        <Text style={styles.muted}>Check that the person in front of you matches their photo.</Text>
      </Card>

      <Card>
        <Row k="Bike" v={[data.bike?.make, data.bike?.model, data.bike?.color].filter(Boolean).join(' ') || '—'} />
        <Row k="Stage" v={data.stage ? `${data.stage.name}, ${data.stage.division}` : '—'} />
        <Row k="Permit valid until" v={fmt(data.permit_valid_until)} />
        <Row k="Insurance valid until" v={fmt(data.insurance_valid_until)} />
        {data.national_id_masked ? <Row k="National ID" v={data.national_id_masked} /> : null}
        {data.phone ? <Row k="Rider phone" v={data.phone} /> : null}
        {typeof data.open_incidents === 'number' ? <Row k="Open incidents" v={String(data.open_incidents)} /> : null}
      </Card>

      {contact ? (
        <Pressable onPress={() => Linking.openURL(`tel:${contact}`)} style={styles.call}>
          <Text style={{ color: '#fff', fontWeight: '700', fontSize: 16 }}>
            Call stage head{data.stage?.contact_name ? ` (${data.stage.contact_name})` : ''}
          </Text>
        </Pressable>
      ) : null}
    </View>
  );
}

function Row({ k, v }: { k: string; v: string }) {
  return (
    <View style={styles.row}>
      <Text style={styles.muted}>{k}</Text>
      <Text style={{ color: colors.text, flexShrink: 1, textAlign: 'right' }}>{v}</Text>
    </View>
  );
}

const styles = StyleSheet.create({
  title: { fontSize: 18, fontWeight: '700', color: colors.text },
  name: { fontSize: 24, fontWeight: '800', color: colors.text },
  plate: { fontSize: 20, fontWeight: '600', color: colors.text, letterSpacing: 1 },
  muted: { color: colors.muted },
  row: { flexDirection: 'row', justifyContent: 'space-between', gap: 12 },
  warning: { backgroundColor: colors.dangerSoft, borderRadius: 16, padding: 20, gap: 10 },
  warningTitle: { fontSize: 24, fontWeight: '800', color: colors.danger },
  call: { backgroundColor: colors.brand, height: 52, borderRadius: 14, alignItems: 'center', justifyContent: 'center' },
  callPolice: { backgroundColor: colors.danger, height: 52, borderRadius: 14, alignItems: 'center', justifyContent: 'center' },
});
