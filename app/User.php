<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Kodeine\Acl\Traits\HasRole;

class User extends Authenticatable
{
    use HasRole;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password'
    ];

    public function activation()
    {
        return $this->hasOne('App\Activation');
    }

    public function role()
    {
        return $this->belongsToMany('App\Role', 'role_user');
    }

    public function activeChangeEmailRequests()
    {
        return $this->hasMany('App\ChangeEmail')
            ->where('active', 1)
            ->orderBy('created_at', 'DESC');
    }

    public function changeEmailRequests()
    {
        return $this->hasMany('App\ChangeEmail')
            ->orderBy('created_at', 'DESC');
    }

    public function payments()
    {
        return $this->hasMany('App\Payment')
            ->orderBy('created_at', 'DESC');
    }

    public function products()
    {
        return $this->belongsToMany('App\Product', 'product_user')
            ->withPivot([
                'trade_account', 'broker', 'id'
            ])
            ->with('payments');
    }

    public static function generate_password($number)
    {
        $arr = array('a','b','c','d','e','f',
            'g','h','i','j','k','l',
            'm','n','o','p','r','s',
            't','u','v','x','y','z',
            'A','B','C','D','E','F',
            'G','H','I','J','K','L',
            'M','N','O','P','R','S',
            'T','U','V','X','Y','Z',
            '1','2','3','4','5','6',
            '7','8','9','0','.',',',
            '(',')','[',']','!','?',
            '&','^','%','@','*','$',
            '<','>','/','|','+','-',
            '{','}','`','~');

        $pass = '';

        for($i = 0; $i < $number; $i++) {
            $index = mt_rand(0, count($arr) - 1);
            $pass .= $arr[$index];
        }

        return $pass;
    }
}