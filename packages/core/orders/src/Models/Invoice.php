<?php

namespace Core\Orders\Models;

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
        'total',
        'qr_code',
    ];

    /**
     * Get the order associated with the invoice.
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }
}
