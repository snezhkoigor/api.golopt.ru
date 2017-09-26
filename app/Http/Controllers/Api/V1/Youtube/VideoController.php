<?php
/**
 * Created by PhpStorm.
 * User: igorsnezko
 * Date: 01.08.17
 * Time: 14:05
 */

namespace App\Http\Controllers\Api\V1\Youtube;

use Alaouy\Youtube\Youtube;
use App\Http\Controllers\Controller;

class VideoController extends Controller
{
    public function getList()
    {
        $youtube = new Youtube(config('youtube.KEY'));
        return response()->json([
            'status' => true,
            'message' => null,
            'data' => $youtube->listChannelVideos('UCaCmSeb1GwQ9OHTXkpXAJVg', 50)
        ]);
    }
}