<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\ClientDepartment;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    /**
     * Display the invoice page.
     */
    public function index()
    {
        // Load all clients with their departments
        $clients = Client::with('departments')->get();

        return view('admin.invoice', compact('clients'));
    }

    /**
     * Return JSON list of clients and departments for AJAX.
     */
    public function getClients()
    {
        $clients = Client::with('departments:id,client_id,department')->get(['id', 'company']);
        return response()->json($clients);
    }

    /**
     * Return departments for a given client.
     */
    public function getDepartments($clientId)
    {
        $departments = ClientDepartment::where('client_id', $clientId)->pluck('department');
        return response()->json($departments);
    }

      // ✅ Fetch the next invoice number (for indicator display)
    public function nextInvoiceNumber()
    {
        $lastInvoice = Invoice::orderByDesc('id')->first();

        if ($lastInvoice) {
            // Extract numeric part
            $lastNumber = (int) substr($lastInvoice->invoice_number, 3);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1710; // starting number
        }

        return response()->json([
            'next_invoice_number' => 'SI ' . $nextNumber
        ]);
    }


    /**
     * Store invoice and its line items.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_date' => 'required|date',
            'client_id' => 'required|exists:clients,id',
            'client_department_id' => 'nullable|exists:client_departments,id',
            'internal_department' => 'nullable|string|max:255',
            'rows' => 'required|array|min:1',
            'rows.*.qty' => 'required|integer|min:1',
            'rows.*.unit' => 'nullable|string|max:50',
            'rows.*.description' => 'required|string',
            'rows.*.unitPrice' => 'required|numeric|min:0',
            'rows.*.amount' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Calculate total
            $total = collect($validated['rows'])->sum('amount');

            // Create invoice
            $invoice = Invoice::create([
                'statement_id' => null,
                'client_id' => $validated['client_id'],
                'client_department_id' => $validated['client_department_id'],
                'invoice_date' => $validated['invoice_date'],
                'internal_department' => $validated['internal_department'],
                'description' => 'Manual invoice creation',
                'total_amount' => $total,
                'status' => 'pending',
            ]);

            // Save invoice items
            foreach ($validated['rows'] as $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'qty' => $item['qty'],
                    'unit' => $item['unit'] ?? '',
                    'description' => $item['description'],
                    'unit_price' => $item['unitPrice'],
                    'amount' => $item['amount'],
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Invoice saved successfully!',
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Failed to save invoice.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }
}
