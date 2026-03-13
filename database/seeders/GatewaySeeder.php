<?php

namespace Database\Seeders;

use App\Models\Gateway;
use Illuminate\Database\Seeder;

class GatewaySeeder extends Seeder
{
    public function run(): void
    {
        Gateway::query()->updateOrCreate(
            ['slug' => 'gateway_1'],
            [
                'name' => 'Gateway 1',
                'is_active' => true,
                'priority' => 1,
            ]
        );

        Gateway::query()->updateOrCreate(
            ['slug' => 'gateway_2'],
            [
                'name' => 'Gateway 2',
                'is_active' => true,
                'priority' => 2,
            ]
        );
    }
}
