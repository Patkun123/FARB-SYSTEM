<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BillingSummary extends Model
{
    //
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

class BillingRate extends Model
{
    use HasFactory;

    protected $fillable = ['billing_summary_id','reg_hr','ot','np','hpnp','reg_hol','spec_hol'];

    public function summary()
    {
        return $this->belongsTo(BillingSummary::class);
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
        'reg_hr','ot','np','hpnp','reg_hol','spec_hol'
    ];

    public function summary()
    {
        return $this->belongsTo(BillingSummary::class);
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

    protected $fillable = ['employee_id','reg_hr','ot','np','hpnp','reg_hol','spec_hol'];

    public function employee()
    {
        return $this->belongsTo(BillingEmployee::class);
    }
}
class BillingDayMeta extends Model
{
    use HasFactory;

    protected $table = 'billing_days_meta'; // explicitly set the correct table

    protected $fillable = ['billing_summary_id','day_date','type','threshold'];

    public function summary()
    {
        return $this->belongsTo(BillingSummary::class);
    }

    public function entries()
    {
        return $this->hasMany(EmployeeDailyEntry::class, 'day_meta_id');
    }
}


class EmployeeDailyEntry extends Model
{
    use HasFactory;

    protected $fillable = ['employee_id','day_meta_id','hours','override_type','override_threshold'];

    public function employee()
    {
        return $this->belongsTo(BillingEmployee::class);
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
        'ot_total',
        'np_total',
        'hpnp_total',
        'reg_hol_total',
        'spec_hol_total'
    ];

    public function summary()
    {
        return $this->belongsTo(BillingSummary::class);
    }
}
