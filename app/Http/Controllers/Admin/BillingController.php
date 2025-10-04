<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Client;
use App\Models\ClientDepartment;
use App\Models\BillingSummary;
use App\Models\StatementOfAccount;

class BillingController extends Controller
{
    /** Get Clients */
    public function getClients()
    {
        $clients = Client::orderBy('company')->get();
        return response()->json($clients);
    }

    /** Get Departments */
    public function getDepartments(Request $request)
    {
        $clientId = $request->query('client_id');

        if (!$clientId) {
            return response()->json([], 400);
        }

        $departments = ClientDepartment::where('client_id', $clientId)
            ->orderBy('department')
            ->get();

        return response()->json($departments);
    }

    /** Fetch Billing Summaries */
    public function getBillingSummaries()
    {
        $summaries = BillingSummary::leftJoin('billing_totals', 'billing_summaries.id', '=', 'billing_totals.billing_summary_id')
            ->select(
                'billing_summaries.id',
                'billing_summaries.summary_name',
                'billing_summaries.department_name',
                'billing_summaries.start_date',
                'billing_summaries.end_date',
                'billing_totals.grand_total'
            )
            ->orderBy('billing_summaries.created_at', 'desc')
            ->get();

        return response()->json($summaries);
    }

    /** 🆕 Save Statement of Account */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'client_id' => 'required|exists:clients,id',
            'department_id' => 'required|exists:client_departments,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'due_date' => 'required|date|after_or_equal:end_date',
            'personnel_name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'statement_text' => 'nullable|string',
            'summaries' => 'required|array|min:1',
            'summaries.*.billing_summary_id' => 'required|exists:billing_summaries,id',
            'summaries.*.amount' => 'nullable|numeric|min:0',
            'total_amount' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Create main SOA record
            $soa = StatementOfAccount::create([
                'client_id' => $validated['client_id'],
                'department_id' => $validated['department_id'],
                'title' => $validated['title'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'due_date' => $validated['due_date'],
                'personnel_name' => $validated['personnel_name'],
                'position' => $validated['position'],
                'statement_text' => $validated['statement_text'] ?? null,
                'total_amount' => $validated['total_amount'] ?? 0,
            ]);

            // Attach billing summaries to SOA
            $syncData = [];
            foreach ($validated['summaries'] as $summary) {
                $syncData[$summary['billing_summary_id']] = [
                    'amount' => $summary['amount'] ?? 0,
                ];
            }

            $soa->billingSummaries()->sync($syncData);

            DB::commit();

            return response()->json([
                'message' => 'Statement of Account saved successfully!',
                'soa_id' => $soa->id,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to save Statement of Account.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
