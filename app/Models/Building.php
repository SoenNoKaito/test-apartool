<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Building extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'address', 'active'];

    public function propertyManagers(): HasMany
    {
        return $this->hasMany(PropertyManager::class, 'building_id');
    }
}
