import { useLocalSearchParams, useRouter } from 'expo-router';
import { useState } from 'react';
import { Text, View } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useAuth } from '@/lib/auth';
import { Button, ErrorNote, Field } from '@/components/ui';
import { colors } from '@/theme';

export default function OtpScreen() {
  const { phone, debugCode } = useLocalSearchParams<{ phone: string; debugCode?: string }>();
  const { verifyCode } = useAuth();
  const router = useRouter();
  const [code, setCode] = useState('');
  const [error, setError] = useState<string | null>(null);
  const [busy, setBusy] = useState(false);

  async function submit() {
    setBusy(true);
    setError(null);
    try {
      await verifyCode(phone, code);
      router.replace('/(app)');
    } catch (e) {
      setError((e as Error).message);
    } finally {
      setBusy(false);
    }
  }

  return (
    <SafeAreaView style={{ flex: 1, padding: 24, gap: 16 }}>
      <Text style={{ fontSize: 26, fontWeight: '800', color: colors.text }}>Enter the code</Text>
      <Text style={{ color: colors.muted }}>Sent to {phone}</Text>
      {debugCode ? <Text style={{ color: colors.muted }}>Dev code: {debugCode}</Text> : null}
      <View style={{ gap: 16 }}>
        <Field
          label="6-digit code"
          keyboardType="number-pad"
          maxLength={6}
          value={code}
          onChangeText={(t) => setCode(t.replace(/\D/g, ''))}
        />
        <Button title="Continue" onPress={submit} loading={busy} disabled={code.length !== 6} />
        {error && <ErrorNote message={error} />}
      </View>
    </SafeAreaView>
  );
}
