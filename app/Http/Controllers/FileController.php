<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\File;
use App\Models\Folder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Exception;

class FileController extends Controller
{

    public function get($id)
    {
        $file = File::find($id);

        if (!$file) {
            return response()->json([
                'message' => 'File not found.'
            ], 404);
        }

        return response()->json($file);
    }
    /**
     * Update a file by ID.
     */
public function update(Request $request, $id)
{
    try {
        // Check if the file exists
        $file = File::find($id);
        if (!$file) {
            return response()->json([
                'success' => false,
                'message' => 'File not found.',
            ], 404);
        }

        // Validate incoming request data
        $validatedData = $request->validate([
            'name' => 'sometimes|string|max:255',
            'path' => 'sometimes|string|max:1024',
            'folderId' => 'sometimes|integer|exists:folders,id',
            'clientId' => 'sometimes|integer|exists:clients,id',
            'status' => 'nullable|string|max:255',
            'built_in_portal' => 'nullable|boolean',
            'template' => 'nullable|string|max:65535', // allow larger text content
        ]);

        // Extra validation: ensure folder belongs to the same client (if both are given)
        if (isset($validatedData['folderId']) && isset($validatedData['clientId'])) {
            $folder = Folder::where('id', $validatedData['folderId'])
                            ->where('clientId', $validatedData['clientId'])
                            ->first();

            if (!$folder) {
                return response()->json([
                    'success' => false,
                    'message' => 'Folder not found or not owned by this client.',
                ], 400);
            }
        }

        // Attempt to update the file
        $file->update($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'File updated successfully.',
            'file' => $file
        ], 200);

    } catch (ValidationException $e) {
        // Handles validation-specific exceptions
        return response()->json([
            'success' => false,
            'message' => 'Validation failed.',
            'errors' => $e->errors(),
        ], 422);

    } catch (ModelNotFoundException $e) {
        // Handles any related model not found
        return response()->json([
            'success' => false,
            'message' => 'Related model not found.',
        ], 404);

    } catch (QueryException $e) {
        // Handles SQL or database-related issues
        return response()->json([
            'success' => false,
            'message' => 'Database error occurred.',
            'error' => $e->getMessage(),
        ], 500);

    } catch (Exception $e) {
        // Handles all other unexpected exceptions
        return response()->json([
            'success' => false,
            'message' => 'An unexpected error occurred.',
            'error' => $e->getMessage(),
        ], 500);
    }
}
}
