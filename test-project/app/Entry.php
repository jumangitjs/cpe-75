<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Entry extends Model
{
    private $img_src;
    private $title;

    public function likers() {
        return $this->hasMany('App\Liker');
    }
}
