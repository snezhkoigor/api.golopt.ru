<?php

namespace App\Transformers;

use App\Lesson;
use League\Fractal\TransformerAbstract;

/**
 * Class LessonTransformer
 * @package App\Transformers
 */
class LessonTransformer extends TransformerAbstract
{
	protected $availableIncludes = [];


	/**
	 * @param Lesson $lesson
	 * @return array
	 */
	public function transform(Lesson $lesson)
	{
		$data = [
			'id' => (int)$lesson->id,
			'title' => $lesson->title,
			'text' => html_entity_decode($lesson->text),
			'meta_description' => $lesson->meta_description,
			'meta_key' => $lesson->meta_key,
			'active' => (bool)$lesson->active,
			'created_at' => $lesson->created_at,
			'updated_at' => $lesson->updated_at
		];

		return $data;
	}
}
