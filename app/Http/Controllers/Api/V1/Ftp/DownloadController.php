<?php
/**
 * Created by PhpStorm.
 * User: igorsnezko
 * Date: 01.08.17
 * Time: 14:03
 */

namespace App\Http\Controllers\Api\V1\Ftp;

use App\Http\Controllers\Controller;

class DownloadController extends Controller
{
    public function index($folder, $file_name)
    {
    	return response()->file(storage_path($folder . '/' . $file_name));
    }
}