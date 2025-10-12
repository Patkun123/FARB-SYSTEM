<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatementOfAccount extends Model
{
    use HasFactory;

    protected $table = 'statements_of_account';

    // Fillable fields for mass assignment
    protected $fillable = [
        'soa_title',
        'client_id',
        'department_id',
        'covered_start_date',
        'covered_end_date',
        'due_date',
        'personnel_name',
        'position',
        'statement_text',
        'total_amount_due',
    ];

    // Cast fields to appropriate types
    protected $casts = [
        'covered_start_date' => 'date',
        'covered_end_date' => 'date',
        'due_date' => 'date',
        'total_amount_due' => 'decimal:2',
    ];

    // Relationships
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function department()
    {
        return $this->belongsTo(ClientDepartment::class, 'department_id');
    }

    public function summaryItems()
    {
        return $this->hasMany(StatementSummaryItem::class, 'statement_id');
    }
}
