<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BikeImage extends Model
{
    protected $guarded = [];

    public function bike()
    {
        return $this->belongsTo(Bike::class);
    }
}