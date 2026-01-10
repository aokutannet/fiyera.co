<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;

class OnboardingController extends Controller
{
    public function index()
    {
        // Fetch tenants that have onboarding data
        $tenants = Tenant::whereNotNull('onboarding_data')->latest()->paginate(20);

        return view('admin.onboarding.index', compact('tenants'));
    }
}
