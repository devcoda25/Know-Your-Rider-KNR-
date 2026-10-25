import { useCallback, useEffect, useState } from 'react';
import { FlatList, RefreshControl, Text, View } from 'react-native';
import { api } from '@/lib/api';
import { Card, ErrorNote, StatusPill } from '@/components/ui';
import type { RiderListItem } from '@/lib/types';
import { colors } from '@/theme';

export default function RidersScreen() {
  const [riders, setRiders] = useState<RiderListItem[]>([]);
  const [error, setError] = useState<string | null>(null);
  const [loading, setLoading] = useState(true);

  const load = useCallback(async () => {
    setError(null);
    try {
      const res = await api<{ data: RiderListItem[] }>('/riders');
      setRiders(res.data);
    } catch (e) {
      setError((e as Error).message);
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => {
    void load();
  }, [load]);

  return (
    <FlatList
      data={riders}
      keyExtractor={(r) => r.id}
      contentContainerStyle={{ padding: 20, gap: 12 }}
      refreshControl={<RefreshControl refreshing={loading} onRefresh={load} />}
      ListHeaderComponent={error ? <ErrorNote message={error} /> : null}
      ListEmptyComponent={
        loading ? null : (
          <View style={{ padding: 32, alignItems: 'center' }}>
            <Text style={{ fontWeight: '700', color: colors.text }}>No riders yet</Text>
            <Text style={{ color: colors.muted }}>Registered riders will show up here.</Text>
          </View>
        )
      }
      renderItem={({ item }) => (
        <Card>
          <Text style={{ fontSize: 17, fontWeight: '700', color: colors.text }}>{item.full_name}</Text>
          <Text style={{ color: colors.muted }}>
            {item.bike.plate ?? 'No plate'} · {item.stage.name}
          </Text>
          <StatusPill status={item.status} />
        </Card>
      )}
    />
  );
}
