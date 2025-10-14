<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\BillingSummary;


class SummaryRecordController extends Controller
{
    /**
     * Display a listing of billing summaries.
     */
    public function index()
    {
        $summaries = BillingSummary::withCount('employees')
            ->with('totals')
            ->orderByDesc('created_at')
            ->paginate(10);

        return response()->json($summaries);
    }

    /**
     * Store a newly created billing summary.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'summary_name' => 'required|string|max:255',
            'department_name' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        DB::beginTransaction();

        try {
            // Create summary
            $summary = BillingSummary::create($validated);

            // Create default rate structure
            $summary->rates()->create([]);

            // Initialize totals record
            $summary->totals()->create([]);

            DB::commit();

            return response()->json([
                'message' => 'Billing summary created successfully.',
                'summary' => $summary->load('rates', 'totals'),
            ], 201);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'error' => 'Failed to create billing summary.',
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Display detailed billing summary (with rates, employees, totals).
     */
    public function show($id)
    {
        $summary = BillingSummary::with([
            'rates',
            'employees.customRates',
            'employees.dailyEntries.dayMeta',
            'daysMeta',
            'totals'
        ])->findOrFail($id);

        return response()->json($summary);
    }

    /**
     * Update an existing billing summary.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'summary_name' => 'required|string|max:255',
            'department_name' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $summary = BillingSummary::findOrFail($id);
        $summary->update($validated);

        return response()->json([
            'message' => 'Billing summary updated successfully.',
            'summary' => $summary,
        ]);
    }

    /**
     * Delete a billing summary and cascade all related data.
     */
    public function destroy($id)
    {
        $summary = BillingSummary::findOrFail($id);
        $summary->delete();

        return response()->json([
            'message' => 'Billing summary deleted successfully.'
        ]);
    }

    /**
     * Get billing summary totals (computed).
     */
    public function getTotals($id)
    {
        $summary = BillingSummary::with('totals')->findOrFail($id);

        return response()->json($summary->totals);
    }

    /**
     * Get employees with rates for a billing summary.
     */
    public function getEmployees($id)
    {
        $employees = BillingEmployee::where('billing_summary_id', $id)
            ->with(['customRates', 'dailyEntries.dayMeta'])
            ->get();

        return response()->json($employees);
    }
}
