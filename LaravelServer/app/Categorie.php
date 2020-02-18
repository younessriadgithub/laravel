<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Categorie extends Model
{
    public function property()
    {
        return $this->hasMany('App\Property');
    }
}
