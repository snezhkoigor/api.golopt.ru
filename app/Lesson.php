<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 *
 * @property integer $id
 * @property string $title
 * @property string $text
 * @property string $meta_key
 * @property string $meta_description
 * @property boolean $active
 * @property boolean $is_deleted
 * @property string $created_at
 * @property string $updated_at
 *
 * Class Lesson
 * @package App\Models
 */
class Lesson extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    	'title',
        'meta_key',
        'meta_description'
    ];

	protected $table = 'lessons';

	protected $guarded = [
        'active',
        'text',
        'is_deleted'
    ];

    protected $dates = [
    	'created_at',
	    'updated_at'
    ];
}
