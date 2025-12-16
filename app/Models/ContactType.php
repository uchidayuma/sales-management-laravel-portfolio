<?php

namespace App\Models;

class ContactType extends MyModel
{
    protected $table = 'contact_types';

    public function contacts()
    {
        return $this->belongsTo('App\Models\Contact');
    }
}
