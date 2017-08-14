<?php
/**
 * Created by PhpStorm.
 * User: igorsnezko
 * Date: 03.08.17
 * Time: 18:38
 */

namespace App;


use Illuminate\Database\Eloquent\Model;

class ChangeEmail extends Model
{
    protected $table = 'user_change_emails';

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}