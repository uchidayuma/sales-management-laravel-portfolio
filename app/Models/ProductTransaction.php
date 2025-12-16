<?php

namespace App\Models;

class ProductTransaction extends MyModel
{
    protected $guarded = ['*'];
    protected $casts = [
      'turf_cuts' => 'json',
    ];
}
