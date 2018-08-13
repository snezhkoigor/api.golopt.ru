<?php

namespace App\Console\Commands;

use App\Console\Parsers\ForwardPointParser;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GetForwardPointsFromFTP extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'getForwardPointsFromFTP';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get forward points files from CME FTP website';

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
        $disk = Storage::disk('public');
        $path_prefix = $disk->getDriver()->getAdapter()->getPathPrefix();
        $date = date('Ymd');

        $contents = file_get_contents('http://bulatlab.ru/forwardpoint/' . $date . '.TXT');
        
        if ($contents)
        {
            $contents_array = explode("/n", $contents);
            var_dump($contents_array);die;
        }
        
//         $conn_id = ftp_connect('http://bulatlab.ru//forwardpoint/');
//         $login_result = ftp_login($conn_id, '', '');
//         ftp_pasv($conn_id, true);
//         $contents = ftp_nlist($conn_id, '');

        // установка соединения
        // $conn_id = ftp_connect(config('cme_ftp.url'));
        // вход с именем пользователя и паролем
        // $login_result = ftp_login($conn_id, config('cme_ftp.user'), '');

        // if ($login_result == true) {
        //    ftp_pasv($conn_id, true);

            // получить содержимое текущей директории
        //     $contents = ftp_nlist($conn_id, config('cme_ftp.ftp_folder') . '/');

        //     if (!empty($contents)) {
        //         $last_file = array_pop($contents);
        //         $last_file = str_replace(config('cme_ftp.ftp_folder') . '/', '', $last_file);

        //         $name_arr = explode('-', $last_file);

        //         $disk->makeDirectory(config('cme_ftp.save_path') . '/' . $name_arr[1] . '/');

                // попытка скачать и распаковать архив
        //         $local_file = config('cme_ftp.save_path') . '/' . $name_arr[1] . '/' . $last_file;
        //         $ftp_file = 'ftp://' . config('cme_ftp.url') . '/' . config('cme_ftp.ftp_folder') . '/' . $last_file;

        //         if (!$disk->has($local_file)) {
        //             if (copy($ftp_file, $path_prefix . $local_file)) {

        //             } else {
        //                 Log::warning('Файл не смогли скопировать.', [ 'file' => $local_file ]);
        //             }
        //         } else {
        //             Log::warning('Файл уже скачан.', [ 'file' => $local_file ]);
        //         }

        //         if ($local_file) {
        //             $forward_point = new ForwardPointParser();

        //             $forward_point->parse($local_file);
        //         }
        //     }
        // }
    }
}
