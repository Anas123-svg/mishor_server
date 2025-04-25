<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class ClientController extends Controller
{
    // Sign up
    public function signUp(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string',
                'email' => 'required|email|unique:clients',
                'password' => 'required|string|min:6|confirmed',
            ]);

            $client = Client::create([
                'name' => $request->name,
                'surname' => $request->surname,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'country' => $request->country,
                'city' => $request->city,
                'notes' => $request->notes,
                'profileImage' => $request->profileImage,
            ]);

            $token = $client->createToken('client-token')->plainTextToken;

            return response()->json([
                'client' => $client,
                'token' => $token,
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (QueryException $e) {
            Log::error('Database error during client signup', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Database error'], 500);
        } catch (\Exception $e) {
            Log::error('Unexpected error during client signup', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Unexpected error'], 500);
        }
    }

    // Login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $client = Client::where('email', $request->email)->first();

        if (!$client || !Hash::check($request->password, $client->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $client->createToken('client-token')->plainTextToken;

        return response()->json([
            'client' => $client,
            'token' => $token,
        ]);
    }

    // Get all clients
    public function index()
    {
        return Client::all();
    }

    // Get client by ID
    public function show($id)
    {
        $client = Client::findOrFail($id);
        return response()->json($client);
    }

    // Get authenticated client by token
    public function getAuthenticatedClient(Request $request)
    {
        return response()->json($request->user());
    }

    // Update client by ID
    public function update(Request $request, $id)
    {
        $client = Client::findOrFail($id);

        $request->validate([
            'email' => 'sometimes|email|unique:clients,email,' . $client->id,
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $data = $request->only([
            'name', 'surname', 'email', 'phone', 'country', 'city', 'notes', 'profileImage'
        ]);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $client->update($data);

        return response()->json([
            'message' => 'Client updated successfully',
            'client' => $client
        ]);
    }

    // Update authenticated client
    public function updateAuthenticated(Request $request)
    {
        $client = $request->user();

        $request->validate([
            'email' => 'sometimes|email|unique:clients,email,' . $client->id,
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $data = $request->only([
            'name', 'surname', 'email', 'phone', 'country', 'city', 'notes', 'profileImage'
        ]);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $client->update($data);

        return response()->json([
            'message' => 'Client updated successfully',
            'client' => $client
        ]);
    }

    // Delete client
    public function destroy($id)
    {
        $client = Client::findOrFail($id);
        $client->delete();

        return response()->json(['message' => 'Client deleted successfully']);
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

}
