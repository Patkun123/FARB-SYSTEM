<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InvoicePayment;

class PaymentRecordController extends Controller
{
    public function index()
    {
        $payments = InvoicePayment::with(['invoice.client', 'invoice.department'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($payment) {
                $invoice = $payment->invoice;

                  $chargeTo = $invoice->client
                    ? ($invoice->department
                        ? "{$invoice->client->company} - {$invoice->department->department}"
                        : $invoice->client->company)
                    : 'N/A';

                return [
                    'id' => $payment->id,
                    'invoice_number' => $invoice->invoice_number ?? '-',
                    'invoice_date' => $invoice->invoice_date ?? '-',
                    'client_name' => $chargeTo, // use combined value
                    'internal_dept' => $invoice->internal_department ?? '-', // show internal department
                    'total_amount' => $invoice->total_amount ?? '0',
                    'status' => $invoice->status ?? '-',
                    'or_number' => $payment->or_number,
                    'amount_paid' => $payment->amount_paid,
                    'date_paid' => $payment->date_paid,
                ];
            });

        return view('admin.receivable-records', compact('payments'));
    }
}
