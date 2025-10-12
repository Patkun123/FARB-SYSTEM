<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'statement_id',
        'client_id',
        'client_department_id',
        'invoice_date',
        'internal_department',
        'description',
        'total_amount',
        'status', // added status
    ];

    /**
     * Relationships
     */

    // Invoice belongs to a Statement of Account
    public function statementOfAccount()
    {
        return $this->belongsTo(StatementOfAccount::class, 'statement_id');
    }

    // Invoice belongs to a Client
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    // Invoice belongs to a Client Department
    public function department()
    {
        return $this->belongsTo(ClientDepartment::class, 'client_department_id');
    }

    // Invoice has many line items
    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    /**
     * Status helpers
     */

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isVoid(): bool
    {
        return $this->status === 'void';
    }

    public function markAsPaid(): void
    {
        $this->update(['status' => 'paid']);
    }

    public function markAsVoid(): void
    {
        $this->update(['status' => 'void']);
    }

    public function markAsPending(): void
    {
        $this->update(['status' => 'pending']);
    }
}
