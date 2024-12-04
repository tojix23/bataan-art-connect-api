<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\PersonalInfo;
use App\Models\Account;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::transaction(function () {
            // Create new personal information record
            $personalInformation = PersonalInfo::updateOrCreate(
                [
                    'email' => 'superadmin@test.com', // Ensure uniqueness by email
                ],
                [
                    'first_name' => 'Super',
                    'last_name' => 'Admin',
                    'main_address' => 'N/A',
                    'sub_address' => 'N/A',
                    'occupation' => 'Administrator',
                    'role' => 'Super Admin',
                    'gender' => 'Male',
                    'contact_number' => 'N/A',
                    'birthdate' => date('Y-m-d'),
                    'type' => 'Administrator',
                ]
            );

            // Create a related account record
            Account::updateOrCreate(
                [
                    'email' => 'superadmin@test.com', // Ensure uniqueness by email
                ],
                [
                    'personal_id' => $personalInformation->id,
                    'fullname' => 'Super Admin',
                    'type' => 'Super Admin',
                    'email_verified_at' => '-',
                    'is_verify' => true,
                    'password' => Hash::make('bataanadmin!'),
                ]
            );
        });
    }
}
