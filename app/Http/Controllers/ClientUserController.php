<?php

namespace App\Http\Controllers;

use App\Models\ClientUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\Client;
use App\Models\UserAssignedFolder;
use Illuminate\Support\Facades\Auth;
use App\Models\Folder;
class ClientUserController extends Controller
{


    //login client user
        public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $clientUser = ClientUser::where('email', $request->email)->first();

        if (!$clientUser || !Hash::check($request->password, $clientUser->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $clientUser->createToken('client-token')->plainTextToken;
        return response()->json([
            'clientUser' => $clientUser,
            'token' => $token,
        ]);
    }


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



    public function usersByClientAuthenticated()
{
    $clientUser = Auth::guard('sanctum')->user();
    $clientId = $clientUser->client_id;

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
            'role'        => 'nullable|string'
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = ClientUser::create($validated);

        return response()->json([
            'message' => 'Client user created successfully',
            'data' => $user
        ], 201);
    }



    //logout client user
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
    public function getAuthenticatedClient(Request $request)
    {
        return response()->json($request->user());
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
            'role'        => 'nullable|string'
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

public function getAllClientUserFoldersWithContents()
{
    $clientUser = Auth::guard('sanctum')->user();

    $folderIds = UserAssignedFolder::where('client_user_id', $clientUser->id)
        ->pluck('folder_id');

    $rootFolders = Folder::whereIn('id', $folderIds)
        ->whereNull('parentId')
        ->get();

    $data = $rootFolders->map(function ($folder) {
        return $this->buildFolderTree($folder);
    });

    return response()->json($data, 200);
}

private function buildFolderTree(Folder $folder)
{
    return [
        'id' => $folder->id,
        'name' => $folder->name,
        'files' => $folder->files()
            ->get(['id', 'name', 'path', 'status', 'built_in_portal', 'created_at', 'updated_at']),
        'subfolders' => $folder->children()->get()->map(function ($subfolder) {
            return $this->buildFolderTree($subfolder);
        }),
    ];
}
    public function updateAuthenticated(Request $request)
    {
        $client = $request->user();

        $request->validate([
            'email' => 'sometimes|email|unique:client_users,email,' . $client->id,
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
            'message' => 'Client user updated successfully',
            'client' => $client
        ]);
    }



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
