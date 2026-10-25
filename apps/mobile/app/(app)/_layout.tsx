import { Redirect, Tabs } from 'expo-router';
import { useAuth } from '@/lib/auth';
import { colors } from '@/theme';

/** Signed-in area. Which tabs appear depends on the user's roles. */
export default function AppLayout() {
  const { loading, signedIn, hasRole } = useAuth();

  if (loading) return null;
  if (!signedIn) return <Redirect href="/" />;

  const canRegister = hasRole('super_admin', 'stage_head', 'stage_official');

  return (
    <Tabs
      screenOptions={{
        headerShadowVisible: false,
        tabBarActiveTintColor: colors.brand,
        tabBarLabelStyle: { fontSize: 12, fontWeight: '600' },
        sceneStyle: { backgroundColor: colors.bg },
      }}
    >
      <Tabs.Screen name="index" options={{ title: 'Home', tabBarLabel: 'Home' }} />
      <Tabs.Screen name="scan" options={{ title: 'Scan QR', tabBarLabel: 'Scan' }} />
      <Tabs.Screen name="riders" options={{ title: 'Riders', tabBarLabel: 'Riders', href: canRegister ? undefined : null }} />
      <Tabs.Screen name="register" options={{ title: 'Register rider', tabBarLabel: 'Register', href: canRegister ? undefined : null }} />
      <Tabs.Screen name="profile" options={{ title: 'Profile', tabBarLabel: 'Profile' }} />
    </Tabs>
  );
}
