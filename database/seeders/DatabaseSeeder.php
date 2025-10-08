<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Supplier;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Base demo data
        if(!User::where('email','test@example.com')->exists()){
            User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);
        }

        if(!Supplier::where('name','Default Supplier')->exists()){
            Supplier::create([
                'name' => 'Default Supplier',
                'phone' => '0000000000',
            ]);
        }

        // Chart of Accounts
        $this->call(AccountsTableSeeder::class);
    }
}
