<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use App\Models\Client;
use App\Mail\FileUploadMail;
use Illuminate\Support\Facades\Mail;
use App\Models\Template;

class FolderController extends Controller
{
    /**
     * Create a new folder.
     */
public function createFolder(Request $request) 
{
    $isClientUser = $request->boolean('client_user'); // defaults to false if not present

    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'parentId' => 'nullable|exists:folders,id',
        'client_user' => 'nullable|boolean',
    ]);

    $folderData = [
        'name' => $validated['name'],
        'parentId' => $validated['parentId'] ?? null,
    ];

    if (!$isClientUser) {
        $client = $request->user();
        $folderData['clientId'] = $client->id;
    }

    $folder = Folder::create($folderData);

    return response()->json([
        'message' => 'Folder created successfully.',
        'folder' => $folder,
    ], 201);
}


    /**
     * Upload a file inside a folder.
     */
public function uploadFile(Request $request)
{
    $isClientUser = $request->boolean('client_user'); // defaults false if missing

    $validated = $request->validate([
        'folderId' => 'required|exists:folders,id',
        'name' => 'required|string|max:255',
        'path' => 'required|string|max:1000',
        'client_user' => 'nullable|boolean',
    ]);

    // If NOT client_user, enforce token client ownership
    if (!$isClientUser) {
        $client = $request->user(); // sanctum token user

        $folder = Folder::where('id', $validated['folderId'])
            ->where('clientId', $client->id)
            ->first();

        if (!$folder) {
            throw ValidationException::withMessages([
                'folderId' => 'Folder not found or you do not have permission.'
            ]);
        }
    } else {
        $folder = Folder::find($validated['folderId']);

        if (!$folder) {
            throw ValidationException::withMessages([
                'folderId' => 'Folder not found.'
            ]);
        }
    }

    $fileData = [
        'name' => $validated['name'],
        'path' => $validated['path'],
        'folderId' => $folder->id,
    ];

    // Attach clientId only when NOT client_user
    if (!$isClientUser) {
        $fileData['clientId'] = $request->user()->id;
    }

    $file = File::create($fileData);

    return response()->json([
        'message' => 'File saved successfully.',
        'file' => $file,
    ], 201);
}    /**
     * Get a folder with all its nested folders and files recursively.
     */
    public function getFolderContents($folderId)
    {
        $client = Auth::guard('sanctum')->user();

        $folder = Folder::where('id', $folderId)->first();

        if (!$folder) {
            return response()->json([
                'message' => 'Folder not found.',
            ], 404);
        }

        $data = $this->buildFolderTree($folder);

        return response()->json($data, 200);
    }

    private function buildFolderTree(Folder $folder)
    {
        return [
            'id' => $folder->id,
            'name' => $folder->name,
            'files' => $folder->files()->get(['id', 'name', 'path', 'status', 'built_in_portal', 'created_at', 'updated_at']),
            'subfolders' => $folder->children()->get()->map(function ($subfolder) {
                return $this->buildFolderTree($subfolder);
            }),
        ];
    }
    public function getAllFoldersWithContents()
    {
        $client = Auth::guard('sanctum')->user();

        $rootFolders = Folder::where('clientId', $client->id)
            ->whereNull('parentId')
            ->get();

        $data = $rootFolders->map(function ($folder) {
            return $this->buildFolderTree($folder);
        });

        return response()->json($data, 200);
    }




    public function createFolderByClientId(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parentId' => 'nullable|exists:folders,id',
            'clientId' => 'required|exists:clients,id',
        ]);

        $folder = Folder::create([
            'name' => $request->name,
            'parentId' => $request->parentId,
            'clientId' => $request->clientId,
        ]);

        return response()->json([
            'message' => 'Folder created successfully using clientId.',
            'folder' => $folder,
        ], 201);
    }

public function uploadFileByClientId(Request $request)
{
    $request->validate([
        'folderId'        => 'required|exists:folders,id',
        'name'            => 'required|string|max:255',
        'path'            => 'nullable|string|max:1000',
        'clientId'        => 'required|exists:clients,id',
        'status'          => 'nullable|string|max:255',
        'built_in_portal' => 'nullable|boolean',
        'templateId'      => 'nullable', // can be int or array
    ]);

    $client = Client::find($request->clientId);

    $folder = Folder::where('id', $request->folderId)
        ->where('clientId', $request->clientId)
        ->first();

    if (!$folder) {
        throw ValidationException::withMessages([
            'folderId' => 'Folder not found or not owned by this client.'
        ]);
    }

    // Normalize templateId into an array
    $templateIds = [];

    if ($request->has('templateId')) {
        if (is_array($request->templateId)) {
            $templateIds = $request->templateId;
        } else {
            $templateIds = [$request->templateId];
        }
    }

    $createdFiles = [];

    // If templateIds exist → create files for each template
    if (!empty($templateIds)) {

        foreach ($templateIds as $templateId) {
            $template = Template::find($templateId);
            $templateContent = $template?->template;

            $builtInPortal = $request->boolean('built_in_portal', true);

            $createdFiles[] = File::create([
                'name'             => $template->name,
                'path'             => $request->path,
                'folderId'         => $folder->id,
                'clientId'         => $request->clientId,
                'status'           => $request->status,
                'built_in_portal'  => $builtInPortal,
                'template'         => $templateContent,
                'templateId'       => $templateId,
            ]);
        }

    } else {
        // No template → single file creation
        $builtInPortal = false;

        $createdFiles[] = File::create([
            'name'             => $request->name,
            'path'             => $request->path,
            'folderId'         => $folder->id,
            'clientId'         => $request->clientId,
            'status'           => $request->status,
            'built_in_portal'  => $builtInPortal,
            'template'         => null,
        ]);
    }

    // Send email notification
    Mail::to($client->email)->send(new FileUploadMail($client));

    return response()->json([
        'message' => 'File(s) saved successfully using clientId.',
        'files'   => $createdFiles,
    ], 201);
}


    public function getAllFoldersWithContentsByClientId($id)
    {
        $clientId = $id;

        if (!\App\Models\Client::where('id', $clientId)->exists()) {
            return response()->json(['message' => 'Client not found.'], 404);
        }

        $rootFolders = Folder::where('clientId', $clientId)
            ->whereNull('parentId')
            ->get();

        $data = $rootFolders->map(function ($folder) {
            return $this->buildFolderTree($folder);
        });

        return response()->json($data, 200);
    }
    public function deleteFile($id)
    {
        $file = File::find($id);

        if (!$file) {
            return response()->json(['message' => 'File not found.'], 404);
        }
        $file->delete();

        return response()->json(['message' => 'File deleted successfully.'], 200);
    }

    public function deleteFolder($id)
    {
        $folder = Folder::find($id);

        if (!$folder) {
            return response()->json(['message' => 'Folder not found.'], 404);
        }

        $this->deleteFolderRecursively($folder);

        return response()->json(['message' => 'Folder and all its contents deleted successfully.'], 200);
    }

    private function deleteFolderRecursively(Folder $folder)
    {
        foreach ($folder->files as $file) {
            $file->delete();
        }

        foreach ($folder->children as $childFolder) {
            $this->deleteFolderRecursively($childFolder);
        }

        $folder->delete();
    }

}