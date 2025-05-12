<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator; 

class AdminController extends Controller
{
    public function create(Request $request)
    {
       
        // 🔹 Validation
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        // 🔹 Create admin
        $admin = Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Admin created successfully.',
            'admin' => $admin,
        ]);
    }

   
    /**
     * Store a newly created resource in storage.
     */
    public function login(Request $request)
{
    $email = $request->input('email');
    $password = $request->input('password');

    $admin = Admin::where('email', $email)->first();

    if (!$admin) {
        return response()->json([
            'status' => false,
            'message' => 'Invalid email or password'
        ], 401);
    }

    if (!Hash::check($password, $admin->password)) {
        return response()->json([
            'status' => false,
            'message' => 'Invalid email or password'
        ], 401);
    }

    // ✅ If you want to generate token (optional):
    $token = $admin->createToken('API Token')->plainTextToken;

    return response()->json([
        'status' => true,
        'message' => 'Login successful',
        'admin' => $admin,
        'token' => $token
    ]);
}

    /**
     * Display the specified resource.
     */
    public function show(Admin $admin)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Admin $admin)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Admin $admin)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Admin $admin)
    {
        //
    }
}
