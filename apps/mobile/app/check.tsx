import { useState } from 'react';
import { ScrollView, Text } from 'react-native';
import { api, ApiError } from '@/lib/api';
import { Button, ErrorNote, Field } from '@/components/ui';
import { RiderCard } from '@/components/RiderCard';
import type { RiderLookup } from '@/lib/types';
import { colors } from '@/theme';

/** Public plate search. No login needed. Exact match only. */
export default function CheckRider() {
  const [plate, setPlate] = useState('');
  const [result, setResult] = useState<RiderLookup | null>(null);
  const [error, setError] = useState<string | null>(null);
  const [busy, setBusy] = useState(false);

  async function search() {
    setBusy(true);
    setError(null);
    setResult(null);
    try {
      setResult(await api<RiderLookup>(`/public/riders/plate/${encodeURIComponent(plate.trim())}`));
    } catch (e) {
      setError(
        e instanceof ApiError && e.status === 429 ? 'Too many searches. Please wait a moment.' : (e as Error).message,
      );
    } finally {
      setBusy(false);
    }
  }

  return (
    <ScrollView contentContainerStyle={{ padding: 20, gap: 16 }} keyboardShouldPersistTaps="handled">
      <Text style={{ color: colors.muted }}>Type the number plate shown on the bike.</Text>
      <Field label="Number plate" placeholder="UBK 482X" autoCapitalize="characters" value={plate} onChangeText={setPlate} />
      <Button title="Check rider" onPress={search} loading={busy} disabled={plate.trim().length < 3} />
      {error && <ErrorNote message={error} />}
      {result && <RiderCard data={result} />}
    </ScrollView>
  );
}
