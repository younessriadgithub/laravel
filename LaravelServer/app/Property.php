<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
    public function categorie()
    {
        return $this->belongsTo('App\Categorie', 'categorie_id');
    }


    public function photos()
    {
        return $this->hasMany('App\Photo');
    }
}
