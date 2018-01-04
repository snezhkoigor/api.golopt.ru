<?php

namespace App\Repositories;

use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Class ClientRepository
 * @package App\Repositories
 */
class UserRepository
{
	/**
	 * @return mixed
	 */
	public static function widgetsTotalRegistrations()
	{
		$result = [];

		$data = User::query()
			->select(DB::raw('COUNT(id) as count, country'))
			->groupBy('country')
			->orderBy('count')
			->get();

		if ($data) {
			$result['name'] = 'Users';
			foreach ($data as $item) {
				$result['data'][] = [ $item['country'] ?: 'unknown', $item['count'] ];
			}
		}

		return $result;
	}


	/**
	 * @param null $period_type
	 * @return array
	 */
	public static function widgetsRegistrationsAndActivations($period_type = null)
	{
		$result = [
			'categories' => [],
			'registrations' => [
				'name' => 'Регистрации',
				'data' => []
			],
			'activations' => [
				'name' => 'Активации',
				'data' => []
			]
		];

		$query = User::query();
		switch ($period_type) {
			case 'year':
				$query->select(DB::raw('COUNT(id) as count, SUM(active) as activations, DATE_FORMAT(created_at, \'%Y\') as date'));
				$query->groupBy(DB::raw('DATE_FORMAT(created_at, \'%Y\')'));
				break;
			case 'month':
				$query->select(DB::raw('COUNT(id) as count, SUM(active) as activations, DATE_FORMAT(created_at, \'%m.%Y\') as date'));
				$query->groupBy(DB::raw('DATE_FORMAT(created_at, \'%m.%Y\')'));
				$query->whereBetween('created_at', [ Carbon::today(), Carbon::today()->subMonths(12) ]);
				break;
			default:
				$query->select(DB::raw('COUNT(id) as count, SUM(active) as activations, DATE_FORMAT(created_at, \'%d.%m.%Y\') as date'));
				$query->groupBy(DB::raw('DATE_FORMAT(created_at, \'%d.%m.%Y\')'));
				$query->whereBetween('created_at', [ Carbon::today(), Carbon::today()->subDays(7) ]);
				break;
		}

		$data = $query
			->orderBy('created_at')
			->get();

		if ($data) {
			foreach ($data as $item) {
				$result['categories'][] = $item['date'];
				$result['registrations']['data'][] = (int)$item['count'];
				$result['activations']['data'][] = (int)$item['activations'];
			}
		}

		return $result;
	}
}