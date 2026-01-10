<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\DashboardController;
use App\Http\Middleware\SetTenant;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Route;

// Global Routes (Locale - accessible on any domain)
Route::get('locale/{lang}', function ($lang) {
    if (in_array($lang, ['en', 'tr'])) {
        session(['locale' => $lang]);
    }
    return back();
})->name('locale.switch');

// 1. Landing Page Domain Group
// Uses default 'fiyera.co' if APP_DOMAIN is not set in .env
Route::domain(env('APP_DOMAIN', 'fiyera.co'))->group(function () {
    Route::get('/', function () {
        $plans = \App\Models\Plan::all();
        return view('frontend.index', compact('plans'));
    })->name('landing');
});

// 2. Application Subdomain Group (app.fiyera.co)
Route::domain('app.' . env('APP_DOMAIN', 'fiyera.co'))->group(function () {
    
    // Redirect app subdomain root to login
    Route::get('/', function () {
        return redirect()->route('login');
    });

    // Auth Routes
    Route::prefix('superadmin')->name('admin.')->group(function () {
        Route::get('/login', [App\Http\Controllers\Admin\AuthController::class, 'showLoginForm'])->name('login')->middleware('guest:super_admin');
        Route::post('/login', [App\Http\Controllers\Admin\AuthController::class, 'login'])->middleware('guest:super_admin');
        Route::post('/logout', [App\Http\Controllers\Admin\AuthController::class, 'logout'])->name('logout');

        Route::middleware('auth:super_admin')->group(function () {
            
            // 2FA Verification Routes
            Route::get('/verify', [App\Http\Controllers\Admin\AuthController::class, 'showVerifyForm'])->name('verify.index');
            Route::post('/verify', [App\Http\Controllers\Admin\AuthController::class, 'verify'])->name('verify.store');
            Route::get('/verify/resend', [App\Http\Controllers\Admin\AuthController::class, 'resend'])->name('verify.resend');

            // Protected Routes
            Route::middleware('admin.two_factor')->group(function () {
                Route::get('/', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
                Route::resource('plans', App\Http\Controllers\Admin\PlanController::class);
                // Tenant Routes will go here
                Route::resource('tenants', App\Http\Controllers\Admin\TenantController::class);
                Route::post('/tenants/{tenant}/impersonate', [App\Http\Controllers\Admin\TenantController::class, 'impersonate'])->name('tenants.impersonate');
                Route::resource('orders', App\Http\Controllers\Admin\OrderController::class)->only(['index', 'show']);
                Route::post('/orders/{order}/upload-invoice', [App\Http\Controllers\Admin\OrderController::class, 'uploadInvoice'])->name('orders.upload-invoice');
                Route::get('/onboarding', [App\Http\Controllers\Admin\OnboardingController::class, 'index'])->name('onboarding.index');
                Route::resource('onboarding-questions', App\Http\Controllers\Admin\OnboardingQuestionController::class);
            });
        });
    });

    Route::get('/login', function () { 
        return view('tenant.login');
    })->name('login')->middleware('guest');
    Route::post('/login', [LoginController::class, 'login'])->middleware('guest');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);

    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');

    Route::get('/auth/google', [\App\Http\Controllers\Auth\SocialAuthController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [\App\Http\Controllers\Auth\SocialAuthController::class, 'handleGoogleCallback']);

    Route::middleware(['auth', SetTenant::class])->group(function () {
        Route::get('verify', [\App\Http\Controllers\Auth\TwoFactorController::class, 'index'])->name('verify.index');
        Route::post('verify', [\App\Http\Controllers\Auth\TwoFactorController::class, 'store'])->name('verify.store');
        Route::get('verify/resend', [\App\Http\Controllers\Auth\TwoFactorController::class, 'resend'])->name('verify.resend');
    });

    Route::middleware(['auth', SetTenant::class, \App\Http\Middleware\TwoFactorMiddleware::class])->group(function () {
        // Onboarding Routes
        Route::get('/onboarding', [App\Http\Controllers\OnboardingController::class, 'index'])->name('onboarding.index');
        Route::post('/onboarding', [App\Http\Controllers\OnboardingController::class, 'store'])->name('onboarding.store');
        Route::get('/onboarding/company-details', [App\Http\Controllers\OnboardingController::class, 'companyDetails'])->name('onboarding.company-details');
        Route::post('/onboarding/company-details', [App\Http\Controllers\OnboardingController::class, 'storeCompanyDetails'])->name('onboarding.company-details.store');
        Route::get('/onboarding/plans', [App\Http\Controllers\OnboardingController::class, 'plans'])->name('onboarding.plans');
        Route::get('/onboarding/processing', [App\Http\Controllers\OnboardingController::class, 'processing'])->name('onboarding.processing');
        Route::post('/onboarding/subscribe', [App\Http\Controllers\OnboardingController::class, 'subscribe'])->name('onboarding.subscribe');
        // Allow subscription purchase during onboarding
        Route::post('/subscription/store', [\App\Http\Controllers\SubscriptionController::class, 'store'])->name('subscription.store');

        // Account Inactive Route (Accessible even if suspended/expired)
        Route::get('/account-inactive', function () {
            return view('tenant.errors.account_inactive', ['reason' => request('reason', 'suspended')]);
        })->name('account.inactive');

        Route::middleware([App\Http\Middleware\EnsureOnboardingComplete::class, \App\Http\Middleware\CheckSubscription::class])->group(function () {
            Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
            
            Route::middleware(['permission:subscription.view'])->group(function () {
                 Route::get('/subscription', [\App\Http\Controllers\SubscriptionController::class, 'index'])->name('subscription.index');
                 Route::get('/subscription/plans', [\App\Http\Controllers\SubscriptionController::class, 'plans'])->name('subscription.plans');
                 Route::get('/subscription/upgrade', [\App\Http\Controllers\SubscriptionController::class, 'upgrade'])->name('subscription.upgrade');
                 Route::post('/subscription/store', [\App\Http\Controllers\SubscriptionController::class, 'store'])->name('subscription.store');
                 Route::any('/subscription/callback', [\App\Http\Controllers\SubscriptionController::class, 'callback'])->name('subscription.callback');
            });
            
            // Profile Routes
            Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
            Route::post('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
        
            // User Management
            // User Management
            Route::middleware(['permission:users.view'])->get('/users', [App\Http\Controllers\UserController::class, 'index'])->name('users.index');
            Route::middleware(['permission:users.create'])->post('/users', [App\Http\Controllers\UserController::class, 'store'])->name('users.store');
            Route::middleware(['permission:users.edit'])->group(function () {
                Route::put('/users/{user}', [App\Http\Controllers\UserController::class, 'update'])->name('users.update');
                Route::patch('/users/{user}/toggle-status', [App\Http\Controllers\UserController::class, 'toggleStatus'])->name('users.toggle-status');
            });
            Route::middleware(['permission:users.delete'])->delete('/users/{user}', [App\Http\Controllers\UserController::class, 'destroy'])->name('users.destroy');
        
            // Customer Management
            Route::middleware(['permission:customers.view'])->group(function () {
                Route::get('/customers', [\App\Http\Controllers\CustomerController::class, 'index'])->name('customers.index');
                Route::middleware(['permission:customers.create'])->group(function () {
                    Route::get('/customers/create', [\App\Http\Controllers\CustomerController::class, 'create'])->name('customers.create');
                    Route::post('/customers', [\App\Http\Controllers\CustomerController::class, 'store'])->name('customers.store');
                });
                Route::middleware(['permission:customers.edit'])->group(function () {
                    Route::get('/customers/{customer}/edit', [\App\Http\Controllers\CustomerController::class, 'edit'])->name('customers.edit');
                    Route::put('/customers/{customer}', [\App\Http\Controllers\CustomerController::class, 'update'])->name('customers.update');
                    Route::patch('/customers/{customer}/toggle-status', [\App\Http\Controllers\CustomerController::class, 'toggleStatus'])->name('customers.toggle-status');
                });
                Route::middleware(['permission:customers.delete'])->group(function () {
                    Route::delete('/customers/bulk-delete', [\App\Http\Controllers\CustomerController::class, 'bulkDestroy'])->name('customers.bulk-destroy');
                    Route::delete('/customers/{customer}', [\App\Http\Controllers\CustomerController::class, 'destroy'])->name('customers.destroy');
                });
            });
        
            // Product Management
            Route::middleware(['permission:products.view'])->group(function () {
                Route::get('/products/search', [\App\Http\Controllers\ProductController::class, 'search'])->name('products.search');
                Route::get('/products', [\App\Http\Controllers\ProductController::class, 'index'])->name('products.index');
                Route::get('/products/export', [\App\Http\Controllers\ProductController::class, 'export'])->name('products.export');
                Route::post('/products/import/analyze', [\App\Http\Controllers\ProductController::class, 'analyzeImport'])->name('products.import.analyze');
                Route::post('/products/import/map', [\App\Http\Controllers\ProductController::class, 'mapImport'])->name('products.import.map');
                Route::post('/products/import/execute', [\App\Http\Controllers\ProductController::class, 'executeImport'])->name('products.import.execute');
                Route::middleware(['permission:products.create'])->group(function () {
                    Route::get('/products/create', [\App\Http\Controllers\ProductController::class, 'create'])->name('products.create');
                    Route::post('/products', [\App\Http\Controllers\ProductController::class, 'store'])->name('products.store');
                });
                Route::get('/products/{product}', [\App\Http\Controllers\ProductController::class, 'show'])->name('products.show');
                Route::middleware(['permission:products.edit'])->group(function () {
                    Route::get('/products/{product}/edit', [\App\Http\Controllers\ProductController::class, 'edit'])->name('products.edit');
                    Route::put('/products/{product}', [\App\Http\Controllers\ProductController::class, 'update'])->name('products.update');
                    Route::patch('/products/{product}/toggle-status', [\App\Http\Controllers\ProductController::class, 'toggleStatus'])->name('products.toggle-status');
                });
                Route::middleware(['permission:products.delete'])->group(function () {
                    Route::delete('/products/bulk-delete', [\App\Http\Controllers\ProductController::class, 'bulkDestroy'])->name('products.bulk-destroy');
                    Route::delete('/products/{product}', [\App\Http\Controllers\ProductController::class, 'destroy'])->name('products.destroy');
                });
            });
        
            // Category Management
            Route::resource('categories', \App\Http\Controllers\CategoryController::class)->except(['create', 'edit', 'show']);
        
            // Proposal Management
            Route::middleware(['permission:reports.view', 'feature:advanced_reports'])->get('/reports', [\App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
    
            Route::middleware(['permission:proposals.view'])->group(function () {
                Route::get('/proposals', [\App\Http\Controllers\ProposalController::class, 'index'])->name('proposals.index');
                Route::middleware(['permission:proposals.create'])->group(function () {
                    Route::get('/proposals/create', [\App\Http\Controllers\ProposalController::class, 'create'])->name('proposals.create');
                    Route::post('/proposals', [\App\Http\Controllers\ProposalController::class, 'store'])->name('proposals.store');
                    Route::post('/proposals/{proposal}/send-sms', [\App\Http\Controllers\ProposalController::class, 'sendSms'])->name('proposals.send-sms');
                    Route::post('/proposals/{proposal}/send-email', [\App\Http\Controllers\ProposalController::class, 'sendEmail'])->name('proposals.send-email');
                    Route::post('/proposals/{proposal}/send-whatsapp', [\App\Http\Controllers\ProposalController::class, 'sendWhatsapp'])->name('proposals.send-whatsapp');
                });
                Route::get('/proposals/design-preview', [\App\Http\Controllers\ProposalController::class, 'designPreview'])->name('proposals.design-preview');
                Route::get('/proposals/{proposal}', [\App\Http\Controllers\ProposalController::class, 'show'])->name('proposals.show');
                Route::get('/proposals/{proposal}/print', [\App\Http\Controllers\ProposalController::class, 'print'])->name('proposals.print');
                Route::get('/proposals/{proposal}/pdf', [\App\Http\Controllers\ProposalController::class, 'pdf'])->name('proposals.pdf');
                
                Route::middleware(['permission:proposals.edit'])->group(function () {
                    Route::get('/proposals/{proposal}/edit', [\App\Http\Controllers\ProposalController::class, 'edit'])->name('proposals.edit');
                    Route::put('/proposals/{proposal}', [\App\Http\Controllers\ProposalController::class, 'update'])->name('proposals.update');
                    Route::patch('/proposals/{proposal}/status', [\App\Http\Controllers\ProposalController::class, 'updateStatus'])->name('proposals.update-status');
                    Route::post('/proposals/{proposal}/notes', [\App\Http\Controllers\ProposalController::class, 'storeNote'])->name('proposals.store-note');
                });
    
                Route::middleware(['permission:proposals.delete'])->delete('/proposals/{proposal}', [\App\Http\Controllers\ProposalController::class, 'destroy'])->name('proposals.destroy');
                // Bulk Actions
                Route::post('/proposals/bulk-actions', [\App\Http\Controllers\ProposalController::class, 'bulkActions'])->name('proposals.bulk-actions');
                Route::post('/proposals/{proposal}/duplicate', [\App\Http\Controllers\ProposalController::class, 'duplicate'])->name('proposals.duplicate');
                Route::post('/proposals/{proposal}/toggle-public', [\App\Http\Controllers\ProposalController::class, 'togglePublic'])->name('proposals.toggle-public');
            });

    // Public Proposal Routes (No Auth Required)
            Route::withoutMiddleware([\App\Http\Middleware\SetTenant::class, 'auth'])->group(function() {
                 Route::get('/offer/{token}', [\App\Http\Controllers\ProposalController::class, 'publicShow'])->name('proposals.public.show');
                 Route::get('/offer/{token}/print', [\App\Http\Controllers\ProposalController::class, 'publicPrint'])->name('proposals.public.print');
                 Route::get('/offer/{token}/pdf', [\App\Http\Controllers\ProposalController::class, 'publicPdf'])->name('proposals.public.pdf');
                 Route::post('/offer/{token}/action', [\App\Http\Controllers\ProposalController::class, 'publicAction'])->name('proposals.public.action');
            });
        
            // System Settings
            Route::middleware(['permission:settings.view'])->group(function () {
                 Route::get('/settings', [App\Http\Controllers\SettingController::class, 'index'])->name('settings.index');
                 Route::middleware(['permission:settings.edit'])->group(function () {
                     Route::post('/settings', [App\Http\Controllers\SettingController::class, 'update'])->name('settings.update');
                     Route::post('/settings/remove-file', [SettingController::class, 'removeFile'])->name('settings.remove-file');
                     Route::delete('/settings/delete-account', [App\Http\Controllers\SettingController::class, 'deleteAccount'])->name('settings.delete-account');
                 });
            });
        
            Route::resource('activity-logs', App\Http\Controllers\ActivityLogController::class)->only(['index', 'show']);
        });
    });

});
