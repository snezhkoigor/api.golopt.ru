<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 *
 * @property integer $id
 * @property integer $parse_date_id
 * @property string $symbol
 * @property string $expire
 * @property float $fp
 * @property string $type
 * @property boolean $odr
 * @property string $created_at
 * @property string $updated_at
 *
 * @property OptionStrikeCallsPuts[] $calls_puts
 *
 * Class Strikes
 * @package App\Models
 */
class OptionStrikes extends Model
{
	const TYPES_AMER = 'a';
	const TYPES_WEEK = 'w';
	const TYPES_MONTH = 'm';
	const TYPES_WEDNESDAY = 'wd';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    	'symbol',
        'expire',
	    'fp',
	    'parse_date_id',
        'type',
	    'strike',
	    'odr'
    ];

	protected $table = 'option_strikes';

    protected $dates = [
    	'created_at',
	    'updated_at'
    ];

    /**
	 * @return HasMany
	 */
	public function calls_puts()
	{
		return $this->hasMany(OptionStrikeCallsPuts::class, 'strike_id', 'id');
	}
	
	public static function getDefaultFields()
	{
		return [
			'odr',
			'expire',
			'fp',
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
			'is_balance_call',
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
			'is_balance_puts'
		];
	}
}
