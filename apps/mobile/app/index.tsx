import { Redirect, useRouter } from 'expo-router';
import { StyleSheet, Text, View } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useAuth } from '@/lib/auth';
import { Button } from '@/components/ui';
import { colors } from '@/theme';

/** Welcome screen. Signed-in users go straight to the app. */
export default function Welcome() {
  const { loading, signedIn } = useAuth();
  const router = useRouter();

  if (loading) return null;
  if (signedIn) return <Redirect href="/(app)" />;

  return (
    <SafeAreaView style={styles.root}>
      <View style={{ flex: 1, justifyContent: 'center', gap: 8 }}>
        <Text style={styles.brand}>Know Your Rider</Text>
        <Text style={styles.tagline}>Ride with confidence.</Text>
      </View>
      <View style={{ gap: 12 }}>
        <Button title="Check a rider" onPress={() => router.push('/check')} />
        <Button title="I'm a stage head or officer" variant="ghost" onPress={() => router.push('/(auth)/phone')} />
      </View>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  root: { flex: 1, padding: 24, backgroundColor: colors.bg },
  brand: { fontSize: 36, fontWeight: '800', color: colors.brand },
  tagline: { fontSize: 18, color: colors.muted },
});
