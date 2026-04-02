<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $managerRole = Role::firstOrCreate(['name' => 'manager']);
        $manager = User::factory()->create([
            'name' => 'Admin Manager',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'),
        ]);

        $manager->assignRole($managerRole);

        Customer::factory(10)
            ->has(Ticket::factory()->count(3))
            ->create();
    }
}
