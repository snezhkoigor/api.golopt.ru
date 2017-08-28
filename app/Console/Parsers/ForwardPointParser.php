<?php
/**
 * Created by PhpStorm.
 * User: igorsnezko
 * Date: 21.07.17
 * Time: 22:05
 */

namespace App\Console\Parsers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\ForwardPoint;

class ForwardPointParser
{
    public function parse($file)
    {
        $disk = Storage::disk('public');

        $contents = $disk->get($file);

        if ($contents) {
            $insert = [];
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
                    foreach ($insert as $symbol => $fp) {
                        $info = ForwardPoint::where([
                            ['date', date('Y-m-d')],
                            ['name', $symbol]
                        ])->first();

                        if ($info) {
                            $forward_point = $info;
                        } else {
                            $forward_point = new ForwardPoint();
                        }

                        $forward_point->name = $symbol;
                        $forward_point->fp = $fp;
                        $forward_point->date = date('Y-m-d');
                        $forward_point->save();
                    }
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