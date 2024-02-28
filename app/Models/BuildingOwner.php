<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BuildingOwner extends Model
{
    use HasFactory;

    protected $fillable = ['first_name', 'last_name', 'email', 'active'];

    public function propertyManagers(): HasMany
    {
        return $this->hasMany(PropertyManager::class, 'building_owner_id');
    }
}
