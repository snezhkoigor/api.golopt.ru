<?php
/**
 * Created by PhpStorm.
 * User: igorsnezko
 * Date: 03.08.17
 * Time: 18:38
 */

namespace App;


use Illuminate\Database\Eloquent\Model;

class Activation extends Model
{
    protected $table = 'user_activations';

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}