<?php

namespace Database\Seeders;

use App\Models\Driver;
use App\Models\Permit;
use App\Models\User;
use App\Services\PermitQrService;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $driver = Driver::query()->updateOrCreate(
            ['employee_id' => 'ZFF-1001'],
            [
                'user_id' => null,
                'full_name' => 'Jacob Chisenga',
                'nrc' => '123456/78/1',
                'department' => 'Transport',
                'license_number' => 'DL-2024-8891',
                'license_class' => 'B',
                'phone' => '+260 97 000 0001',
            ]
        );

        if ($driver->permits()->exists()) {
            return;
        }

        $permit = Permit::create([
            'driver_id' => $driver->id,
            'permit_number' => 'DPMS-'.now()->format('Y').'-0012',
            'issue_date' => now()->subMonths(2),
            'expiry_date' => now()->addMonths(10),
            'status' => Permit::STATUS_VALID,
            'issued_by' => User::where('role', User::ROLE_FLEET_OFFICER)->value('id'),
            'qr_code' => null,
        ]);

        PermitQrService::ensure($permit);

        Driver::query()->updateOrCreate(
            ['employee_id' => 'ZFF-1002'],
            [
                'full_name' => 'Mary Banda',
                'nrc' => '234567/89/2',
                'department' => 'Forestry Operations',
                'license_number' => 'DL-2023-4410',
                'license_class' => 'C',
                'phone' => '+260 96 000 0002',
            ]
        );
    }
}
