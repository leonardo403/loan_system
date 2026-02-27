<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoanApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'requested_amount',
        'purpose',
        'status',
        'credit_score',
        'risk_category',
        'approval_status',
        'approved_amount',
        'interest_rate',
        'max_term',
        'justification',
        'recommendations',
        'conditions',
        'ai_analysis'
    ];

    protected $casts = [
        'requested_amount' => 'decimal:2',
        'approved_amount' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'credit_score' => 'integer',
        'max_term' => 'integer',
        'recommendations' => 'array',
        'conditions' => 'array',
        'ai_analysis' => 'array',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
