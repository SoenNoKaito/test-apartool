<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyManager extends Model
{
    use HasFactory;

    protected $fillable = ['building_owner_id', 'building_id', 'active'];

    public function buildingOwner(): BelongsTo
    {
        return $this->belongsTo(BuildingOwner::class, 'building_owner_id');
    }

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class, 'building_id');
    }
}
