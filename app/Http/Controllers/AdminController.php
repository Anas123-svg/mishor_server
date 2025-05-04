<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Exception;

class AdminController extends Controller
{
    // SignUp
    public function signUp(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string',
                'email' => 'required|email|unique:admins',
                'password' => 'required|string|min:6',
            ]);
    
            $admin = Admin::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'profileImage' => $request->profileImage,
            ]);
    
            $token = $admin->createToken('admin-token')->plainTextToken;
    
            return response()->json([
                'token' => $token,
                'admin' => $admin
            ], 201);
    
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
    
        } catch (QueryException $e) {
            Log::error('Database error during admin signup', ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Database error. Please try again later.'
            ], 500);
    
        } catch (Exception $e) {
            Log::error('Unexpected error during admin signup', ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Something went wrong. Please try again.'
            ], 500);
        }
    }
    

    // Login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $admin = Admin::where('email', $request->email)->first();

        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $admin->createToken('admin-token')->plainTextToken;

        return response()->json([
            'admin' => $admin,
            'token' => $token,
        ]);
    }

    // Get all admins
    public function index()
    {
        return Admin::all();
    }

    // Get admin by ID
    public function show($id)
    {
        $admin = Admin::findOrFail($id);
        return response()->json($admin);
    }

    // Get admin by token
    public function getAuthenticatedAdmin(Request $request)
    {
        return response()->json($request->user());
    }

    // Update admin
    public function update(Request $request, $id)
    {
        $admin = Admin::findOrFail($id);

        $admin->update($request->only([
            'name', 'email', 'phone', 'profileImage'
        ]));

        return response()->json($admin);
    }

    // Delete admin
    public function destroy($id)
    {
        $admin = Admin::findOrFail($id);
        $admin->delete();

        return response()->json(['message' => 'Admin deleted successfully']);
    }
    //update by token


    public function updateAuthenticated(Request $request)
    {
        try {
            $admin = $request->user(); 
    
            $request->validate([
                'name' => 'sometimes|string',
                'email' => 'sometimes|email|unique:admins,email,' . $admin->id,
                'phone' => 'nullable|string',
                'profileImage' => 'nullable|string',
                'password' => 'nullable|string|min:6|confirmed' 
            ]);
    
            $data = $request->only([
                'name',
                'email',
                'phone',
                'profileImage'
            ]);
    
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }
    
            $admin->update($data);
    
            return response()->json([
                'message' => 'Admin updated successfully',
                'admin' => $admin
            ]);
    
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update admin',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    

    public function logout(Request $request)
{
    try {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Successfully logged out.'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Logout failed.',
            'error' => $e->getMessage()
        ], 500);
    }
}



// Change password
public function changePassword(Request $request)
{
    try {
        $admin = $request->user();

        $request->validate([
            'old_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        if (!Hash::check($request->old_password, $admin->password)) {
            return response()->json([
                'message' => 'Old password does not match'
            ], 400);
        }

        $admin->password = Hash::make($request->new_password);
        $admin->save();

        return response()->json([
            'message' => 'Password updated successfully'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Failed to change password',
            'error' => $e->getMessage()
        ], 500);
    }
}

}
