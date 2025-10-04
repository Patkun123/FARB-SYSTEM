<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatementOfAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'department_id',
        'title',
        'start_date',
        'end_date',
        'due_date',
        'personnel_name',
        'position',
        'statement_text',
        'total_amount',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function department()
    {
        return $this->belongsTo(ClientDepartment::class, 'department_id');
    }

    public function billingSummaries()
    {
        return $this->belongsToMany(BillingSummary::class, 'soa_billing_summaries', 'soa_id', 'billing_summary_id')
                    ->withPivot('amount')
                    ->withTimestamps();
    }
}
