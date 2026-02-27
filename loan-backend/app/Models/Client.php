<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'income',
        'age',
        'employment',
        'credit_history',
        'email',
        'phone'
    ];

    protected $casts = [
        'income' => 'decimal:2',
        'age' => 'integer',
    ];

    public function loanApplications(): HasMany
    {
        return $this->hasMany(LoanApplication::class);
    }
}