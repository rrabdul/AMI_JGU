<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Indicator;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call([
            UserRolePermissionSeeder::class,
            PermissionSeeder::class,
            AuditStatusSeeder::class,
            DepartmentSeeder::class,
            LocationSeeder::class,
            StandardSeeder::class,
            IndicatorSeeder::class,
            SubIndicatorSeeder::class
        ]);
    }
}
