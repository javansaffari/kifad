<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    use HasFactory;

    protected $table = 'persons';

    protected $fillable = [
        'name',
        'type',
        'description'
    ];

    /**
     * Get all transactions associated with this person.
     */
    public function transactions()
    {
        return $this->hasMany(\App\Models\Tenant\Transaction::class, 'person_id');
    }
}
