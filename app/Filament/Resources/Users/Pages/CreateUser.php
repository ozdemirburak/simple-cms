<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $role = $data['role'] ?? null;
        unset($data['role']);

        $record = static::getModel()::create($data);

        if ($role) {
            $record->role = $role;
            $record->save();
        }

        return $record;
    }
}
