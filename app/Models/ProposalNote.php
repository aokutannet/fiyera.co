<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProposalNote extends Model
{
    protected $connection = 'tenant';

    protected $fillable = [
        'proposal_id',
        'user_id',
        'note',
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
