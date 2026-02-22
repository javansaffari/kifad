<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'type', // expense, income, transfer
        'amount',
        'date',
        'main_category_id',
        'sub_category_id',
        'account_id',      // for income/expense
        'from_account_id', // for transfer
        'to_account_id',   // for transfer
        'person_id',
        'desc',
    ];

    /**
     * Get the account (for income/expense transactions)
     */
    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    /**
     * Get the person associated with this transaction
     */
    public function person()
    {
        return $this->belongsTo(Person::class, 'person_id');
    }

    /**
     * Get the main category (expense/income)
     */
    public function mainCategory()
    {
        return $this->belongsTo(Category::class, 'main_category_id');
    }

    /**
     * Get the sub category (expense/income)
     */
    public function subCategory()
    {
        return $this->belongsTo(Category::class, 'sub_category_id');
    }

    /**
     * Get the source account (for transfers)
     */
    public function fromAccount()
    {
        return $this->belongsTo(Account::class, 'from_account_id');
    }

    /**
     * Get the destination account (for transfers)
     */
    public function toAccount()
    {
        return $this->belongsTo(Account::class, 'to_account_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'transaction_tag', 'transaction_id', 'tag_id');
    }
}
