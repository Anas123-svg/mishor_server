<?php

namespace App\Http\Controllers;

use App\Models\JobReport;
use Illuminate\Http\Request;

class JobReportController extends Controller
{
    public function index()
    {
        $jobReports = JobReport::get();
        return response()->json($jobReports);
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'report_name' => 'nullable|string|max:255',
            'job_id'      => 'nullable|exists:app_jobs,id',
            'form_data'   => 'nullable|array',
            'layout'      => 'nullable|string|max:255',
        ]);

        $report = JobReport::create($validated);

        return response()->json($report, 201);
    }

    /**
     * Get report by report ID
     */
    public function show($id)
    {
        $report = JobReport::with('job')->find($id);

        if (!$report) {
            return response()->json(['message' => 'Report not found'], 404);
        }

        return response()->json($report);
    }

    /**
     * Get reports by job ID
     */
    public function getByJob($jobId)
    {
        $reports = JobReport::where('job_id', $jobId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($reports);
    }

    /**
     * Update job report
     */
    public function update(Request $request, $id)
    {
        $report = JobReport::find($id);

        if (!$report) {
            return response()->json(['message' => 'Report not found'], 404);
        }

        $validated = $request->validate([
            'report_name' => 'nullable|string|max:255',
            'job_id'      => 'nullable|exists:app_jobs,id',
            'form_data'   => 'nullable|array',
            'layout'      => 'nullable|string|max:255',
        ]);

        $report->update($validated);

        return response()->json($report);
    }

    /**
     * Delete job report
     */
    public function destroy($id)
    {
        $report = JobReport::find($id);

        if (!$report) {
            return response()->json(['message' => 'Report not found'], 404);
        }

        $report->delete();

        return response()->json(['message' => 'Report deleted successfully']);
    }
}
