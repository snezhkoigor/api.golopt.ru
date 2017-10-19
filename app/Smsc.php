<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Smsc extends Model
{
    protected $table = null;

    public static function send($post, $token)
    {
        $result = true;

        $ch = curl_init('http://smsc.ru/sys/send.php');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            'login' => config('smsc.SMSC_LOGIN'),
            'psw' => config('smsc.SMSC_PASSWORD'),
            'phones' => $post['phones'],
            'mes' => $post['mes'],
            'charset' => config('smsc.SMSC_CHARSET'),
            'sender' => config('smsc.SMSC_FROM'),
            'fmt' => config('smsc.SMSC_FMT')
        ]);
        $body = curl_exec($ch);
        curl_close($ch);

        $answer = json_decode($body);

        $admin_message = '';
        if ($answer) {
            if (property_exists($answer, 'error')) {
                $admin_message = 'Запрос не выполнился. Код ошибки:' . $answer->error_code . '. Текст ошибки: ' . $answer->error;
                $result = false;
            }
        } else {
            $admin_message = 'Запрос не выполнился. Не удалось установить связь с сервером.';
            $result = false;
        }

        if ('' !== $admin_message) {
            DB::table('user_activations')
                ->where('token', $token)
                ->update(['log' => $admin_message]);
        }

        return $result;
    }
}