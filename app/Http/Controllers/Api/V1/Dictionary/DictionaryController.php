<?php
/**
 * Created by PhpStorm.
 * User: igorsnezko
 * Date: 14.08.17
 * Time: 16:22
 */

namespace App\Http\Controllers\Api\V1\Dictionary;

use App\Dictionary;
use App\Fragment;
use App\Http\Controllers\Controller;

class DictionaryController extends Controller
{
    public function index()
    {
        return response()->json([
            'status' => true,
            'message' => null,
            'data' => [
                'price_by' => Dictionary::get_product_price_by(),
                'locales' => Dictionary::get_localizations(),
                'payment_systems' => Dictionary::get_available_payment_systems(),
                'const' => Dictionary::get_const(),
                'countries' => Dictionary::get_countries()
            ]
        ]);
    }

    public function getFragments($lang)
    {
        $result = [];
        if ($lang) {
            $data = Fragment::where('text', 'like', '{%' . $lang . '%:%}')->get();

            if (count($data) > 0) {
                foreach ($data as $fragment) {
                    $result[$fragment['key']] = $fragment['text'];
                }
            }
        }

        return response()->json($result);
    }
}