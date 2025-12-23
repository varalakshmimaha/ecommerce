<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['mobile' => '9845145363'],
            [
                'email'       => 'info@suvee.com',
                'password'    => Hash::make('suveeindia123'),
                'name'        => 'Suvee India',
                'is_admin'    => true,
                'is_verified' => true,
            ]
        );

        $this->command->info('✅ Admin user seeded successfully.');
    }
}
