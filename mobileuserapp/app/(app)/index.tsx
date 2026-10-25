import { useRouter } from 'expo-router';
import { ScrollView, Text } from 'react-native';
import { useAuth } from '@/lib/auth';
import { Button, Card } from '@/components/ui';
import { colors } from '@/theme';

export default function Home() {
  const { roles, hasRole } = useAuth();
  const router = useRouter();
  const canRegister = hasRole('super_admin', 'stage_head', 'stage_official');

  return (
    <ScrollView contentContainerStyle={{ padding: 20, gap: 16 }}>
      <Card>
        <Text style={{ fontSize: 18, fontWeight: '700', color: colors.text }}>Welcome</Text>
        <Text style={{ color: colors.muted }}>
          {roles.length ? roles.map((r) => r.role.replace(/_/g, ' ')).join(', ') : 'Your account has no role yet. An admin needs to assign one.'}
        </Text>
      </Card>

      <Button title="Scan a rider's QR code" onPress={() => router.push('/(app)/scan')} />
      <Button title="Search by number plate" variant="ghost" onPress={() => router.push('/check')} />
      {canRegister && <Button title="Register a rider" variant="ghost" onPress={() => router.push('/(app)/register')} />}

      {hasRole('traffic_officer', 'lc_official') && (
        <Text style={{ color: colors.muted, textAlign: 'center' }}>All lookups are logged.</Text>
      )}
    </ScrollView>
  );
}
