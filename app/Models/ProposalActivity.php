<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProposalActivity extends Model
{
    protected $connection = 'tenant';

    protected $fillable = [
        'proposal_id',
        'user_id',
        'activity_type',
        'description',
        'old_value',
        'new_value',
        'ip_address'
    ];

    public function proposal()
    {
        return $this->belongsTo(Proposal::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
