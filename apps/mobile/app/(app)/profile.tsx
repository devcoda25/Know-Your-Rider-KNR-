import { useRouter } from 'expo-router';
import { Text, View } from 'react-native';
import { useAuth } from '@/lib/auth';
import { Button, Card } from '@/components/ui';
import { colors } from '@/theme';

export default function Profile() {
  const { roles, signOut } = useAuth();
  const router = useRouter();

  return (
    <View style={{ padding: 20, gap: 16 }}>
      <Card>
        <Text style={{ fontWeight: '700', color: colors.text }}>Roles</Text>
        {roles.length === 0 && <Text style={{ color: colors.muted }}>None assigned yet.</Text>}
        {roles.map((r, i) => (
          <Text key={i} style={{ color: colors.muted }}>
            {r.role.replace(/_/g, ' ')}
            {r.scope_type ? ` (${r.scope_type})` : ''}
          </Text>
        ))}
      </Card>
      <Button
        title="Sign out"
        variant="ghost"
        onPress={async () => {
          await signOut();
          router.replace('/');
        }}
      />
    </View>
  );
}
