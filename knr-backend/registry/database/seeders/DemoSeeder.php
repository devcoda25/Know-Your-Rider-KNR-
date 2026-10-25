<?php

namespace Database\Seeders;

use App\Models\Rider;
use App\Models\Stage;
use Illuminate\Database\Seeder;

/**
 * Fictional sample data for local development only:
 *   php artisan db:seed --class=DemoSeeder
 */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $stage = Stage::firstOrCreate(['name' => 'Kisenyi Stage'], [
            'district' => 'Kampala',
            'division' => 'Kampala Central Division',
            'parish' => 'Kisenyi',
            'status' => 'active',
            'contact_name' => 'Kato Samuel',
            'contact_phone' => '+256700000001',
        ]);

        $riders = [
            ['Ssebunya Moses', 'CM90000000001A', 'UBK 482X', 'Bajaj', 'Boxer', 90],
            ['Nakato Joan', 'CF90000000002B', 'UEA 117M', 'TVS', 'HLX', 12],
            ['Okello David', 'CM90000000003C', 'UDV 905C', 'Honda', 'CG 125', -10],
        ];

        foreach ($riders as [$name, $nin, $plate, $make, $model, $days]) {
            if (Rider::where('national_id_hash', Rider::hashNationalId($nin))->exists()) {
                continue;
            }

            $rider = Rider::create([
                'stage_id' => $stage->id,
                'full_name' => $name,
                'phone' => '+2567000001'.random_int(10, 99),
                'national_id' => $nin,
                'national_id_hash' => Rider::hashNationalId($nin),
                'consent_at' => now(),
            ]);

            $rider->bike()->create(['plate' => $plate, 'make' => $make, 'model' => $model]);
            $rider->documents()->create(['type' => 'permit', 'expires_at' => now()->addDays($days)]);
            $rider->documents()->create(['type' => 'insurance', 'expires_at' => now()->addDays($days)]);

            $this->command?->info("{$name}: {$rider->qrUrl()}");
        }
    }
}
