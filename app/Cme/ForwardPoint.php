<?php
/**
 * Created by PhpStorm.
 * User: igorsnezko
 * Date: 21.07.17
 * Time: 22:05
 */

namespace App\Cme;


use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ForwardPoint
{
    public function parse($file)
    {
        $disk = Storage::disk('public');

        $contents = $disk->get($file);

        if ($contents) {
            $insert = [];
            $file_content = '';
            $lines = explode("\n", $contents);

            if (count($lines) !== 0) {
                foreach ($lines as $line) {
                    if ($line !== '') {
                        $line_arr = explode(',', $line);

                        if (count($line_arr) === 5) {
                            $pair_arr = explode('_', $line_arr[0]);
                            $symbol = null;
                            if (count($pair_arr) === 3) {
                                $symbol = $this->getPairWithMajor($pair_arr[1]);
                            }

                            $insert[$symbol] = (float)$line_arr[1];
                        }
                    }
                }

                if (count($insert) !== 0) {
                    $file_content .= (!empty($insert[Base::PAIR_AUD . Base::PAIR_USD]) ? $insert[Base::PAIR_AUD . Base::PAIR_USD] : 0) . ';';
                    $file_content .= (!empty($insert[Base::PAIR_USD . Base::PAIR_CAD]) ? $insert[Base::PAIR_USD . Base::PAIR_CAD] : 0) . ';';
                    $file_content .= (!empty($insert[Base::PAIR_USD . Base::PAIR_CHF]) ? $insert[Base::PAIR_USD . Base::PAIR_CHF] : 0) . ';';
                    $file_content .= (!empty($insert[Base::PAIR_EUR . Base::PAIR_USD]) ? $insert[Base::PAIR_EUR . Base::PAIR_USD] : 0) . ';';
                    $file_content .= (!empty($insert[Base::PAIR_GBP . Base::PAIR_USD]) ? $insert[Base::PAIR_GBP . Base::PAIR_USD] : 0) . ';';
                    $file_content .= (!empty($insert[Base::PAIR_USD . Base::PAIR_JPY]) ? $insert[Base::PAIR_USD . Base::PAIR_JPY] : 0) . ';';
                    $file_content .= (!empty($insert[Base::PAIR_MXN . Base::PAIR_USD]) ? $insert[Base::PAIR_MXN . Base::PAIR_USD] : 0) . ';';
                    $file_content .= (!empty($insert[Base::PAIR_NZD . Base::PAIR_USD]) ? $insert[Base::PAIR_NZD . Base::PAIR_USD] : 0) . ';';

                    $disk->put('Forward_Point.csv', $file_content);
                }
            } else {
                Log::warning('Файл forward point пуст.', [ 'file' => $file ]);
            }
        } else {
            Log::warning('Не смогли получить содержимое файла forward point.', [ 'file' => $file ]);
        }

        return true;
    }

    protected function getPairWithMajor($pair)
    {
        $result = null;

        switch ($pair) {
            case Base::PAIR_AUD:
                $result = Base::PAIR_AUD.Base::PAIR_USD;

                break;

            case Base::PAIR_EUR:
                $result = Base::PAIR_EUR.Base::PAIR_USD;

                break;

            case Base::PAIR_GBP:
                $result = Base::PAIR_GBP.Base::PAIR_USD;

                break;

            case Base::PAIR_CAD:
                $result = Base::PAIR_USD.Base::PAIR_CAD;

                break;

            case Base::PAIR_CHF:
                $result = Base::PAIR_USD.Base::PAIR_CHF;

                break;

            case Base::PAIR_JPY:
                $result = Base::PAIR_USD.Base::PAIR_JPY;

                break;

            case Base::PAIR_XAU:
                $result = Base::PAIR_XAU.Base::PAIR_USD;

                break;

            case Base::PAIR_NZD:
                $result = Base::PAIR_NZD.Base::PAIR_USD;

                break;

            case Base::PAIR_MXN:
                $result = Base::PAIR_MXN.Base::PAIR_USD;

                break;
        }

        return $result;
    }
}