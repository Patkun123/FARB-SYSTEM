<?php

namespace App\Http\Controllers\BillingClerk;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Client;
use App\Models\ClientDepartment;
use App\Models\BillingSummary;
use App\Models\StatementOfAccount;
use App\Models\StatementSummaryItem;
use App\Models\Invoice;
use App\Models\InvoiceItem;

class BillingController extends Controller
{
    /**
     * Show Billing Page
     */
    public function index()
    {
        return view('billing_clerk.billing');
    }

    /**
     * Get all clients (JSON)
     */
    public function clients()
    {
        $clients = Client::select('id', 'company')->get();
        return response()->json($clients);
    }

    /**
     * Get departments for a given client (JSON)
     */
    public function departments(Request $request)
    {
        $clientId = $request->query('client_id');

        if (!$clientId) {
            return response()->json([]);
        }

        $departments = ClientDepartment::where('client_id', $clientId)
            ->select('id', 'department', 'personnel', 'position')
            ->get();

        return response()->json($departments);
    }

    /**
     * Fetch Billing Summaries for the Billing Summary UI
     */
    public function getBillingSummaries(Request $request)
    {
        $query = BillingSummary::leftJoin('billing_totals', 'billing_summaries.id', '=', 'billing_totals.billing_summary_id')
            ->select(
                'billing_summaries.id',
                'billing_summaries.summary_name',
                'billing_summaries.department_name',
                'billing_summaries.start_date',
                'billing_summaries.end_date',
                'billing_summaries.created_at',
                DB::raw('COALESCE(billing_totals.grand_total, 0) as grand_total')
            );

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('billing_summaries.summary_name', 'like', "%{$search}%")
                  ->orWhere('billing_summaries.department_name', 'like', "%{$search}%")
                  ->orWhere('billing_summaries.start_date', 'like', "%{$search}%")
                  ->orWhere('billing_summaries.end_date', 'like', "%{$search}%")
                  ->orWhereDate('billing_summaries.created_at', $search);
            });
        }

        return response()->json($query->orderBy('billing_summaries.created_at', 'desc')->limit(30)->get());
    }

    /**
     * Store a new Statement of Account + create Invoice automatically
     */
    public function store(Request $request)
    {
        $request->validate([
            'soa_title' => 'required|string|max:255',
            'client_id' => 'required|exists:clients,id',
            'department_id' => 'required|exists:client_departments,id',
            'covered_start_date' => 'required|date',
            'covered_end_date' => 'required|date|after_or_equal:covered_start_date',
            'due_date' => 'required|date',
            'personnel_name' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'statement_text' => 'nullable|string',
            'summaries' => 'required|string',
            'total_amount_due' => 'required|numeric',
        ]);

        DB::beginTransaction();

        try {
            // 1️⃣ Create Statement of Account
            $soa = StatementOfAccount::create([
                'soa_title' => $request->soa_title,
                'client_id' => $request->client_id,
                'department_id' => $request->department_id,
                'covered_start_date' => $request->covered_start_date,
                'covered_end_date' => $request->covered_end_date,
                'due_date' => $request->due_date,
                'personnel_name' => $request->personnel_name,
                'position' => $request->position,
                'statement_text' => $request->statement_text,
                'total_amount_due' => $request->total_amount_due,
            ]);

            // 2️⃣ Save linked summaries
            $selectedSummaries = json_decode($request->summaries, true);
            foreach ($selectedSummaries as $summary) {
                DB::table('statement_summary_items')->insert([
                    'statement_id' => $soa->id,
                    'billing_summary_id' => $summary['id'],
                    'grand_total' => $summary['grand_total'] ?? 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // 3️⃣ Automatically create Invoice
            $invoice = Invoice::create([
                'statement_id' => $soa->id,
                'client_id' => $request->client_id,
                'client_department_id' => $request->department_id,
                'invoice_date' => now(),
                'description' => $request->statement_text,
                'total_amount' => $request->total_amount_due,
            ]);


            DB::commit();

            return redirect()->route('billing_clerk.billing')
                ->with('success', 'Statement of Account and Invoice created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to save SOA and Invoice: ' . $e->getMessage()]);
        }
    }
}
