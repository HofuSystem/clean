<?php

namespace Core\Financials\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyFinancialReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'ad_cost',
        'operating_expenses',
        'bank_balance',
        'note',
    ];

    protected $casts = [
        'date' => 'date',
        'ad_cost' => 'decimal:2',
        'operating_expenses' => 'decimal:2',
        'bank_balance' => 'decimal:2',
    ];
}
