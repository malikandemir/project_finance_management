<?php

namespace Database\Seeders;

use App\Models\TheUniformChartOfAccount;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class TheUniformChartOfAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Path to the CSV file
        $csvFile = base_path('The Uniform Chart of Accounts - Sayfa1.csv');
        
        // Check if file exists
        if (!File::exists($csvFile)) {
            $this->command->error('CSV file not found: ' . $csvFile);
            return;
        }
        
        // Temporarily disable foreign key checks to allow truncation
        if (\DB::connection()->getDriverName() === 'mysql') {
            \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        } elseif (\DB::connection()->getDriverName() === 'sqlite') {
            \DB::statement('PRAGMA foreign_keys = OFF;');
        }
        
        // Clear existing records
        TheUniformChartOfAccount::truncate();
        
        // Re-enable foreign key checks
        if (\DB::connection()->getDriverName() === 'mysql') {
            \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        } elseif (\DB::connection()->getDriverName() === 'sqlite') {
            \DB::statement('PRAGMA foreign_keys = ON;');
        }
        
        // Open the CSV file
        if (($handle = fopen($csvFile, 'r')) !== false) {
            // Read the header row
            $header = fgetcsv($handle, 1000, ',');
            
            // Read data rows
            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                if (count($data) >= 3) {
                    TheUniformChartOfAccount::create([
                        'number' => $data[0],
                        'tr_name' => $data[1],
                        'en_name' => $data[2],
                    ]);
                }
            }
            
            fclose($handle);
            $this->command->info('The Uniform Chart of Accounts seeded successfully!');
        } else {
            $this->command->error('Could not open the CSV file.');
        }
    }
}
