<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProposalItem extends Model
{
    protected $connection = 'tenant';

    protected $fillable = [
        'proposal_id',
        'product_id',
        'description',
        'quantity',
        'unit',
        'unit_price',
        'discount_type',
        'discount_value',
        'discount_amount',
        'tax_rate',
        'total_price',
    ];

    public function proposal()
    {
        return $this->belongsTo(Proposal::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
