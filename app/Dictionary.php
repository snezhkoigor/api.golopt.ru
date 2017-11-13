<?php
/**
 * Created by PhpStorm.
 * User: igorsnezko
 * Date: 14.08.17
 * Time: 16:23
 */

namespace App;


use Illuminate\Support\Facades\DB;
use Countries;
use Illuminate\Support\Facades\Log;

class Dictionary
{
    const PRODUCT_PRICE_BY_MONTH = 'month';
    const PRODUCT_PRICE_BY_YEAR = 'year';
    const PRODUCT_PRICE_BY_FULL = 'full';

    const PAYMENT_SYSTEM_YANDEX_MONEY = 'ym';
    const PAYMENT_SYSTEM_WEB_MONEY = 'wm';
	const PAYMENT_SYSTEM_QIWI = 'qiwi';
    const PAYMENT_SYSTEM_DEMO = 'demo';

    const PRODUCT_TYPE_REAL = 'real';
    const PRODUCT_TYPE_DEMO = 'demo';

    const CURRENCY_USD = 'USD';
    const CURRENCY_RUB = 'RUB';

    public static function getConst()
    {
        return [
            'PRODUCT_PRICE_BY_MONTH' => 'month',
            'PRODUCT_PRICE_BY_YEAR' => 'year',
            'PRODUCT_PRICE_BY_FULL' => 'full',
            'PAYMENT_SYSTEM_YANDEX_MONEY' => 'ym',
            'PAYMENT_SYSTEM_WEB_MONEY' => 'wm',
            'PAYMENT_SYSTEM_DEMO' => 'demo',
            'PRODUCT_TYPE_REAL' => 'real',
            'PRODUCT_TYPE_DEMO' => 'demo',
            'CURRENCY_USD' => 'usd'
        ];
    }

    public static function getProductPriceBy()
    {
        return [
            self::PRODUCT_PRICE_BY_MONTH => [
                'key' => self::PRODUCT_PRICE_BY_MONTH,
                'text' => 'Месяц'
            ],
            self::PRODUCT_PRICE_BY_YEAR => [
                'key' => self::PRODUCT_PRICE_BY_YEAR,
                'text' => 'Год'
            ],
            self::PRODUCT_PRICE_BY_FULL => [
                'key' => self::PRODUCT_PRICE_BY_YEAR,
                'text' => 'Бессрочно'
            ]
        ];
    }

    public static function getLocalizations()
    {
        return [
            'hello' => 'test'
        ];
    }

    public static function getAvailablePaymentSystems()
    {
        return [
            self::PAYMENT_SYSTEM_YANDEX_MONEY => [
                'key' => self::PAYMENT_SYSTEM_YANDEX_MONEY,
                'text' => 'YandexMoney'
            ],
            self::PAYMENT_SYSTEM_WEB_MONEY => [
                'key' => self::PAYMENT_SYSTEM_WEB_MONEY,
                'text' => 'WebMoney'
            ],
//            self::PAYMENT_SYSTEM_QIWI => [
//	            'key' => self::PAYMENT_SYSTEM_QIWI,
//	            'text' => 'QIWI'
//            ],
            self::PAYMENT_SYSTEM_DEMO => [
                'key' => self::PAYMENT_SYSTEM_DEMO,
                'text' => 'Demo access'
            ]
        ];
    }

    public static function getCountries()
    {
        $result = [];
        $countries = DB::table('countries')
            ->where('active', '=', 1)
            ->orderBy('name')
            ->get();

        if (0 !== count($countries)) {
            foreach ($countries as $country) {
                $result[] = $country->name;
            }
        }

        return $result;
    }

    public static function getCallingCodes()
    {
        $result = [];
        $countries = DB::table('countries')
            ->where('active', '=', 1)
            ->orderBy('name')
            ->get();

        if (0 !== count($countries)) {
            foreach ($countries as $country) {
                if ($countryFromJson = Countries::where('cca2', $country->code)->first()) {
                    if (!empty($countryFromJson->items['callingCode'][0])) {
                    	$result[$country->name] = $country->code === 'KZ' ? '7' : $countryFromJson->items['callingCode'][0];
                    }
                }
            }
        }

        return $result;
    }
}