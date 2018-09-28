<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 *
 * @property integer $id
 * @property string $symbol
 * @property string $expw
 * @property string $parse_date
 * @property string $type
 * @property string $strike
 * @property string $open_interest_call
 * @property string $volume_call
 * @property string $premia_call
 * @property string $spros_1_call
 * @property string $spros_2_call
 * @property string $predlojenie_1_call
 * @property string $predlojenie_2_call
 * @property string $prirost_tekushiy_call
 * @property string $prirost_predydushiy_call
 * @property string $money_obshiy_call
 * @property string $money_tekushiy_call
 * @property string $balance_of_day_call
 *
 * @property string $open_interest_puts
 * @property string $volume_puts
 * @property string $premia_puts
 * @property string $spros_1_puts
 * @property string $spros_2_puts
 * @property string $predlojenie_1_puts
 * @property string $predlojenie_2_puts
 * @property string $prirost_tekushiy_puts
 * @property string $prirost_predydushiy_puts
 * @property string $money_obshiy_puts
 * @property string $money_tekushiy_puts
 * @property string $balance_of_day_puts
 *
 * @property integer $fp
 * @property boolean $odr
 * @property string $created_at
 * @property string $updated_at
 *
 * Class Option
 * @package App\Models
 */
class Option extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    	'symbol',
        'expw',
        'type',
	    'strike',
	    'open_interest_call',
	    'volume_call',
	    'premia_call',
	    'spros_1_call',
	    'spros_2_call',
	    'predlojenie_1_call',
	    'predlojenie_2_call',
	    'prirost_tekushiy_call',
	    'prirost_predydushiy_call',
	    'money_obshiy_call',
	    'money_tekushiy_call',
	    'balance_of_day_call',
	    'open_interest_puts',
	    'volume_puts',
	    'premia_puts',
	    'spros_1_puts',
	    'spros_2_puts',
	    'predlojenie_1_puts',
	    'predlojenie_2_puts',
	    'prirost_tekushiy_puts',
	    'prirost_predydushiy_puts',
	    'money_obshiy_puts',
	    'money_tekushiy_puts',
	    'balance_of_day_puts',
	    'fp',
	    'odr'
    ];

	protected $table = 'options';

    protected $dates = [
    	'created_at',
	    'updated_at'
    ];
}
