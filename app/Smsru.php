<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Smsru extends Model
{
    protected $table = null;
    protected $api_id = null;

    public function __construct($apiId)
    {
        $this->api_id = $apiId;
    }

    public function send($post, $token)
    {
        $result = false;

        $ch = curl_init('https://sms.ru/sms/send');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array(
            'api_id' => $this->api_id,
            'to' => $post['to'],
            'msg' => $post['msg'],
            'json' => 1
        )));
        $body = curl_exec($ch);
        curl_close($ch);

        $json = json_decode($body);
        $admin_message = '';

        if ($json) {
            if ($json->status === 'OK') {
                foreach ($json->sms as $phone => $data) {
                    if ($data->status === 'OK') {
                        $admin_message = 'Сообщение на номер ' . $phone . ' успешно отправлено. ID сообщения: ' . $data->sms_id;
                    } else {
                        $admin_message = 'Сообщение на номер ' . $phone . ' не отправлено. Код ошибки:' . $data->status_code . '. Текст ошибки: ' . $data->status_text;
                    }
                }
            } else {
                $admin_message = 'Запрос не выполнился. Код ошибки:' . $json->status_code . '. Текст ошибки: ' . $json->status_text;
            }
        } else {
            $admin_message = 'Запрос не выполнился. Не удалось установить связь с сервером.';
        }

        if ('' !== $admin_message) {
            DB::table('user_activations')
                ->where('token', $token)
                ->update(['log' => $admin_message]);
        } else {
            $result = true;
        }

        return $result;
    }
}