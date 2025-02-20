<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'country',
        'latitude',
        'longitude',
    ];
    protected $hidden = ['created_at', 'updated_at'];

    public function temperatures(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Temperature::class);
    }
}
