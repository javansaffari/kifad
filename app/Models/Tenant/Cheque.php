<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cheque extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'amount',
        'serial_number',
        'sayad_id',
        'person_id',
        'account_id',
        'issue_date',
        'due_date',
        'bank',
        'tags',
        'description',
        'reminder'
    ];

    protected $casts = [
        'tags' => 'array',
        'reminder' => 'boolean'
    ];

    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
