<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::factory()->create([
            'nomRole' => 'ADMIN',
        ]);

        Role::factory()->create([
            'nomRole' => 'COATCH',
        ]);

        Role::factory()->create([
            'nomRole' => 'Manager',
        ]);

        Role::factory()->create([
            'nomRole' => 'CM',
        ]);
        Role::factory()->create([
            'nomRole' => 'APPRENANT'
        ]);
    }
}
