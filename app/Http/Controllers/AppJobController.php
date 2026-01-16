<?php

namespace App\Http\Controllers;

use App\Models\AppJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\JobReport;


class AppJobController extends Controller
{
    /**
     * List all jobs
     */
    public function index()
    {
        $jobs = AppJob::get();
        return response()->json($jobs);
    }
    /**
     * List all jobs
     */
public function updateTest(Request $request)
{
    // Log the incoming payload
    Log::info(
        'Job update request received',
        [
            'payload' => json_decode(
                json_encode($request->all(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
                true
            )
        ]
    );

    // Step 1: Find the job on server by server_id
    $job = AppJob::where('id', $request->server_id)->first(); // server_id from request maps to id on server

    if (!$job) {
        return response()->json([
            'status' => 'error',
            'message' => 'Job not found on server'
        ], 404);
    }

    // Step 2: Update job fields
    $job->update([
        'client_name' => $request->client_name ?? null,
        'job_title'   => $request->event_title ?? null,
        'notes'       => $request->notes ?? null,
        'on_site_date'=> $request->on_site_date ?? null,
        'on_site_time'=> $request->on_site_time ?? null,
        'due_on'      => $request->due_on ?? null,
        'status'      => $request->status ?? null,
        // add clientId if provided
        'clientId'    => $request->clientId ?? $job->clientId ?? null,
    ]);

    // Step 3: Loop through reports
    if ($request->has('reports')) {
        foreach ($request->reports as $reportData) {
            if (isset($reportData['server_id']) && $reportData['server_id']) {
                $report = JobReport::find($reportData['server_id']);
                if ($report) {
                    $report->update([
                        'report_name' => $reportData['report_name'],
                        'layout'      => $reportData['layout'],
                        'form_data'   => $reportData['form_data'] ?? [],
                        'job_id'      => $job->id,
                    ]);
                }
            } else {
                // Create a new report for this job
                JobReport::create([
                    'report_name' => $reportData['report_name'],
                    'layout'      => $reportData['layout'],
                    'form_data'   => $reportData['form_data'] ?? [],
                    'job_id'      => $job->id,
                ]);
            }
        }
    }
    $reportsArray = $job->reports->map(function ($report) use ($job) {
        return [
            'server_id'     => $report->id,      // server report id
            'server_job_id' => $job->id,         // server job id
            'report_name'   => $report->report_name,
            'layout'        => $report->layout,
            'form_data'     => $report->form_data,
        ];
    });

    return response()->json([
        'status'  => 'ok',
        'message' => 'Job and reports have been updated',
        'reports' => $reportsArray
    ], 200);
}


public function appSync() 
{
    $jobs = AppJob::with('reports')->get();

    $transformed = $jobs->map(function($job) {
        return [
            'server_id' => $job->id, // maps server id to client server_id
            'client_name' => $job->client_name,
            'pipeline' => $job->pipeline ?? null, // if you have pipeline
            'event_title' => $job->job_title,
            'notes' => $job->notes,
            'on_site_date' => $job->on_site_date,
            'on_site_time' => $job->on_site_time,
            'due_on' => $job->due_on,
            'status' => $job->status,
            'reports' => $job->reports->map(function($report) use ($job) {
                return [
                    'server_id' => $report->id, // server report id
                    'server_job_id' => $job->id, // server job id
                    'report_name' => $report->report_name,
                    'layout' => $report->layout,
                    'form_data' => $report->form_data,
                ];
            }),
        ];
    });

    return response()->json($transformed);
}


    /**
     * Get a single job by ID
     */
    public function show($id)
    {
        $job = AppJob::with('client')->find($id);
        if (!$job) {
            return response()->json(['message' => 'Job not found'], 404);
        }
        return response()->json($job);
    }

    /**
     * Get jobs by client ID
     */
    public function getByClient($clientId)
    {
        $jobs = AppJob::where('clientId', $clientId)->with('client')->get();
        return response()->json($jobs);
    }

    /**
     * Create a new job
     */
public function store(Request $request)
{
    $validated = $request->validate([
        'client_name' => 'nullable|string|max:255',
        'job_title' => 'nullable|string|max:255',
        'notes' => 'nullable|string',
        'on_site_date' => 'nullable|date',
        'on_site_time' => 'nullable|string',
        'status' => 'nullable|string|max:50',
        'due_on' => 'nullable|date',
        'clientId' => 'nullable|exists:clients,id',
    ]);

if (!empty($validated['clientId']) && empty($validated['client_name'])) { 
    $client = \App\Models\Client::find($validated['clientId']);
    if ($client) {
        $validated['client_name'] = trim($client->name . ' ' . $client->surname);
    }
}


    $job = AppJob::create($validated);

    return response()->json($job, 201);
}


    /**
     * Update an existing job
     */
    public function update(Request $request, $id)
    {
        $job = AppJob::find($id);
        if (!$job) {
            return response()->json(['message' => 'Job not found'], 404);
        }

        $validated = $request->validate([
            'client_name' => 'nullable|string|max:255',
            'job_title' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'on_site_date' => 'nullable|date',
            'on_site_time' => 'nullable|string',
            'status' => 'nullable|string|max:50',
            'due_on' => 'nullable|date',
            'clientId' => 'nullable|exists:clients,id',
        ]);

        $job->update($validated);

        return response()->json($job);
    }

    /**
     * Delete a job
     */
    public function destroy($id)
    {
        $job = AppJob::find($id);
        if (!$job) {
            return response()->json(['message' => 'Job not found'], 404);
        }

        $job->delete();
        return response()->json(['message' => 'Job deleted successfully']);
    }
}
