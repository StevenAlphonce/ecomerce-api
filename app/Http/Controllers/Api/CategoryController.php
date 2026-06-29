<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CategoryRequest;
use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::all();

        return response()->json([
            'status' => 'Success',
            'data' => $categories
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryRequest $request)
    {

        $data = $request->validated();

        $data['name'] = $request->name;
        $data['slug'] = Str::slug($request->name);
        $data['description'] = $request->description;

        Category::save($data);

        return response()->json([
            'status' => 'Success',
            'massage' => 'Categery added sucessfully'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryRequest $request, string $id)
    {
        $category = Category::Find($id);

        if (!$category) {

            return response()->json(
                [
                    'status' => 'fail',
                    'message' => 'No category found'
                ],
                404
            );
        }

        $data = $request->validated();

        $data['name'] = $request->name;
        $data['slug'] = Str::slug($request->name);
        $data['description'] = $request->description;

        $category->update($data);

        return response()->json(
            [
                'status' => 'success',
                'message' => 'category updated successfully'
            ],
            200
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Category::find($id);

        if (!$category) {

            return response()->json(
                [
                    'status' => 'fail',
                    'message' => 'No category found'
                ],
                404
            );
        }

        $category->delete();

        return response()->json(
            [
                'status' => 'success',
                'message' => 'Category deleted sucessfully'
            ]
        );
    }
}
