<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\RoleAssignment;
use App\Models\User;
use Illuminate\Console\Command;

class MakeSuperAdmin extends Command
{
    protected $signature = 'knr:make-super-admin {phone : Phone number in +256XXXXXXXXX format} {--name= : Display name}';

    protected $description = 'Create (or promote) a platform super admin. Run this once to bootstrap the first admin.';

    public function handle(): int
    {
        $phone = (string) $this->argument('phone');

        if (! preg_match('/^\+256\d{9}$/', $phone)) {
            $this->error('Phone must look like +256700000000.');

            return self::FAILURE;
        }

        $role = Role::where('key', 'super_admin')->first();

        if (! $role) {
            $this->error('Roles are missing. Run: php artisan db:seed');

            return self::FAILURE;
        }

        $user = User::firstOrCreate(['phone' => $phone], ['name' => $this->option('name')]);

        RoleAssignment::firstOrCreate(
            ['user_id' => $user->id, 'role_id' => $role->id, 'scope_type' => 'platform', 'scope_id' => null],
        );

        $this->info("{$phone} is now a super admin. Sign in with a one-time code.");

        return self::SUCCESS;
    }
}
