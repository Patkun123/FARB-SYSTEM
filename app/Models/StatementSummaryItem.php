<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatementSummaryItem extends Model
{
    use HasFactory;

    protected $table = 'statement_summary_items';

    // Fillable fields
    protected $fillable = [
        'statement_id',
        'billing_summary_id',
        'grand_total',
    ];

    // Cast grand_total to decimal
    protected $casts = [
        'grand_total' => 'decimal:2',
    ];

    // Relationship
    public function statement()
    {
        return $this->belongsTo(StatementOfAccount::class, 'statement_id');
    }

    public function billingSummary()
    {
        return $this->belongsTo(BillingSummary::class, 'billing_summary_id');
    }
}
