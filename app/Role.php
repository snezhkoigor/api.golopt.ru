<?php
/**
 * Created by PhpStorm.
 * User: igorsnezko
 * Date: 09.08.17
 * Time: 16:00
 */

namespace App;


use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    public function user()
    {
        return $this->belongsToMany('App\User');
    }
}