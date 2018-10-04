<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 *
 * @property integer $id
 * @property string $parse_date
 * @property string $created_at
 * @property string $updated_at
 *
 * @property OptionStrikes[] $strikes
 *
 * Class OptionParseDates
 * @package App\Models
 */
class OptionParseDates extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    	'parse_date'
    ];

	protected $table = 'option_parse_dates';

    protected $dates = [
    	'parse_date',
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
}
