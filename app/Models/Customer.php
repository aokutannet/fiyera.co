<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use BelongsToTenant, HasFactory, LogsActivity;

    protected $connection = 'tenant';

    protected $fillable = [
        'tenant_id',
        'company_name',
        'contact_person',
        'company_email',
        'category',
        'landline_phone',
        'mobile_phone',
        'legal_title',
        'address',
        'country',
        'city',
        'district',
        'type',
        'tax_number',
        'tax_office',
        'status',
    ];

    public function proposals()
    {
        return $this->hasMany(Proposal::class);
    }
}
