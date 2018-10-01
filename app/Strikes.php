<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 *
 * @property integer $id
 * @property string $symbol
 * @property string $expw
 * @property string $parse_date
 * @property string $type
 * @property integer $fp
 * @property boolean $odr
 * @property string $created_at
 * @property string $updated_at
 *
 * @property CallsPuts[] $calls_puts
 *
 * Class Strikes
 * @package App\Models
 */
class Strikes extends Model
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
        'expw',
	    'parse_date',
        'type',
	    'strike',
	    'fp',
	    'odr'
    ];

	protected $table = 'strikes';

    protected $dates = [
    	'created_at',
	    'updated_at'
    ];

    /**
	 * @return HasMany
	 */
	public function calls_puts()
	{
		return $this->hasMany(CallsPuts::class, 'strike_id', 'id');
	}
}
