<?php

namespace Core\Products\Models;

use Core\Settings\Models\CoreModel;

class ProductVariant extends CoreModel
{
    protected $table = 'product_variants';

    protected $fillable = [
        'product_id', 'pieces_count', 'price',
        'cost', 'points', 'extra_piece_price', 'quantity', 'status', 'sort_order',
    ];

    protected $casts = [
        'price' => 'double',
        'cost' => 'double',
        'points' => 'double',
        'extra_piece_price' => 'double',
        'pieces_count' => 'integer',
        'sort_order' => 'integer',
        'quantity' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
