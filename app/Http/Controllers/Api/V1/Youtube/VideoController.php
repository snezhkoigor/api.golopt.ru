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

        $data = (array)$youtube->listChannelVideos('UCaCmSeb1GwQ9OHTXkpXAJVg', 4, 'date');

        $data = json_decode(json_encode($data), True);

        if ($data) {
        	foreach ($data as $id => $item) {
        		$data[$id]['src'] = 'https://www.youtube.com/embed/' . $item['id']['videoId'];
	        }
        }

        return response()->json([
            'status' => true,
            'message' => null,
            'data' => $data
        ]);
    }
}