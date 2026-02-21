<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Debt extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'amount',
        'due_date',
        'account_id',
        'person_id',
        'tags',
        'description',
        'reminder'
    ];

    protected $casts = [
        'tags' => 'array',
        'reminder' => 'boolean'
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function person()
    {
        return $this->belongsTo(Person::class);
    }
}
