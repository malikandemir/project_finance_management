<?php

namespace App\Filament\Resources\CompanyResource\Pages;

use App\Filament\Resources\CompanyResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CreateCompany extends CreateRecord
{
    protected static string $resource = CompanyResource::class;
    
    /**
     * @param array<string, mixed> $data
     * @return Model
     */
    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            // Check if owner_id is provided and exists
            if (isset($data['owner_id']) && User::find($data['owner_id'])) {
                // If owner_id exists, use it directly
                $data['created_by'] = auth()->id();
                return static::getModel()::create($data);
            }
            
            // If no owner_id or it doesn't exist, create a new user
            $userData = [
                'name' => $data['name'] . ' Owner',
                'email' => $data['email'] ?? 'owner_' . time() . '@' . strtolower(str_replace(' ', '', $data['name'])) . '.com',
                'password' => Hash::make('password'), // Default password, should be changed later
                'is_active' => true,
            ];
            
            // Create the user
            $user = User::create($userData);
            
            // Assign company owner role if exists
            if ($role = \Spatie\Permission\Models\Role::where('name', 'company-owner')->first()) {
                $user->assignRole($role);
            }
            
            // Set the owner_id to the newly created user's ID
            $data['owner_id'] = $user->id;
            $data['created_by'] = auth()->id();
            
            // Create and return the company
            return static::getModel()::create($data);
        });
    }
}
