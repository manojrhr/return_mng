<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\StoreRegisterRequest;
use App\Models\Role;

class StoreController extends Controller
{
    public function register(StoreRegisterRequest $request)
    {
        // Create Store
        $store = Store::create([
            'store_name' => $request->store_name,
            'store_code' => $request->store_code,
            'point_of_contact' => $request->point_of_contact,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address
        ]);

        $storeUserRole = Role::where('name', 'store_user')->first();

        if (! $storeUserRole) {
            return response()->json([
                'status' => false,
                'message' => 'Store user role not found. Please contact admin.'
            ], 500);
        }

        // Create Store User
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $storeUserRole->id,
            'store_id' => $store->id
        ]);

        $token = $user->createToken('store-token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Store registered successfully',
            'store' => $store,
            'token' => $token
        ], 201);
    }
}
