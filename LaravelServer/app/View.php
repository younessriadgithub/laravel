<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class View extends Model
{
    public function property()
    {
        return $this->belongsTo('App\Property', 'property_id');
    }
}
