<?php

namespace Core\Admin\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FixedCost extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'amount',
        'frequency',
        'date',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'date',
    ];

    /**
     * Get the monthly amount for this fixed cost
     */
    public function getMonthlyAmountAttribute()
    {
        switch ($this->frequency) {
            case 'monthly':
                return $this->amount;
            case 'quarterly':
                return $this->amount / 3;
            case 'yearly':
                return $this->amount / 12;
            default:
                return $this->amount;
        }
    }

    /**
     * Scope to get fixed costs for a specific month
     */
    public function scopeForMonth($query, $year, $month)
    {
        $date = \Carbon\Carbon::create($year, $month, 1);

        return $query->whereYear('date', $year)
                    ->whereMonth('date', $month);
    }

    /**
     * Get total fixed costs for a specific month
     */
    public static function getTotalForMonth($year, $month)
    {
        return self::forMonth($year, $month)
                   ->get()
                   ->sum('monthly_amount');
    }
}
