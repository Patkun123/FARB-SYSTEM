<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\BillingSummary;

class BillingSummaryController extends Controller
{
    /**
     * Store a new billing summary with all relations.
     */
    public function store(Request $request)
    {
        // Get all data
        $data = $request->all();

        // Decode JSON inputs from hidden fields
        $data['rates']     = json_decode($request->input('rates'), true) ?? [];
        $data['daysMeta']  = json_decode($request->input('daysMeta'), true) ?? [];
        $data['employees'] = json_decode($request->input('employees'), true) ?? [];
        $data['totals']    = json_decode($request->input('totals'), true) ?? [];

        DB::transaction(function () use ($data) {

            $summary = BillingSummary::create([
                'summary_name'    => $data['summary_name'],
                'department_name' => $data['department_name'] ?? null,
                'start_date'      => $data['start_date'],
                'end_date'        => $data['end_date'],
            ]);

            // Rates
            if (!empty($data['rates'])) {
                $summary->rates()->create($data['rates']);
            }

            // Days Meta
            $startDate = \Carbon\Carbon::parse($data['start_date']);
            $dayMetaMap = [];
            foreach ($data['daysMeta'] ?? [] as $dIndex => $day) {
                $dayDate = $startDate->copy()->addDays($dIndex)->format('Y-m-d');

                $meta = $summary->daysMeta()->create([
                    'day_date'  => $dayDate,
                    'type'      => $day['type'] ?? 'work',
                    'threshold' => $day['threshold'] ?? 8,
                ]);

                $dayMetaMap[$dIndex] = $meta->id;
            }

            // Employees
            foreach ($data['employees'] ?? [] as $empData) {
                $employee = $summary->employees()->create([
                    'name'            => $empData['name'] ?? null,
                    'manual_override' => $empData['manual_override'] ?? false,
                    'use_custom'      => $empData['use_custom'] ?? false,
                    'reg_hr'          => $empData['reg_hr'] ?? 0,
                    'ot'              => $empData['ot'] ?? 0,
                    'np'              => $empData['np'] ?? 0,
                    'hpnp'            => $empData['hpnp'] ?? 0,
                    'reg_hol'         => $empData['reg_hol'] ?? 0,
                    'spec_hol'        => $empData['spec_hol'] ?? 0,
                ]);

                // Custom Rates
                if (!empty($empData['customRates'])) {
                    $employee->customRates()->create($empData['customRates']);
                }

                // Daily Entries
                foreach ($empData['dailyEntries'] ?? [] as $dIndex => $entry) {
                    if (!isset($dayMetaMap[$dIndex])) continue;
                    $employee->dailyEntries()->create([
                        'day_meta_id'        => $dayMetaMap[$dIndex],
                        'hours'              => $entry['hours'] ?? 0,
                        'override_type'      => $entry['override_type'] ?? null,
                        'override_threshold' => $entry['override_threshold'] ?? null,
                    ]);
                }
            }

            // Totals: Save into billing_totals table
            if (!empty($data['totals'])) {
                $summary->totals()->create([
                    'grand_total'    => $data['totals']['grand_total'] ?? 0,
                    'ot_total'       => $data['totals']['ot_total'] ?? 0,
                    'np_total'       => $data['totals']['np_total'] ?? 0,
                    'hpnp_total'     => $data['totals']['hpnp_total'] ?? 0,
                    'reg_hol_total'  => $data['totals']['reg_hol_total'] ?? 0,
                    'spec_hol_total' => $data['totals']['spec_hol_total'] ?? 0,
                ]);
            }
        }); // End transaction

        // Redirect back after successful transaction
        return redirect()
            ->route('admin.billing-summary')
            ->with('success', 'Billing summary created successfully.');
    }
}
