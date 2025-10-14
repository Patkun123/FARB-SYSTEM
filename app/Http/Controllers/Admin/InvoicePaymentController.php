<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use Illuminate\Http\Request;

class InvoicePaymentController extends Controller
{
    // Fetch invoices for the table
    public function index()
    {
        $invoices = Invoice::with(['client', 'department', 'items'])
            ->orderBy('invoice_date', 'desc')
            ->get()
            ->map(function ($invoice) {
                $description = $invoice->description ?: (
                    $invoice->items->isNotEmpty()
                        ? $invoice->items->pluck('description')->implode(', ')
                        : null
                );

                $chargeTo = $invoice->client
                    ? ($invoice->department
                        ? "{$invoice->client->company} - {$invoice->department->department}"
                        : $invoice->client->company)
                    : 'N/A';

                // Take first invoice item for table display
                $firstItem = $invoice->items->first();

                return [
                    'id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'date' => $invoice->invoice_date,
                    'chargeTo' => $chargeTo,
                    'client' => $invoice->client->company ?? 'N/A',
                    'department' => $invoice->department->department ?? 'N/A',
                    'qty' => $firstItem->qty ?? null,
                    'unit' => $firstItem->unit ?? null,
                    'description' => $description,
                    'unitPrice' => $firstItem ? number_format($firstItem->unit_price, 2) : null,
                    'internalDept' => $invoice->internal_department ?? 'N/A',
                    'status' => ucfirst($invoice->status),
                    'total' => number_format($invoice->total_amount, 2),
                    'items' => $invoice->items->map(fn($item) => [
                        'qty' => $item->qty,
                        'unit' => $item->unit,
                        'description' => $item->description,
                        'unitPrice' => number_format($item->unit_price, 2),
                        'amount' => number_format($item->amount, 2),
                    ]),
                ];
            });

        return response()->json($invoices); // ✅ Must return JSON
    }

    // Save payment and mark invoice as paid
    public function storePayment(Request $request)
    {
        $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'or_number' => 'required|string',
            'amount_paid' => 'required|numeric',
            'date_paid' => 'required|date',
        ]);

        try {
            \DB::transaction(function () use ($request) {
                InvoicePayment::create([
                    'invoice_id' => $request->invoice_id,
                    'or_number' => $request->or_number,
                    'amount_paid' => $request->amount_paid,
                    'date_paid' => $request->date_paid,
                ]);

                $invoice = Invoice::findOrFail($request->invoice_id);
                $invoice->status = 'paid';
                $invoice->save();
            });
        } catch (\Exception $e) {
            \Log::error('Payment save failed: '.$e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }

        return response()->json(['success' => true, 'message' => 'Payment saved and invoice marked as paid.']);
    }
}
