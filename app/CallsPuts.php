<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 *
 * @property integer $id
 * @property string $strike_id
 * @property string $type
 * @property string $open_interest
 * @property string $volume
 * @property string $premia
 * @property string $spros_1
 * @property string $spros_2
 * @property string $predlojenie_1
 * @property string $predlojenie_2
 * @property string $prirost_tekushiy
 * @property string $prirost_predydushiy
 * @property string $money_obshiy
 * @property string $money_tekushiy
 * @property string $balance_of_day
 * @property boolean $is_balance
 *
 * @property Strikes $strike
 *
 * @property string $created_at
 * @property string $updated_at
 *
 * Class CallsPuts
 * @package App\Models
 */
class CallsPuts extends Model
{
	const TYPES_CALL = 'call';
	const TYPES_PUT = 'puts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    	'strike_id',
	    'type',
        'open_interest',
	    'volume',
	    'premia',
	    'spros_1',
	    'spros_2',
	    'predlojenie_1',
	    'predlojenie_2',
	    'prirost_tekushiy',
	    'prirost_predydushiy',
	    'money_obshiy',
	    'money_tekushiy',
	    'balance_of_day',
	    'is_balance'
    ];

	protected $table = 'calls_puts';

    protected $dates = [
    	'created_at',
	    'updated_at'
    ];
    
    /**
	 * @return HasOne
	 */
	public function strike()
	{
		return $this->hasOne(Strikes::class, 'id', 'strike_id');
	}
}
