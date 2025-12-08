<?php

namespace App\Http\Controllers;

use App\Models\UserAssignedFolder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserAssignedFolderController extends Controller
{
    /**
     * Get all folders by client_user_id
     */
public function getByClientUser($client_user_id)
{
    $folders = UserAssignedFolder::with('folder')
        ->where('client_user_id', $client_user_id)
        ->get();

    if ($folders->isEmpty()) {
        return response()->json([
            'status' => false,
            'message' => 'No folders found for this user',
            'data' => []
        ]);
    }

    // Add folder_name field
    $folders = $folders->map(function ($item) {
        return [
            'id'             => $item->id,
            'client_user_id' => $item->client_user_id,
            'folder_id'      => $item->folder_id,
            'created_at'     => $item->created_at,
            'updated_at'     => $item->updated_at,
            'folder_name'    => $item->folder?->name,   
           // 'folder'         => $item->folder           
        ];
    });

    return response()->json([
        'status' => true,
        'data'   => $folders
    ]);
}


    /**
     * Store/assign folders to client user
     * Body expects:
     * client_user_id
     * folders => [1,2,3]
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'client_user_id' => 'required|exists:client_users,id',
            'folders'        => 'required|array',
            'folders.*'      => 'exists:folders,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $clientUserId = $request->client_user_id;
        $folderIds = $request->folders;

        $created = [];

        foreach ($folderIds as $folderId) {

            // Avoid duplicates
            $exists = UserAssignedFolder::where('client_user_id', $clientUserId)
                ->where('folder_id', $folderId)
                ->exists();

            if (!$exists) {
                $created[] = UserAssignedFolder::create([
                    'client_user_id' => $clientUserId,
                    'folder_id'      => $folderId,
                ]);
            }
        }

        return response()->json([
            'status'  => true,
            'message' => 'Folders assigned successfully',
            'data'    => $created
        ]);
    }

    /**
     * Update assignment (replace all folders of a user)
     */
    public function update(Request $request, $client_user_id)
    {
        $validator = Validator::make($request->all(), [
            'folders'   => 'required|array',
            'folders.*' => 'exists:folders,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Delete old ones
        UserAssignedFolder::where('client_user_id', $client_user_id)->delete();

        // Insert new ones
        foreach ($request->folders as $folderId) {
            UserAssignedFolder::create([
                'client_user_id' => $client_user_id,
                'folder_id'      => $folderId,
            ]);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Folders updated successfully'
        ]);
    }

    /**
     * Delete a specific assignment
     */
    public function destroy($id)
    {
        $record = UserAssignedFolder::find($id);

        if (!$record) {
            return response()->json([
                'status'  => false,
                'message' => 'Record not found'
            ], 404);
        }

        $record->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Folder assignment deleted'
        ]);
    }
}
