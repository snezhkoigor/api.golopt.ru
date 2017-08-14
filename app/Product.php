<?php
/**
 * Created by PhpStorm.
 * User: igorsnezko
 * Date: 03.08.17
 * Time: 11:26
 */

namespace App;


use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public function payments()
    {
        return $this->hasMany('App\Payment');
    }
}