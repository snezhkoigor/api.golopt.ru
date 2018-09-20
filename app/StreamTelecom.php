<?php

namespace App;

final class StreamTelecom
{
	public static function GetConnect($href)
	{
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL,$href);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);

	    curl_close($ch);

	    return $result;
	}
	
	public static function PostConnect($href, $src)
	{
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-type: application/x-www-form-urlencoded']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CRLF, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $src);
        curl_setopt($ch, CURLOPT_URL, $href);
        $result = curl_exec($ch);

        curl_close($ch);

        return $result;
    }
}
