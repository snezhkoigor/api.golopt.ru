<?php
/**
 * Created by PhpStorm.
 * User: igorsnezko
 * Date: 21.07.17
 * Time: 22:05
 */

namespace App\Console\Commands;

use App\Rate;
use Illuminate\Support\Facades\Log;
use Ixudra\Curl\Facades\Curl;
use Illuminate\Console\Command;

class GetYahooRates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'getYahooRates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get rates from Yahoo website';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $response = Curl::to('https://query.yahooapis.com/v1/public/yql?q=select+*+from+yahoo.finance.xchange+where+pair+=+%22USDRUB,EURRUB%22&format=json&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys&callback=')
            ->get();

        if ($response) {
            $json = json_decode($response, true);

            foreach ($json['query']['results']['rate'] as $item) {
                $info = Rate::where([
                    ['date', date('Y-m-d')],
                    ['name', $item['id']]
                ])->first();

                if ($info) {
                    $rate = $info;
                } else {
                    $rate = new Rate();
                }

                $rate->name = $item['id'];
                $rate->rate = (float)$item['Rate'];
                $rate->date = date('Y-m-d');
                $rate->save();
            }
        } else {
            Log::warning('Не смогли получить курсы валют.');
        }

        return true;
    }
}