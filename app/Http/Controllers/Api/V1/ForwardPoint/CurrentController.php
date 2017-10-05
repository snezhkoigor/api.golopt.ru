<?php
/**
 * Created by PhpStorm.
 * User: igorsnezko
 * Date: 01.08.17
 * Time: 14:03
 */

namespace App\Http\Controllers\Api\V1\ForwardPoint;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CurrentController extends Controller
{
    public function index(Request $request)
    {
        $fp = DB::table('forward_points')
            ->select('forward_points.name', 'forward_points.fp', DB::raw('UNIX_TIMESTAMP(forward_points.updated_at) as updated_at'))
            ->where('forward_points.date', '=', ($request->get('date') ? $request->get('date') : date('Y-m-d')))
            ->get();

        if ($fp) {
            return response()->json([
                'status' => true,
                'message' => null,
                'data' => $fp
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'No forward points.',
            'data' => null
        ], 422);
    }
}