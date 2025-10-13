<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillingSummary extends Model
{
    use HasFactory;

    protected $fillable = [
        'summary_name',
        'department_name',
        'start_date',
        'end_date',
    ];

    public function rates()
    {
        return $this->hasOne(BillingRate::class);
    }

    public function employees()
    {
        return $this->hasMany(BillingEmployee::class);
    }

    public function daysMeta()
    {
        return $this->hasMany(BillingDayMeta::class);
    }

    public function totals()
    {
        return $this->hasOne(BillingTotal::class);
    }
}

/**
 * ───────────────────────────────────────────────────────────────
 * Related Models Below
 * ───────────────────────────────────────────────────────────────
 */

class BillingRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'billing_summary_id',
        'regular_day',
        'regular_ot',
        'night_premium',
        'night_premium_ot',
        'rest_day',
        'sunday_ot',
        'sunday_night_premium',
        'sunday_night_premium_ot',
        'regular_holiday',
        'regular_holiday_ot',
        'reg_hol_night_premium',
        'reg_hol_night_premium_ot',
        'unworked_regular_day',
    ];

    public function summary()
    {
        return $this->belongsTo(BillingSummary::class, 'billing_summary_id');
    }
}

class BillingEmployee extends Model
{
    use HasFactory;

    protected $fillable = [
        'billing_summary_id',
        'name',
        'manual_override',
        'use_custom',
        'regular_day',
        'regular_ot',
        'night_premium',
        'night_premium_ot',
        'rest_day',
        'sunday_ot',
        'sunday_night_premium',
        'sunday_night_premium_ot',
        'regular_holiday',
        'regular_holiday_ot',
        'reg_hol_night_premium',
        'reg_hol_night_premium_ot',
        'unworked_regular_day',
    ];

    public function summary()
    {
        return $this->belongsTo(BillingSummary::class, 'billing_summary_id');
    }

    public function customRates()
    {
        return $this->hasOne(EmployeeCustomRate::class, 'employee_id');
    }

    public function dailyEntries()
    {
        return $this->hasMany(EmployeeDailyEntry::class, 'employee_id');
    }
}

class EmployeeCustomRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'regular_day',
        'regular_ot',
        'night_premium',
        'night_premium_ot',
        'rest_day',
        'sunday_ot',
        'sunday_night_premium',
        'sunday_night_premium_ot',
        'regular_holiday',
        'regular_holiday_ot',
        'reg_hol_night_premium',
        'reg_hol_night_premium_ot',
        'unworked_regular_day',
    ];

    public function employee()
    {
        return $this->belongsTo(BillingEmployee::class, 'employee_id');
    }
}

class BillingDayMeta extends Model
{
    use HasFactory;

    protected $fillable = [
        'billing_summary_id',
        'day_date',
        'type',
        'threshold',
    ];

    public function summary()
    {
        return $this->belongsTo(BillingSummary::class, 'billing_summary_id');
    }

    public function dailyEntries()
    {
        return $this->hasMany(EmployeeDailyEntry::class, 'day_meta_id');
    }
}

class EmployeeDailyEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'day_meta_id',
        'hours',
        'override_type',
        'override_threshold',
    ];

    public function employee()
    {
        return $this->belongsTo(BillingEmployee::class, 'employee_id');
    }

    public function dayMeta()
    {
        return $this->belongsTo(BillingDayMeta::class, 'day_meta_id');
    }
}

class BillingTotal extends Model
{
    use HasFactory;

    protected $fillable = [
        'billing_summary_id',
        'grand_total',
        'regular_day_total',
        'regular_ot_total',
        'night_premium_total',
        'night_premium_ot_total',
        'rest_day_total',
        'sunday_ot_total',
        'sunday_night_premium_total',
        'sunday_night_premium_ot_total',
        'regular_holiday_total',
        'regular_holiday_ot_total',
        'reg_hol_night_premium_total',
        'reg_hol_night_premium_ot_total',
        'unworked_regular_day_total',
    ];

    public function summary()
    {
        return $this->belongsTo(BillingSummary::class, 'billing_summary_id');
    }
}
