<?php
/**
 * Created by PhpStorm.
 * User: igorsnezko
 * Date: 31.07.17
 * Time: 23:54
 */

namespace App\Helpers;


class GetCountryFromIP
{
    public static function execute() {
        $result = null;
//        $ip = $_SERVER['REMOTE_ADDR'];
        $ip = '95.161.170.226';

        $country = exec("whois $ip  | grep -i country"); // Run a local whois and get the result back
        //$country = strtolower($country); // Make all text lower case so we can use str_replace happily
        // Clean up the results as some whois results come back with odd results, this should cater for most issues
        $country = str_replace("country:", "", "$country");
        $country = str_replace("Country:", "", "$country");
        $country = str_replace("Country :", "", "$country");
        $country = str_replace("country :", "", "$country");
        $country = str_replace("network:country-code:", "", "$country");
        $country = str_replace("network:Country-Code:", "", "$country");
        $country = str_replace("Network:Country-Code:", "", "$country");
        $country = str_replace("network:organization-", "", "$country");
        $country = str_replace("network:organization-usa", "us", "$country");
        $country = str_replace("network:country-code;i:us", "us", "$country");
        $country = str_replace("eu#countryisreallysomewhereinafricanregion", "af", "$country");
        $country = str_replace("", "", "$country");
        $country = str_replace("countryunderunadministration", "", "$country");
        $country = str_replace(" ", "", "$country");

        if ($country) {
            $result = $country;
        }

        return $result;
    }
}