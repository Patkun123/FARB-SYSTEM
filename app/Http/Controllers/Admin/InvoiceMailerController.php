<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Client;
Use App\Models\ClientDepartment;
use Illuminate\Http\Request;

class InvoiceMailerController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search', '');

        $invoices = Invoice::with(['items', 'client', 'department'])
            ->when($search, function ($query, $search) {
                $query->where('invoice_number', 'like', "%{$search}%")
                    ->orWhereHas('client', fn($q) => $q->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('department', fn($q) => $q->where('name', 'like', "%{$search}%"));
            })
            ->orderBy('invoice_date', 'desc')
            ->get()
            ->map(function ($invoice) {
                // ✅ Combine client + department name into "charge_to"
                $clientName = $invoice->client?->company_name ?? $invoice->client?->name ?? 'N/A';
                $departmentName = $invoice->department?->name ?? null;

                $invoice->charge_to = $departmentName
                    ? "{$clientName} - {$departmentName}"
                    : $clientName;

                // ✅ Optional: expose internal department if exists
                $invoice->internal_department = $invoice->department?->internal_department ?? null;

                // ✅ Fallback for missing invoice description
                if (empty($invoice->description)) {
                    $invoice->description = $invoice->items->map(fn($item) =>
                        "{$item->description} ({$item->qty} {$item->unit})"
                    )->implode(', ');
                }

                return $invoice;
            })
            ->values();

        return response()->json([
            'success' => true,
            'data' => $invoices,
        ]);
    }
}
