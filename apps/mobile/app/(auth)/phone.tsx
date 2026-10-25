import { useRouter } from 'expo-router';
import { useState } from 'react';
import { Text, View } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useAuth } from '@/lib/auth';
import { Button, ErrorNote, Field } from '@/components/ui';
import { colors } from '@/theme';

export default function PhoneScreen() {
  const { requestCode } = useAuth();
  const router = useRouter();
  const [phone, setPhone] = useState('+256');
  const [error, setError] = useState<string | null>(null);
  const [busy, setBusy] = useState(false);

  async function submit() {
    setBusy(true);
    setError(null);
    try {
      const { debugCode } = await requestCode(phone);
      router.push({ pathname: '/(auth)/otp', params: { phone, debugCode: debugCode ?? '' } });
    } catch (e) {
      setError((e as Error).message);
    } finally {
      setBusy(false);
    }
  }

  return (
    <SafeAreaView style={{ flex: 1, padding: 24, gap: 16 }}>
      <Text style={{ fontSize: 26, fontWeight: '800', color: colors.text }}>Your phone number</Text>
      <Text style={{ color: colors.muted }}>We will send you a 6-digit code.</Text>
      <View style={{ gap: 16 }}>
        <Field label="Phone number" keyboardType="phone-pad" value={phone} onChangeText={setPhone} />
        <Button title="Send code" onPress={submit} loading={busy} />
        {error && <ErrorNote message={error} />}
      </View>
    </SafeAreaView>
  );
}
