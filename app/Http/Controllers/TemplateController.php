<?php

namespace App\Http\Controllers;

use App\Models\Template;
use Illuminate\Http\Request;

class TemplateController extends Controller
{
    // Get all templates
    public function index()
    {
        return response()->json(Template::all(), 200);
    }

    // Get template by ID
    public function show($id)
    {
        $template = Template::find($id);
        if (!$template) {
            return response()->json(['message' => 'Template not found'], 404);
        }
        return response()->json($template, 200);
    }

    //  Create a new template
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'template' => 'required|string',
        ]);

        $template = Template::create($request->only(['name', 'template']));

        return response()->json([
            'message' => 'Template created successfully',
            'data' => $template,
        ], 201);
    }

    // Update existing template
    public function update(Request $request, $id)
    {
        $template = Template::find($id);

        if (!$template) {
            return response()->json(['message' => 'Template not found'], 404);
        }

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'template' => 'sometimes|string',
        ]);

        $template->update($request->only(['name', 'template']));

        return response()->json([
            'message' => 'Template updated successfully',
            'data' => $template,
        ], 200);
    }

    // Delete template
    public function destroy($id)
    {
        $template = Template::find($id);

        if (!$template) {
            return response()->json(['message' => 'Template not found'], 404);
        }

        $template->delete();

        return response()->json(['message' => 'Template deleted successfully'], 200);
    }
}
