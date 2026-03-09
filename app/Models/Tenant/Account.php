<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'balance',
        'initial_balance',
        'type',
        'bank',
        'description'
    ];

    /**
     * Transactions where this account is the source (from)
     */
    public function transactionsFrom()
    {
        return $this->hasMany(Transaction::class, 'from_account_id');
    }

    /**
     * Transactions where this account is the destination (to)
     */
    public function transactionsTo()
    {
        return $this->hasMany(Transaction::class, 'to_account_id');
    }

    /**
     * All transactions related to this account (from or to)
     */
    public function transactions()
    {
        return $this->transactionsFrom()->union($this->transactionsTo());
    }
}
