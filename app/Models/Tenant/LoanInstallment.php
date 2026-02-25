<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanInstallment extends Model
{
    use HasFactory;

    protected $table = 'loan_installments';

    // Fillable fields for mass assignment
    protected $fillable = [
        'loan_id',
        'installment_number',
        'due_date',
        'amount',
        'paid',
    ];

    // Cast fields
    protected $casts = [
        'due_date' => 'date',
        'paid' => 'boolean',
        'amount' => 'integer',
    ];

    /**
     * Relationship: LoanInstallment belongs to a Loan
     */
    public function loan()
    {
        return $this->belongsTo(Loan::class, 'loan_id');
    }

    /**
     * Scope: Get only unpaid installments
     */
    public function scopeUnpaid($query)
    {
        return $query->where('paid', false);
    }

    /**
     * Scope: Get only paid installments
     */
    public function scopePaid($query)
    {
        return $query->where('paid', true);
    }

    /**
     * Mark installment as paid
     */
    public function markAsPaid()
    {
        $this->paid = true;
        $this->save();
    }

    /**
     * Get formatted due date in Persian format
     */
    public function getDueDateFormattedAttribute()
    {
        return $this->due_date ? $this->due_date->format('Y-m-d') : null;
    }
}
