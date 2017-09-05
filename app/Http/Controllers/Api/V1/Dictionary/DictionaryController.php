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
                'const' => Dictionary::get_const()
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
//                    if (strpos($fragment['key'], '.') !== false) {
//                        $key_array = explode('.', $fragment['key']);
                        $result[$fragment['key']] = $fragment['text'];
//                        $key_array[count($key_array) - 1] = $fragment['text'];

//                        $result = [];
//                        for ($i = count($key_array) - 1; $i >= 0; $i--) {
//                            $result = [ $key_array[$i] => $result ];
//                        }
//                    } else {
//                        $result[$fragment['key']] = $fragment['text'];
//                    }
                }
            }
        }

        return response()->json($result);
    }
}