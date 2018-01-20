<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;
use Kodeine\Acl\Traits\HasRole;
use Countries;

class User extends Authenticatable
{
    use HasRole;

    const AVAILABLE_LANG_RU = 'ru';
    const AVAILABLE_LANG_EN = 'en';

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

    public static function getDevAccounts()
    {
        return [
            '305019', '10175989', '3391875', '5542813', '10177055', '104363', '157179', '5147282', '279213', '10182844', '29655990', '3676583', '3676584'
        ];
    }

    public static function getLanguage($country)
    {
        return !in_array($country, ['Russia', 'Ukraine', 'Belarus', 'Kazakhstan']) || null === $country ? self::AVAILABLE_LANG_EN : self::AVAILABLE_LANG_RU;
    }

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

    public function feedback()
    {
        return $this->hasMany('App\Feedback')
            ->orderBy('created_at', 'DESC');
    }

    public function payments()
    {
        return $this->hasMany('App\Payment')
            ->orderBy('created_at', 'DESC');
    }

    public function hasProductByItemId($item_id)
    {
        return DB::table('product_user')
            ->where([
                ['user_id', $this->id],
                ['id', $item_id]
            ])->first();
    }

    public function products()
    {
        return $this->belongsToMany('App\Product', 'product_user')
            ->withPivot([
                'trade_account', 'broker', 'id', 'active', 'subscribe_date_until', 'type'
            ])
            ->with('payments');
    }

    public static function generate_password($number)
    {
        $result = '';
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

        for($i = 0; $i < $number; $i++) {
            $index = mt_rand(0, count($arr) - 1);
            $result .= $arr[$index];
        }

        return $result;
    }

    public static function generatePhoneCode($number = 6)
    {
        $result = '';
        $arr = array('1','2','3','4','5','6','7','8','9','0');

        for($i = 0; $i < $number; $i++) {
            $index = mt_rand(0, count($arr) - 1);
            $result .= $arr[$index];
        }

        return $result;
    }

    public static function replaceCallingCodeFromPhone($calling_code, $phone)
    {
        $result = $phone;
        if (strpos($phone, $calling_code) === 0 || strpos($phone, '+' . $calling_code) === 0) {
            $result = substr($phone, strlen($calling_code));
        }

        return $result;
    }

    public static function getCallingCode($country)
    {
        $result = null;

        $countryFromDb = DB::table('countries')->where('name', '=', $country)->first();
        $callingCode = null;

        // TODO баг в пакете
	    if ($country === 'Kazakhstan') {
		    $result = '7';
	    } elseif ($countryFromJson = Countries::where('cca2', $countryFromDb->code)->first()) {
            $result = $countryFromJson->items['callingCode'][0];
        }

        return $result;
    }
}