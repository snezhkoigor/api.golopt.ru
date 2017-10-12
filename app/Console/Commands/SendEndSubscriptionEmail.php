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

class SendEndSubscriptionEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sendEndSubscriptionEmail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email for end subscription to user';

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
            ->select('products.name', 'product_user.subscribe_date_until', 'users.email', 'users.country')
            ->where('product_user.active', 1)
            ->join('products', 'products.id', '=', 'product_user.product_id')
            ->join('users', 'users.id', '=', 'product_user.user_id')
            ->get();

        if ($products) {
            $check_date = date('Y-m-d', strtotime('+ ' . Product::$how_many_days_before . ' DAYS'));
            foreach ($products as $product) {
                if ($check_date === $product->subscribe_date_until) {
                    $mail = new EndSubscriptionEmail($product->name, $product->subscribe_date_until, $product->country);
                    Mail::to($products->email)->send($mail);

                    unset($mail);
                }
            }
        }

    }
}