import { useEffect, useState } from 'react';
import { Pressable, ScrollView, Text, View } from 'react-native';
import { api, post } from '@/lib/api';
import { Button, ErrorNote, Field } from '@/components/ui';
import type { Stage } from '@/lib/types';
import { colors } from '@/theme';

/**
 * Single-screen rider registration to prove the flow end to end.
 * TODO: split into the 5-step flow from the design (personal, bike, documents,
 * next of kin, review + consent), add photo capture, then show the QR code.
 */
export default function RegisterRider() {
  const [stages, setStages] = useState<Stage[]>([]);
  const [stageId, setStageId] = useState<string | null>(null);
  const [form, setForm] = useState({
    full_name: '', phone: '+256', national_id: '', plate: '', make: '', model: '',
    permit_expires: '', insurance_expires: '', // YYYY-MM-DD
  });
  const [consent, setConsent] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [done, setDone] = useState<string | null>(null);
  const [busy, setBusy] = useState(false);

  useEffect(() => {
    api<{ data: Stage[] }>('/stages')
      .then((res) => {
        const active = res.data.filter((s) => s.status === 'active');
        setStages(active);
        if (active.length === 1) setStageId(active[0].id);
      })
      .catch((e) => setError((e as Error).message));
  }, []);

  const set = (key: keyof typeof form) => (value: string) => setForm((f) => ({ ...f, [key]: value }));

  async function submit() {
    setBusy(true);
    setError(null);
    try {
      const documents = [
        form.permit_expires && { type: 'permit', expires_at: form.permit_expires },
        form.insurance_expires && { type: 'insurance', expires_at: form.insurance_expires },
      ].filter(Boolean);

      const res = await post<{ qr_url: string }>('/riders', {
        stage_id: stageId,
        full_name: form.full_name,
        phone: form.phone.length > 4 ? form.phone : undefined,
        national_id: form.national_id,
        consent,
        bike: { plate: form.plate, make: form.make, model: form.model || undefined },
        documents,
      });
      setDone(res.qr_url);
    } catch (e) {
      setError((e as Error).message);
    } finally {
      setBusy(false);
    }
  }

  if (done) {
    return (
      <View style={{ padding: 20, gap: 16 }}>
        <Text style={{ fontSize: 22, fontWeight: '800', color: colors.ok }}>Rider registered</Text>
        <Text style={{ color: colors.muted }}>QR code link (the QR image and printable ID card come next):</Text>
        <Text selectable style={{ color: colors.text }}>{done}</Text>
        <Button title="Register another" onPress={() => { setDone(null); setConsent(false); }} />
      </View>
    );
  }

  return (
    <ScrollView contentContainerStyle={{ padding: 20, gap: 14 }} keyboardShouldPersistTaps="handled">
      {stages.length !== 1 && (
        <View style={{ gap: 6 }}>
          <Text style={{ fontWeight: '600', color: colors.text }}>Stage</Text>
          {stages.length === 0 && <Text style={{ color: colors.muted }}>No approved stage yet.</Text>}
          {stages.map((s) => (
            <Pressable
              key={s.id}
              onPress={() => setStageId(s.id)}
              style={{ padding: 14, borderRadius: 12, borderWidth: 1, borderColor: stageId === s.id ? colors.brand : colors.border, backgroundColor: stageId === s.id ? colors.brandSoft : colors.surface }}
            >
              <Text style={{ color: colors.text }}>{s.name}</Text>
            </Pressable>
          ))}
        </View>
      )}

      <Field label="Full name" value={form.full_name} onChangeText={set('full_name')} />
      <Field label="Phone" keyboardType="phone-pad" value={form.phone} onChangeText={set('phone')} />
      <Field label="National ID number" autoCapitalize="characters" value={form.national_id} onChangeText={set('national_id')} />
      <Field label="Number plate" autoCapitalize="characters" value={form.plate} onChangeText={set('plate')} />
      <Field label="Bike make" value={form.make} onChangeText={set('make')} />
      <Field label="Bike model (optional)" value={form.model} onChangeText={set('model')} />
      <Field label="Permit expires (YYYY-MM-DD)" value={form.permit_expires} onChangeText={set('permit_expires')} />
      <Field label="Insurance expires (YYYY-MM-DD)" value={form.insurance_expires} onChangeText={set('insurance_expires')} />

      <Pressable onPress={() => setConsent(!consent)} style={{ flexDirection: 'row', gap: 10, alignItems: 'center' }}>
        <Text style={{ fontSize: 22 }}>{consent ? '☑' : '☐'}</Text>
        <Text style={{ flex: 1, color: colors.text }}>The rider agrees to be registered and to their verified details being shown when scanned.</Text>
      </Pressable>

      {error && <ErrorNote message={error} />}
      <Button
        title="Register rider"
        onPress={submit}
        loading={busy}
        disabled={!stageId || !consent || !form.full_name || !form.national_id || !form.plate || !form.make}
      />
    </ScrollView>
  );
}
