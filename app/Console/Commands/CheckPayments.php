<?php
/**
 * Created by PhpStorm.
 * User: igorsnezko
 * Date: 21.07.17
 * Time: 22:05
 */

namespace App\Console\Commands;

use App\Dictionary;
use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;

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
                if ('' !== $payment->post) {
                    $post = json_decode($payment->post,true);

                    if (0 !== count($post)) {
                        if (isset($post['label']) || isset($post['LMI_PAYMENT_NO'])) {
                            $transaction = DB::table('payments')
                                ->where('id', (int)(isset($post['label']) ? $post['label'] : $post['LMI_PAYMENT_NO']))
                                ->first();

                            $product = DB::table('products')
                                ->where('id', $transaction->product_id)
                                ->first();

                            $user_product = DB::table('product_user')
                                ->where([
                                    [ 'product_id', $transaction->product_id ],
                                    [ 'user_id', $transaction->user_id ]
                                ])
                                ->first();

                            if ($transaction) {
                                $success = false;
                                switch ($transaction->payment_system) {
                                    case Dictionary::PAYMENT_SYSTEM_YANDEX_MONEY:
                                        $sha1 = sha1( $post['notification_type'] . '&'. $post['operation_id']. '&' . $post['amount'] . '&643&' . $post['datetime'] . '&'. $post['sender'] . '&' . $post['codepro'] . '&' . config('payments.YANDEX_SECRET_KEY') . '&' . $post['label']);
                                        if ($sha1 === $post['sha1_hash']/* && (float)$post['withdraw_amount'] >= (float)$transaction->amount && (float)$post['withdraw_amount'] >= (float)$product->price*/) {
                                            $success = true;
                                        } else {
                                            // неверные данные
                                        }

                                        break;

                                    case Dictionary::PAYMENT_SYSTEM_WEB_MONEY_RUB:
	                                case Dictionary::PAYMENT_SYSTEM_WEB_MONEY_USD:
	                                	$secret_key = config($transaction->payment_system === Dictionary::PAYMENT_SYSTEM_WEB_MONEY_RUB ? 'payments.WEBMONEY_SECRET_KEY_RUB' : 'payments.WEBMONEY_SECRET_KEY_USD');

                                        $sha256 = strtoupper(hash('sha256', $post['LMI_PAYEE_PURSE'].$post['LMI_PAYMENT_AMOUNT'].$post['LMI_PAYMENT_NO'].$post['LMI_MODE'].$post['LMI_SYS_INVS_NO'].$post['LMI_SYS_TRANS_NO'].$post['LMI_SYS_TRANS_DATE'].$secret_key.$post['LMI_PAYER_PURSE'].$post['LMI_PAYER_WM']));
                                        if ($sha256 === $post['LMI_HASH']/* && (float)$post['LMI_PAYMENT_AMOUNT'] >= (float)$transaction->amount && (float)$post['LMI_PAYMENT_AMOUNT'] >= (float)$product->price*/) {
                                            $success = true;
                                        } else {
                                            // неверные данные
                                        }

                                        break;
                                }

                                if ($success) {
                                    // выставляем успешность оплаты
                                    DB::table('payments')
                                        ->where('id', $transaction->id)
                                        ->update([
                                            'success' => 1,
                                            'updated_at' => date('Y-m-d H:i:s')
                                        ]);

                                    $details = json_decode($transaction->details, true);
                                    if (!$user_product) {
                                        // добавляем продукт к пользователю
                                        DB::table('product_user')
                                            ->insert([
                                                    'product_id' => $transaction->product_id,
                                                    'user_id' => $transaction->user_id,
                                                    'trade_account' => $details['trade_account'],
                                                    'broker' => $details['broker'],
                                                    'subscribe_date_until' => $product->price_by !== Dictionary::PRODUCT_PRICE_BY_FULL ? date('Y-m-d', strtotime('+1 ' . strtoupper($product->price_by))) : null,
                                                    'type' => Dictionary::PRODUCT_TYPE_REAL,
                                                    'created_at' => date('Y-m-d H:i:s'),
                                                    'updated_at' => date('Y-m-d H:i:s'),
                                                    'active' => 1
                                                ]
                                            );
                                    } else {
                                        $current_subscribe_date_until = date('Y-m-d') > $user_product->subscribe_date_until ?? $user_product->subscribe_date_until;
                                        // изменяем настройки продукта у пользователя
                                        DB::table('product_user')
                                            ->where('id', $user_product->id)
                                            ->update([
                                                'active' => 1,
                                                'type' => Dictionary::PRODUCT_TYPE_REAL,
                                                'trade_account' => $details['trade_account'],
                                                'broker' => $details['broker'],
                                                'updated_at' => date('Y-m-d H:i:s'),
                                                'subscribe_date_until' => $product->price_by !== Dictionary::PRODUCT_PRICE_BY_FULL ? date('Y-m-d', strtotime('+1 ' . strtoupper($product->price_by), strtotime($current_subscribe_date_until))) : null
                                            ]);
                                    }
                                }

                                DB::table('payment_answer_queue')
                                    ->where('id', $payment->id)
                                    ->update([
                                        'active' => 0,
                                        'updated_at' => date('Y-m-d H:i:s')
                                    ]);
                            } else {
                                // нет транзакции такой
                            }
                        }
                    } else {
                        // пустой POST
                    }
                } else {
                    // пустой POST
                }
            }
        }
    }
}
