<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class BillingSummaryController extends Controller
{
    /**
     * Store a new billing summary with all relations.
     */
    public function store(Request $request)
    {
        // Basic validation
        $request->validate([
            'summary_name' => 'required|string|max:255',
            'start_date'   => 'required|date',
            'end_date'     => 'required|date|after_or_equal:start_date',
            'rates'        => 'nullable|string',
            'daysMeta'     => 'nullable|string',
            'employees'    => 'nullable|string',
            'totals'       => 'nullable|string',
        ]);

        // decode JSON payloads safely
        $rates     = json_decode($request->input('rates', '{}'), true) ?: [];
        $daysMeta  = json_decode($request->input('daysMeta', '[]'), true) ?: [];
        $employees = json_decode($request->input('employees', '[]'), true) ?: [];
        $totals    = json_decode($request->input('totals', '{}'), true) ?: [];

        DB::beginTransaction();

        try {
            // 1) billing_summaries
            $summaryId = DB::table('billing_summaries')->insertGetId([
                'summary_name'    => $request->input('summary_name'),
                'department_name' => $request->input('department_name'),
                'start_date'      => $request->input('start_date'),
                'end_date'        => $request->input('end_date'),
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);

            // 2) billing_rates (single row per summary - columns match migration)
            $knownRateKeys = [
                'regular_day','regular_ot','night_premium','night_premium_ot',
                'rest_day','sunday_ot','sunday_night_premium','sunday_night_premium_ot',
                'regular_holiday','regular_holiday_ot','reg_hol_night_premium','reg_hol_night_premium_ot',
                'unworked_regular_day'
            ];
            $ratesRow = ['billing_summary_id' => $summaryId, 'created_at' => now(), 'updated_at' => now()];
            foreach ($knownRateKeys as $k) {
                // if provided, cast to float; otherwise let DB defaults handle (use null here)
                $ratesRow[$k] = array_key_exists($k, $rates) ? (is_numeric($rates[$k]) ? (float)$rates[$k] : null) : null;
            }
            DB::table('billing_rates')->insert($ratesRow);

            // 3) billing_days_meta
            $start = Carbon::parse($request->input('start_date'));
            $dayMetaMap = []; // index => id
            foreach ($daysMeta as $index => $d) {
                $date = $start->copy()->addDays($index)->format('Y-m-d');
                $type = $d['type'] ?? 'regular_day';
                $threshold = isset($d['threshold']) ? (float)$d['threshold'] : 8.0;

                $dayMetaId = DB::table('billing_days_meta')->insertGetId([
                    'billing_summary_id' => $summaryId,
                    'day_date'           => $date,
                    'type'               => $type,
                    'threshold'          => $threshold,
                    'created_at'         => now(),
                    'updated_at'         => now(),
                ]);

                $dayMetaMap[$index] = $dayMetaId;
            }

            // summary fields list (matches migrations)
            $summaryFields = [
                'regular_day','regular_ot','night_premium','night_premium_ot',
                'rest_day','sunday_ot','sunday_night_premium','sunday_night_premium_ot',
                'regular_holiday','regular_holiday_ot','reg_hol_night_premium','reg_hol_night_premium_ot',
                'unworked_regular_day'
            ];

            // 4) billing_employees, employee_custom_rates, employee_daily_entries
            foreach ($employees as $emp) {
                $employeeInsert = [
                    'billing_summary_id' => $summaryId,
                    'name'               => $emp['name'] ?? null,
                    'manual_override'    => !empty($emp['manual_override']) ? 1 : 0,
                    'use_custom'         => !empty($emp['use_custom']) ? 1 : 0,
                    'created_at'         => now(),
                    'updated_at'         => now(),
                ];

                // include computed summary fields if present (otherwise 0)
                foreach ($summaryFields as $sf) {
                    $employeeInsert[$sf] = isset($emp[$sf]) ? (float)$emp[$sf] : 0;
                }

                $employeeId = DB::table('billing_employees')->insertGetId($employeeInsert);

                // insert custom rates if provided (columns nullable)
                $customRates = $emp['customRates'] ?? [];
                if (!empty($customRates)) {
                    $customInsert = ['employee_id' => $employeeId, 'created_at' => now(), 'updated_at' => now()];
                    foreach ($summaryFields as $sf) {
                        $customInsert[$sf] = array_key_exists($sf, $customRates) && is_numeric($customRates[$sf]) ? (float)$customRates[$sf] : null;
                    }
                    DB::table('employee_custom_rates')->insert($customInsert);
                }

                // insert daily entries (map by dIndex -> day_meta_id)
                foreach ($emp['dailyEntries'] ?? [] as $dIndex => $entry) {
                    if (!isset($dayMetaMap[$dIndex])) {
                        // skip entries not matching date range
                        continue;
                    }

                    DB::table('employee_daily_entries')->insert([
                        'employee_id'        => $employeeId,
                        'day_meta_id'        => $dayMetaMap[$dIndex],
                        'hours'              => isset($entry['hours']) ? (float)$entry['hours'] : 0,
                        'override_type'      => $entry['override_type'] ?? null,
                        'override_threshold' => isset($entry['override_threshold']) ? (float)$entry['override_threshold'] : null,
                        'created_at'         => now(),
                        'updated_at'         => now(),
                    ]);
                }
            }

            // 5) billing_totals
            $totalsInsert = [
                'billing_summary_id' => $summaryId,
                'grand_total'        => isset($totals['grand_total']) ? (float)$totals['grand_total'] : 0,
                'regular_day_total'  => $totals['regular_day_total'] ?? 0,
                'regular_ot_total'   => $totals['regular_ot_total'] ?? 0,
                'night_premium_total'=> $totals['night_premium_total'] ?? 0,
                'night_premium_ot_total' => $totals['night_premium_ot_total'] ?? 0,
                'rest_day_total'     => $totals['rest_day_total'] ?? 0,
                'sunday_ot_total'    => $totals['sunday_ot_total'] ?? 0,
                'sunday_night_premium_total' => $totals['sunday_night_premium_total'] ?? 0,
                'sunday_night_premium_ot_total' => $totals['sunday_night_premium_ot_total'] ?? 0,
                'regular_holiday_total' => $totals['regular_holiday_total'] ?? 0,
                'regular_holiday_ot_total' => $totals['regular_holiday_ot_total'] ?? 0,
                'reg_hol_night_premium_total' => $totals['reg_hol_night_premium_total'] ?? 0,
                'reg_hol_night_premium_ot_total' => $totals['reg_hol_night_premium_ot_total'] ?? 0,
                'unworked_regular_day_total' => $totals['unworked_regular_day_total'] ?? 0,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            DB::table('billing_totals')->insert($totalsInsert);

            DB::commit();

            return redirect()
                ->route('admin.billing-summary')
                ->with('success', 'Billing summary created successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('BillingSummary store failed: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->withInput()->withErrors(['error' => 'Failed to save billing summary. Check logs.']);
        }
    }
}