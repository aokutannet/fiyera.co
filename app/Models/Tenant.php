<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tenant extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'name', 
        'status',
        'subscription_status',
        'subscription_plan', // Deprecated but kept for now
        'subscription_plan_id',
        'trial_starts_at',
        'trial_ends_at',
        'onboarding_completed',
        'billing_details'
    ];

    protected $casts = [
        'trial_starts_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'onboarding_completed' => 'boolean',
        'billing_details' => 'array',
    ];

    protected static function booted()
    {
        static::creating(function ($tenant) {
            if (!$tenant->trial_starts_at) {
                $tenant->trial_starts_at = now();
            }
            if (!$tenant->trial_ends_at) {
                $tenant->trial_ends_at = now()->addDays(14);
            }
            if (!$tenant->subscription_status) {
                $tenant->subscription_status = 'trial';
            }
        });
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(\App\Models\Plan::class, 'subscription_plan_id');
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscription()
    {
        return $this->hasOne(Subscription::class)->latestOfMany()->where('status', 'active')->where('ends_at', '>', now());
    }

    public function onTrial()
    {
        return $this->subscription_status === 'trial' && $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    public function hasActiveSubscription()
    {
        return $this->subscription_status === 'active' && $this->activeSubscription()->exists();
    }

    public function isReadOnly()
    {
        // If not on trial and no active subscription, it's read-only (expired)
        if ($this->onTrial()) {
            return false;
        }
        if ($this->hasActiveSubscription()) {
            return false;
        }
        return true;
    }

    public function getTrialDaysRemaining()
    {
        // If actively subscribed (paid), no trial days remaining logic applies visually
        if ($this->subscription_status === 'active') {
             return 0;
        }

        if (!$this->trial_ends_at || $this->trial_ends_at->isPast()) {
            return 0;
        }
        return ceil(now()->floatDiffInDays($this->trial_ends_at));
    }

    /**
     * Check if the tenant's plan has a specific feature.
     */
    public function hasFeature(string $feature): bool
    {
        if (!$this->plan) {
            return false;
        }

        // Assuming 'features' in Plan is an array of enabled feature keys
        // or a key-value pair where key is feature and value is boolean/limit
        $features = $this->plan->features ?? [];

        // If it's a simple list of strings
        if (in_array($feature, $features)) {
            return true;
        }

        // If it's associative array (key => value)
        if (isset($features[$feature]) && $features[$feature]) {
            return true;
        }

        return false;
    }
}
