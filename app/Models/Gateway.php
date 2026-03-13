<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gateway extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'is_active',
        'priority'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
