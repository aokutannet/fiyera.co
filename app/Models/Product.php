<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogsActivity;

class Product extends Model
{
    use HasFactory, BelongsToTenant, SoftDeletes, LogsActivity;

    protected $connection = 'tenant';

    protected $fillable = [
        'tenant_id',
        'name',
        'code',
        'description',
        'image_path',
        'category_id',
        'category', // Keeping for backward compatibility or direct string usage if needed, but mostly replacing.
        'price', // This is Selling Price (Excl Tax)
        'selling_currency',
        'buying_price',
        'buying_currency',
        'vat_rate',
        'unit',
        'stock_tracking',
        'stock',
        'critical_stock_alert',
        'critical_stock_quantity',
        'status',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'buying_price' => 'decimal:2',
        'vat_rate' => 'integer',
        'stock_tracking' => 'boolean',
        'critical_stock_alert' => 'boolean',
        'status' => 'string',
    ];

    public function proposalItems()
    {
        return $this->hasMany(ProposalItem::class);
    }

    public function productCategory()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
