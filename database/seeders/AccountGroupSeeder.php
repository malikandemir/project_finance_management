<?php

namespace Database\Seeders;

use App\Models\AccountGroup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AccountGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default account group if it doesn't exist
        if (!AccountGroup::where('name', 'Default Group')->exists()) {
            AccountGroup::create([
                'name' => 'Default Group',
            ]);
        }
    }
}
