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

class UpdateUserSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'updateUserSubscriptions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update active status all client subscriptions';

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
        $products = DB::table('product_user')
            ->where('active', 1)
            ->get();

        if ($products) {
            $now = date('Y-m-d');
            foreach ($products as $product) {
                if ($now < $product->subscribe_date_until) {
                    DB::table('product_user')
                        ->where('id', $product->id)
                        ->update([
                            'active' => 0,
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);
                }
            }
        }
    }
}