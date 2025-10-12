<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\File;

class FileController extends Controller
{
    /**
     * Update a file by ID.
     */
    public function update(Request $request, $id)
    {
        $file = File::find($id);

        if (!$file) {
            return response()->json([
                'message' => 'File not found.'
            ], 404);
        }

        $validatedData = $request->validate([
            'name' => 'sometimes|string|max:255',
            'path' => 'sometimes|string|max:1024',
            'folderId' => 'sometimes|integer|exists:folders,id',
            'clientId' => 'sometimes|integer|exists:clients,id',
            'status' => 'nullable|string|max:255',
            'built_in_portal' => 'nullable|boolean',
            'template' => 'nullable|string|max:255',
        ]);

        $file->update($validatedData);

        return response()->json([
            'message' => 'File updated successfully.',
            'file' => $file
        ]);
    }
}
