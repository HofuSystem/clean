<?php

namespace Core\Financials\Models;

use Carbon\Carbon;
use Core\Orders\Models\Order;
use Core\Settings\Models\CoreModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends CoreModel
{
    use SoftDeletes;

    protected $table = 'invoices';

    protected $fillable = [
        'order_id',
        'invoice_number',
        'type',
        'subtotal',
        'vat_amount',
        'delivery_price',
        'total_coupon',
        'total_price',
        'filed_at',
        'qr_code',
        'fixed',
    ];

    /**
     * Get the order associated with the invoice.
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function scopeSearch($query)
    {
        if (request()->has('filters.search') && !empty(request('filters.search'))) {
            $search = request('filters.search');
            $query->where(function($q) use ($search) {
                $q->where('invoice_number', 'LIKE', '%' . $search . '%')
                  ->orWhereHas('order', function($oq) use ($search) {
                      $oq->where('reference_id', 'LIKE', '%' . $search . '%');
                  });
            });
        }

        if (request()->has('filters.type') && !empty(request('filters.type')) && request('filters.type') !== 'all') {
            $query->where('type', request('filters.type'));
        }

        if (request()->has('filters.from_date') && !empty(request('filters.from_date'))) {
            $query->whereDate('filed_at', '>=', Carbon::parse(request('filters.from_date')));
        }

        if (request()->has('filters.to_date') && !empty(request('filters.to_date'))) {
            $query->whereDate('filed_at', '<=', Carbon::parse(request('filters.to_date')));
        }

        return $query;
    }

    public function scopeDataTable($query): void
    {
        if (request()->has('start') and request()->has('length') and request()->input('length') != -1) {
            $query->skip(request()->input('start'))->take(request()->input('length'));
        }
        
        $query->orderBy('created_at', 'desc');
    }
}
