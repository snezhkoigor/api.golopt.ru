<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 *
 * @property integer $id
 * @property string $type
 * @property string $title
 * @property string $text
 * @property string $date
 * @property string $meta_key
 * @property string $meta_description
 * @property boolean $active
 * @property boolean $is_delete
 * @property string $created_at
 * @property string $updated_at
 *
 * Class News
 * @package App\Models
 */
class News extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    	'type',
        'title',
        'meta_key',
        'meta_description',
        'date'
    ];

	protected $table = 'news';

	protected $guarded = [
        'active',
        'text',
        'is_delete'
    ];

    protected $dates = [
    	'created_at',
	    'updated_at'
    ];
}
