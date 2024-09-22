<?php

namespace App\Http\Controllers;

use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductsController extends Controller
{
    public function index(Request $request)
    {
        $response['message'] ="Product List";
        $response['data'] = Products::get();
        return response()->json($response, 200);
    }

    public function detail($product)
    {
        $productdata=Products::find($product);
        if (!$productdata) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $response['message'] ="Product Detail";
        $response['data'] = $productdata;

        return response()->json($response, 200);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:products,name',
            'price' => 'required|numeric',
            'stock' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $create = Products::create([
            'name' => $request->name,
            'price' => $request->price,
            'stock' => $request->stock,
            'sold' => 0,
            'created_at' =>now(),
            'updated_at' =>now(),
        ]);

        $response['message'] ="Product created successfully";
        $response['data'] = $create;
        return response()->json($response, 200);
    }
    
    public function update(Request $request, $product)
    {
        $productdata = Products::find($product);
    
        if (!$productdata) {
            return response()->json(['message' => 'Product not found'], 404);
        }
    
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:products,name,' . $product,
            'price' => 'required|numeric',
            'stock' => 'required|numeric',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        $productdata->update([
            'name' => $request->name,
            'price' => $request->price,
            'stock' => $request->stock,
            'updated_at' => now(),
        ]);
    
        return response()->json([
            'message' => 'Product updated successfully',
            'data' => $productdata,
        ], 200);
    }

    public function delete($product)
    {
        $productdata = Products::find($product);
    
        if (!$productdata) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $productdata->delete();
        return response()->json([
            'message' => 'Product deleted successfully',
            'data' => $productdata,
        ], 200);
    }
}
