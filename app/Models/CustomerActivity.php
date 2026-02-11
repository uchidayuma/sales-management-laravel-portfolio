<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerActivity extends Model
{
    protected $guarded = ['id'];

    // INTENTIONAL FLAW: No relationships defined to force manual fetching
}
