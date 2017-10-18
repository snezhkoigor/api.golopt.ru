<?php
/**
 * Created by PhpStorm.
 * User: igorsnezko
 * Date: 09.08.17
 * Time: 21:26
 */

namespace App\Http\Controllers\Api\V1\Product;


use App\Http\Controllers\Controller;
use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use JWTAuth;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.auth', ['except' => ['pricingTable']]);
    }

    public function rules()
    {
        return [
            'name' => 'required',
            'path' => 'required',
            'price' => 'required|numeric'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Enter product name.',
            'path.required' => 'Enter product download path.',
            'price.required' => 'Enter product price.',
            'price.numeric' => 'Product price must be a numeric.',
        ];
    }

    public function pricingTable()
    {
        $result = [];
        $jwt_user = JWTAuth::getToken() ? JWTAuth::toUser(JWTAuth::getToken()) : null;
        $products = $jwt_user ? Product::with(['users' => function($query) use ($jwt_user) { $query->where('users.id', (int)$jwt_user->id); }])->get() : Product::all();

        foreach ($products as $product_key => $product) {
            $result[$product['group']][$product_key] = $product;
            $result[$product['group']][$product_key]['functional'] = null !== $product['functional'] ? json_decode($product['functional'], true) : null;
        }

        return response()->json([
            'status' => true,
            'message' => null,
            'data' => $result
        ]);
    }

    public function index(Request $request)
    {
        $result = [];
        $jwt_user = JWTAuth::getToken() ? JWTAuth::toUser(JWTAuth::getToken()) : null;
        $products = $jwt_user ? Product::with(['users'])->get() : Product::all();

        if ($jwt_user) {
            foreach ($products as $product_key => $product) {
                if (count($product->users)) {
                    foreach ($product->users as $user_key => $user) {
                        if ($jwt_user->id !== (int)$user['id']) {
                            unset($products[$product_key]['users'][$user_key]);
                        } else {
                            $products[$product_key]['users'] = $user;
                        }
                    }
                }
            }
        }

        if ($request->get('grouped') === true) {
            foreach ($products as $product_key => $product) {
                $result[$product['group']][$product_key] = $product;
            }
        } else {
            $result = $products;
        }

        return response()->json([
            'status' => true,
            'message' => null,
            'data' => $result
        ]);
    }

    public function save(Request $request, $id = null)
    {
        $validator = Validator::make($request->all(), $this->rules(), $this->messages());

        if ($validator->fails() === false) {
            $product = ($id !== null) ? Product::find($id)->first() : new Product();

            $product->name = $request->get('name');
            $product->path = $request->get('path');
            $product->description = $request->get('description');
            $product->price = $request->get('price');

            if (!empty($request->get('price_by'))) {
                $product->price_by = $request->get('price_by');
            }
            if ((int)$request->get('demo_access_days')) {
                $product->demo_access_days = $request->get('demo_access_days');
            }
            $product->active = true;
            $product->save();

            if ($product) {
                return response()->json([
                    'status' => true,
                    'message' => null,
                    'data' => Product::all()
                ]);
            }
        }

        return response()->json([
            'status' => false,
            'message' => $validator->errors()->getMessages(),
            'data' => null
        ]);
    }
}