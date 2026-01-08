<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class Proposal extends Model
{
    use BelongsToTenant;
    use LogsActivity;

    protected $connection = 'tenant';

    protected $fillable = [
        'tenant_id',
        'customer_id',
        'user_id',
        'proposal_number',
        'title',
        'description',
        'proposal_date',
        'valid_until',
        'delivery_date',
        'payment_type',
        'subtotal',
        'discount_type',
        'discount_value',
        'discount_amount',
        'tax_amount',
        'total_amount',
        'currency',
        'status',
        'notes',
    ];

    protected $casts = [
        'proposal_date' => 'date',
        'valid_until' => 'date',
        'delivery_date' => 'date',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(ProposalItem::class);
    }

    public function activities()
    {
        return $this->hasMany(ProposalActivity::class)->latest();
    }

    public function internalNotes()
    {
        return $this->hasMany(ProposalNote::class)->latest();
    }
}
