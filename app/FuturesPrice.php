<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 *
 * @property integer $id
 * @property string $date
 * @property string $pair
 * @property double $price
 *
 * Class FuturesPrice
 * @package App\Models
 */
class FuturesPrice extends Model
{
	protected $table = 'futures_prices';


    public $timestamps = null;
}
