<?php
/**
 * Created by PhpStorm.
 * User: igorsnezko
 * Date: 14.08.17
 * Time: 17:42
 */

namespace App\Http\Controllers\Api\V1\Country;


use App\Http\Controllers\Controller;
use Gerardojbaez\GeoData\Models\Country;

class CountryController extends Controller
{
    public function search($name)
    {
        $result = [];

        if ($name && ($countries = Country::select('name', 'code')->where('name', 'like', '%' . mb_strtolower($name) . '%')->get())) {
            foreach ($countries as $country) {
                $result[$country['code']] = [
                    'key' => $country['code'],
                    'text' => $country['name']
                ];
            }
        }

        return response()->json([
            'success' => true,
            'message' => null,
            'data' => $result
        ]);
    }
}