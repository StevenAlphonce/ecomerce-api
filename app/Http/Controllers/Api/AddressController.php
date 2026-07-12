<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AddressRequest;
use App\Models\Address;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {
        $addresses = Address::where('user_id', $request->user()->id)->latest()->get();

        return response()->json([
            'status' => 'success',
            'data' => $addresses,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AddressRequest $request)
    {
        $data = $request->validated();

        $data['user_id'] = $request->user()->id;

        Address::create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Address created successfuly',
        ]);
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
    public function update(AddressRequest $request, string $id)
    {
        $address = Address::findOrfail($id);

        // Ensure the authenticated user owns the address
        if ($address->user_id !== $request->user()->id) {

            return response()->json(
                [
                    'status' => 'fail',
                    'message' => 'Unathorized'
                ],
                403
            );
        }

        $data = $request->validated();

        // Prevent changing the owner
        unset($data['user_id']);

        $address->update($data);

        return response()->json([
            'status' => 'sucuss',
            'message' => 'Address updated successfully'
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AddressRequest $request, string $id)
    {

        $address = Address::where('id', $id,)->where('user_id', $request->user()->id)->findOrfail($id);

        // Ensure the authenticated user owns the address
        if (!$address) {

            return response()->json(
                [
                    'status' => 'fail',
                    'message' => 'Unathorized'
                ],
                403
            );
        }

        $address->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Address deletade successfuly'

        ], 201);
    }
}
