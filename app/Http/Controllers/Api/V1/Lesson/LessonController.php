<?php

namespace App\Http\Controllers\Api\V1\Lesson;

use App\Exceptions\SystemErrorException;
use App\Lesson;
use App\Repositories\LessonRepository;
use App\Services\RichTextService;
use App\Transformers\LessonTransformer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class LessonController extends Controller
{
	private $rich_text_service;

	public function __construct(RichTextService $rich_text_service)
	{
		$this->rich_text_service = $rich_text_service;
	}

	public function rules()
	{
		return [
			'title' => 'required',
			'text' => 'required'
		];
	}

	public function messages()
	{
		return [
			'title.required' => 'Заполните заголовок',
			'text.required' => 'Заполните текст',
		];
	}

    public function getLessons(Request $request)
    {
	    $filters = $this->getFilters($request);
	    $sorts = $this->getSortParameters($request);
	    $search_string = $this->getSearchString($request);
	    $fieldsets = $this->getFieldsets($request);
	    $includes = $this->getIncludes($request);
	    $limit = $this->getPaginationLimit($request);
	    $offset = $this->getPaginationOffset($request);

	    $relations = $this->getRelationsFromIncludes($request);

	    $lessons = LessonRepository::getLessons($filters, $sorts, $relations, ['*'], $search_string, $limit, $offset);

	    $meta = [
		    'count' => LessonRepository::getLessonsCount($filters, $search_string),
	    ];

	    return fractal($lessons, new LessonTransformer())
		    ->parseIncludes($includes)
		    ->parseFieldsets($fieldsets)
		    ->addMeta($meta)
		    ->respond();
    }

	public function getLessonById($lesson_id)
	{
		$lesson = Lesson::find($lesson_id);
		if ($lesson === null) {
			throw new NotFoundHttpException('Нет урока');
		}

		return fractal($lesson, new LessonTransformer())
			->respond();
	}

	public function view()
	{
		return fractal(LessonRepository::getLessons(), new LessonTransformer())
			->toArray();
	}

	public function show($lesson_id)
	{
		$lesson = Lesson::find($lesson_id);
		if ($lesson === null) {
			throw new NotFoundHttpException('Нет урока');
		}

		return fractal($lesson, new LessonTransformer())
			->respond();
	}

    public function add(Request $request)
    {
    	$this->validate($request, $this->rules(), $this->messages());

    	try
	    {
		    $lesson = new Lesson();
		    $lesson->fill($request->all());
		    $lesson->text = $this->rich_text_service->getProcessedNewsText($request->get('text'));
		    $lesson->active = $request->get('active', true);
		    $lesson->save();
	    }
	    catch (\Exception $e)
	    {
		    throw new SystemErrorException('Ошибка добавления урока', $e);
	    }

	    return response()->json(['data' => null], Response::HTTP_NO_CONTENT);
    }

    public function updateById(Request $request, $lesson_id)
    {
	    $this->validate($request, $this->rules(), $this->messages());

	    $lesson = Lesson::find($lesson_id);
		if ($lesson === null) {
			throw new NotFoundHttpException('Нет урока');
		}

	    try
	    {
		    $lesson->fill($request->all());
		    $lesson->active = $request->get('active');
		    $lesson->text = $this->rich_text_service->getProcessedNewsText($request->get('text'));
		    $lesson->save();
	    }
	    catch (\Exception $e)
	    {
		    throw new SystemErrorException('Ошибка редактирования урока', $e);
	    }

	    return response()->json(['data' => null], Response::HTTP_NO_CONTENT);
    }

    public function deleteById($lesson_id)
    {
	    $lesson = Lesson::find($lesson_id);
		if ($lesson === null) {
			throw new NotFoundHttpException('Нет урока');
		}

	    try
	    {
		    $lesson->is_delete = true;
		    $lesson->save();
	    }
	    catch (\Exception $e)
	    {
		    throw new SystemErrorException('Ошибка удаления урока', $e);
	    }

	    return response()->json(['data' => null], Response::HTTP_NO_CONTENT);
    }
}
