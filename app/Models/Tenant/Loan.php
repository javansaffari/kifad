<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'amount',
        'bank',
        'start_date',
        'installments_paid',
        'installment_due_day',
        'installment_amount',
        'reminder'
    ];

    protected $casts = [
        'reminder' => 'boolean'
    ];
}
