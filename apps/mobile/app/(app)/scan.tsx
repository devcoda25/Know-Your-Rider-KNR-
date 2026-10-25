import { CameraView, useCameraPermissions } from 'expo-camera';
import { useCallback, useRef, useState } from 'react';
import { ScrollView, StyleSheet, Text, View } from 'react-native';
import { api } from '@/lib/api';
import { Button, ErrorNote } from '@/components/ui';
import { RiderCard } from '@/components/RiderCard';
import type { RiderLookup } from '@/lib/types';
import { colors } from '@/theme';

/** Pulls the token out of ".../r/<token>" (the URL printed inside every rider QR code). */
function tokenFrom(data: string): string | null {
  const match = data.match(/\/r\/([A-Za-z0-9]{20,})\/?$/);
  return match ? match[1] : null;
}

export default function ScanScreen() {
  const [permission, requestPermission] = useCameraPermissions();
  const [result, setResult] = useState<RiderLookup | null>(null);
  const [error, setError] = useState<string | null>(null);
  const [busy, setBusy] = useState(false);
  const locked = useRef(false); // the camera fires many times per second

  const onScan = useCallback(async ({ data }: { data: string }) => {
    if (locked.current) return;
    locked.current = true;
    setBusy(true);
    setError(null);

    const token = tokenFrom(data);
    if (!token) {
      setError('This is not a KNR rider code.');
      setBusy(false);
      return;
    }

    try {
      // Sent with the user's token, so police and stage staff get the full profile.
      setResult(await api<RiderLookup>(`/public/riders/qr/${token}`));
    } catch (e) {
      setError((e as Error).message);
    } finally {
      setBusy(false);
    }
  }, []);

  function reset() {
    setResult(null);
    setError(null);
    locked.current = false;
  }

  if (!permission) return null;

  if (!permission.granted) {
    return (
      <View style={styles.center}>
        <Text style={{ color: colors.muted, textAlign: 'center' }}>KNR needs the camera to scan rider QR codes.</Text>
        <Button title="Allow camera" onPress={requestPermission} />
      </View>
    );
  }

  if (result || error) {
    return (
      <ScrollView contentContainerStyle={{ padding: 20, gap: 16 }}>
        {error && <ErrorNote message={error} />}
        {result && <RiderCard data={result} />}
        <Button title="Scan another" onPress={reset} />
      </ScrollView>
    );
  }

  return (
    <View style={{ flex: 1 }}>
      <CameraView
        style={{ flex: 1 }}
        facing="back"
        barcodeScannerSettings={{ barcodeTypes: ['qr'] }}
        onBarcodeScanned={busy ? undefined : onScan}
      />
      <View style={styles.hint}>
        <Text style={{ color: '#fff', fontWeight: '600' }}>{busy ? 'Checking…' : "Point the camera at the rider's QR code"}</Text>
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  center: { flex: 1, alignItems: 'center', justifyContent: 'center', padding: 24, gap: 16 },
  hint: { position: 'absolute', bottom: 24, alignSelf: 'center', backgroundColor: 'rgba(15,23,42,0.75)', borderRadius: 999, paddingHorizontal: 18, paddingVertical: 10 },
});
