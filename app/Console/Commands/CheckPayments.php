<?php
/**
 * Created by PhpStorm.
 * User: igorsnezko
 * Date: 21.07.17
 * Time: 22:05
 */

namespace App\Console\Commands;

use App\Mail\EndSubscriptionEmail;
use App\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class CheckPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'checkPayments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check payment in DB';

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
        $queue = DB::table('payment_answer_queue')
            ->where('active', 1)
            ->get();

        if ($queue) {
            foreach ($queue as $payment) {
                DB::table('payment_answer_queue')
                    ->where('id', $payment->id)
                    ->update([
                        'active' => 0,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
            }
        }
    }
}