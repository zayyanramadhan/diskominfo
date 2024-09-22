<?php

namespace App\Http\Controllers;

use App\Models\Orders;
use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class OrdersController extends Controller
{
    public function index(Request $request)
    {
        $response['message'] ="Order List";
        $response['data'] = Orders::get();
        return response()->json($response, 200);
    }

    public function detail($orders)
    {
        $orderdata = Orders::with('products')->where('orders_id', $orders)->get();
        $response = [
            'message' => "Order Detail",
            'data' => [
                'id' => $orderdata[0]->orders_id,
                'products' => $orderdata->map(function ($orderItem) {
                    return [
                        'id' => $orderItem->products->id,
                        'name' => $orderItem->products->name,
                        'price' => $orderItem->products->price,
                        'quantity' => $orderItem->quantity,
                        'stock' => $orderItem->products->stock,
                        'sold' => $orderItem->products->sold,
                        'created_at' => $orderItem->products->created_at,
                        'updated_at' => $orderItem->products->updated_at,
                    ];
                }),
                'created_at' => $orderdata[0]->created_at,
                'updated_at' => $orderdata[0]->updated_at,
            ]
        ];

        return response()->json($response, 200);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'products' => 'required|array',
            'products.*.id' => 'required|numeric|exists:products,id|distinct',
            'products.*.quantity' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $errors = [];
        foreach ($request->products as $product) {
            $stock = Products::find($product['id'])->stock;
            if ($product['quantity'] > $stock) {
                $errors[] = "Quantity for product ID {$product['id']} must be less than stock ({$stock}).";
            }
        }

        if (!empty($errors)) {
            return response()->json(['errors' => $errors], 422);
        }

        $lastOrders = Orders::max('orders_id');
        $setid=1;
        if (!$lastOrders) {
            $setid=1;
        }else {
            $setid=($lastOrders+1);
        }

        foreach ($request->products as $key => $value) {
            $create = Orders::create([
                'orders_id' => $setid,
                'products_id' => $value['id'],
                'quantity' => $value['quantity'],
                'created_at' =>now(),
                'updated_at' =>now(),
            ]);
            $productdata = Products::find($value['id']);
        
            if (!$productdata) {
                return response()->json(['message' => 'Product not found'], 404);
            }
            $productdata->update([
                'stock' => $productdata->stock - $value['quantity'],
                'sold' => $productdata->sold + $value['quantity'],
                'updated_at' => now(),
            ]);
        }
        
        $orderdata = Orders::with('products')->where('orders_id', $setid)->get();
        $response = [
            'message' => "Order created",
            'data' => [
                'id' => $orderdata[0]->orders_id,
                'products' => $orderdata->map(function ($orderItem) {
                    return [
                        'id' => $orderItem->products->id,
                        'name' => $orderItem->products->name,
                        'price' => $orderItem->products->price,
                        'quantity' => $orderItem->quantity,
                        'stock' => $orderItem->products->stock,
                        'sold' => $orderItem->products->sold,
                        'created_at' => $orderItem->products->created_at,
                        'updated_at' => $orderItem->products->updated_at,
                    ];
                }),
                'created_at' => $orderdata[0]->created_at,
                'updated_at' => $orderdata[0]->updated_at,
            ]
        ];

        return response()->json($response, 200);
    }

    public function update(Request $request, $orders)
    {
        $ordersdata = Orders::where('orders_id',$orders)->get();
        if (!isset($ordersdata[0])) {
            return response()->json(['message' => 'Order not found'], 404);
        }
    
        $validator = Validator::make($request->all(), [
            'products' => 'required|array',
            'products.*.id' => 'required|numeric|exists:products,id|distinct',
            'products.*.quantity' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $errors = [];
        foreach ($request->products as $product) {
            $stock = Products::find($product['id'])->stock;
            if ($product['quantity'] > $stock) {
                $errors[] = "Quantity for product ID {$product['id']} must be less than stock ({$stock}).";
            }
        }

        if (!empty($errors)) {
            return response()->json(['errors' => $errors], 422);
        }

        foreach ($ordersdata as $key => $value) {
            $productdata = Products::find($value['products_id']);
        
            if (!$productdata) {
                return response()->json(['message' => 'Product not found'], 404);
            }

            $productdata->update([
                'stock' => $productdata->stock + $value['quantity'],
                'sold' => $productdata->sold - $value['quantity'],
                'updated_at' => now(),
            ]);
        }

        $setid=$orders;
        Orders::where('orders_id', $setid)->delete();

        foreach ($request->products as $key => $value) {
            $create = Orders::create([
                'orders_id' => $setid,
                'products_id' => $value['id'],
                'quantity' => $value['quantity'],
                'created_at' =>$ordersdata[0]['created_at'],
                'updated_at' =>now(),
            ]);
            $productdata = Products::find($value['id']);
        
            if (!$productdata) {
                return response()->json(['message' => 'Product not found'], 404);
            }
            $productdata->update([
                'stock' => $productdata->stock - $value['quantity'],
                'sold' => $productdata->sold + $value['quantity'],
                'updated_at' => now(),
            ]);
        }
        
        $orderdata = Orders::with('products')->where('orders_id', $setid)->get();
        $response = [
            'message' => "Order updated",
            'data' => [
                'id' => $orderdata[0]->orders_id,
                'products' => $orderdata->map(function ($orderItem) {
                    return [
                        'id' => $orderItem->products->id,
                        'name' => $orderItem->products->name,
                        'price' => $orderItem->products->price,
                        'quantity' => $orderItem->quantity,
                        'stock' => $orderItem->products->stock,
                        'sold' => $orderItem->products->sold,
                        'created_at' => $orderItem->products->created_at,
                        'updated_at' => $orderItem->products->updated_at,
                    ];
                }),
                'created_at' => $orderdata[0]->created_at,
                'updated_at' => $orderdata[0]->updated_at,
            ]
        ];

        return response()->json($response, 200);
    }

    public function delete($orders)
    {
        $resorderdata = Orders::with('products')->where('orders_id', $orders)->get();
        $response = [
            'message' => "Order deleted successfully",
            'data' => [
                'id' => $resorderdata[0]->orders_id,
                'products' => $resorderdata->map(function ($orderItem) {
                    return [
                        'id' => $orderItem->products->id,
                        'name' => $orderItem->products->name,
                        'price' => $orderItem->products->price,
                        'quantity' => $orderItem->quantity,
                        'stock' => $orderItem->products->stock,
                        'sold' => $orderItem->products->sold,
                        'created_at' => $orderItem->products->created_at,
                        'updated_at' => $orderItem->products->updated_at,
                    ];
                }),
                'created_at' => $resorderdata[0]->created_at,
                'updated_at' => $resorderdata[0]->updated_at,
            ]
        ];
        
        if (!isset($resorderdata[0])) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        Orders::where('orders_id',$orders)->delete();
        return response()->json($response, 200);
    }
}
