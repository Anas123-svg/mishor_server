<?php

namespace App\Http\Controllers;

use App\Models\ClientUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\Client;

class ClientUserController extends Controller
{


    public function usersByClient($clientId)
{
    $client = Client::findOrFail($clientId);

    $users = ClientUser::where('client_id', $clientId)->get();

    return response()->json([
        'message' => 'Users fetched successfully',
        'client' => [
            'name' => $client->name,
            'surname' => $client->surname,
            'email' => $client->email,
        ],
        'data' => $users
    ]);
}


    public function index()
    {
        $users = ClientUser::with('client')->latest()->get();
        return response()->json($users);
    }

    /**
     * Store a newly created client user.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id'   => 'nullable|exists:clients,id',
            'name'        => 'required|string|max:255',
            'surname'     => 'nullable|string|max:255',
            'email'       => 'required|email|unique:client_users,email',
            'password'    => 'required|min:6',
            'phone'       => 'nullable|string|max:50',
            'country'     => 'nullable|string|max:100',
            'city'        => 'nullable|string|max:100',
            'notes'       => 'nullable|string',
            'profileImage'=> 'nullable|string',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = ClientUser::create($validated);

        return response()->json([
            'message' => 'Client user created successfully',
            'data' => $user
        ], 201);
    }

    /**
     * Show a specific client user.
     */
    public function show($id)
    {
        $user = ClientUser::with('client')->findOrFail($id);
        return response()->json($user);
    }

    /**
     * Update a client user.
     */
    public function update(Request $request, $id)
    {
        $user = ClientUser::findOrFail($id);

        $validated = $request->validate([
            'client_id'   => 'nullable|exists:clients,id',
            'name'        => 'required|string|max:255',
            'surname'     => 'nullable|string|max:255',
            'email'       => [
                'required',
                'email',
                Rule::unique('client_users')->ignore($user->id)
            ],
            'password'    => 'nullable|min:6',
            'phone'       => 'nullable|string|max:50',
            'country'     => 'nullable|string|max:100',
            'city'        => 'nullable|string|max:100',
            'notes'       => 'nullable|string',
            'profileImage'=> 'nullable|string',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return response()->json([
            'message' => 'Client user updated successfully',
            'data' => $user
        ]);
    }

    /**
     * Remove a client user.
     */
    public function destroy($id)
    {
        $user = ClientUser::findOrFail($id);
        $user->delete();

        return response()->json([
            'message' => 'Client user deleted successfully'
        ]);
    }
}
