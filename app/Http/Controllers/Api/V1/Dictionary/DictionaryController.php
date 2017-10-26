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
                'price_by' => Dictionary::getProductPriceBy(),
                'locales' => Dictionary::getLocalizations(),
                'payment_systems' => Dictionary::getAvailablePaymentSystems(),
                'const' => Dictionary::getConst(),
                'countries' => Dictionary::getCountries(),
                'codes' => Dictionary::getCallingCodes()
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