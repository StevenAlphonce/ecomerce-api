<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with('category')->get();

        return response()->json([
            'status' => 'success',
            'data' => $products
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request)
    {
        $data = $request->validated();

        $data['slug'] = Str::slug($data['name']);

        $image_path = '';

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $image_name = Str::slug($request->image . '-' . time() . '-' . $image->getClientOriginalExtension());
            $image_path = $image->storeAs('product', $image_name, 'public');
        }

        $data['image'] = $image_path;

        Product::create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Product created successfully'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::find($id);

        return response()->json([
            'status' => 'sucess',
            'data' => $product
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, string $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Product not found'
            ]);
        }

        $data = $request->validated();

        $data['slug'] = Str::slug($data['name']);


        if ($request->hasFile('image')) {

            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            $image = $request->file('image');
            $image_name = Str::slug($request->image . '-' . time() . '-' . $image->getClientOriginalExtension());
            $data['image'] = $image->storeAs('product', $image_name, 'public');
        }

        $product->update($data);

        return response()->json([
            'status' => 'sucess',
            'message' => 'Product updated successfully'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Product not found'
            ]);
        }

        $product->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Product deleted sucessfully'
        ]);
    }
}
