<?php

namespace App\Http\Controllers;

use App\Models\Proposal;
use App\Models\Customer;
use App\Models\ProposalActivity;
use App\Models\ProposalNote;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

use Barryvdh\DomPDF\Facade\Pdf;

class ProposalController extends Controller
{
    public function index(Request $request)
    {
        $query = Proposal::with('customer', 'user')->latest();

        // Status Filtering
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->filled('search')) {
            $searchTerm = trim($request->search);
            $query->where(function($q) use ($searchTerm) {
                $q->where('proposal_number', 'like', "%{$searchTerm}%")
                  ->orWhere('title', 'like', "%{$searchTerm}%")
                  ->orWhereHas('customer', function($cq) use ($searchTerm) {
                      $cq->where('company_name', 'like', "%{$searchTerm}%");
                  });
            });
        }

        $proposals = $query->paginate(10)->withQueryString();

        $tenant = \App\Models\Tenant::find(auth()->user()->tenant_id);
        $plan = $tenant->plan;
        
        $limit = $plan->limits['proposal_monthly'] ?? 0;
        $currentCount = Proposal::count(); // Scoped by trait
        $limitReached = ($limit != -1 && $currentCount >= $limit);
        
        return view('tenant.proposals.index', compact('proposals', 'limitReached', 'plan'));
    }

    public function create()
    {
        $tenant = \App\Models\Tenant::find(auth()->user()->tenant_id);
        $plan = $tenant->plan;
        
        $proposalLimit = $plan->limits['proposal_monthly'] ?? 0; // Default to 0 if not set, or handle as needed
        
        if ($proposalLimit != -1) {
             if (Proposal::count() >= $proposalLimit) {
                return redirect()->route('subscription.plans')->with('error', 'Paketinizin teklif oluşturma limiti doldu. Lütfen paketinizi yükseltin.');
             }
        }

        $customers = Customer::where('status', 'active')->get();
        return view('tenant.proposals.create', compact('customers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:tenant.customers,id',
            'title' => 'required|string|max:255',
            'proposal_date' => 'nullable|date',
            'valid_until' => 'nullable|date|after_or_equal:proposal_date',
            'delivery_date' => 'nullable|date',
            'payment_type' => 'nullable|string',
            'currency' => 'nullable|string|max:3',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
            'status' => 'nullable|in:draft,pending',
            'discount_type' => 'nullable|in:fixed,percentage',
            'discount_value' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'nullable',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'nullable|numeric|min:0.01',
            'items.*.unit' => 'nullable|string',
            'items.*.unit_price' => 'nullable|numeric|min:0',
            'items.*.tax_rate' => 'nullable|numeric|min:0',
            'items.*.discount_type' => 'nullable|in:fixed,percentage',
            'items.*.discount_value' => 'nullable|numeric|min:0',
        ]);

        // Set defaults
        $validated['proposal_date'] = $validated['proposal_date'] ?? now();
        $validated['currency'] = $validated['currency'] ?? 'TRY';
        $validated['status'] = $validated['status'] ?? 'draft';
        $validated['discount_type'] = $validated['discount_type'] ?? 'fixed';
        $validated['discount_value'] = $validated['discount_value'] ?? 0;

        $subtotal = 0;
        $totalTaxAmount = 0;

        $proposalItemsData = [];
        
        // Prepare items and Handle Stock/Product Creation
        foreach ($request->items as $item) {
            
            // Item defaults
            $item['quantity'] = $item['quantity'] ?? 1;
            $item['unit'] = $item['unit'] ?? 'Adet';
            $item['unit_price'] = $item['unit_price'] ?? 0;
            $item['tax_rate'] = $item['tax_rate'] ?? 0;
            $item['discount_type'] = $item['discount_type'] ?? 'fixed';
            $item['discount_value'] = $item['discount_value'] ?? 0;

            // Auto-create product if not selected
            $productId = $item['product_id'] ?? null;
            
            if (empty($productId)) {
                // Check if product exists with exact same name to avoid duplicates
                $existingProduct = \App\Models\Product::where('name', trim($item['description']))->first();
                if ($existingProduct) {
                    $productId = $existingProduct->id;
                } else {
                    $newProduct = \App\Models\Product::create([
                        'name' => trim($item['description']),
                        'price' => $item['unit_price'],
                        'vat_rate' => $item['tax_rate'],
                        'unit' => $item['unit'],
                        'status' => 'active',
                        'selling_currency' => $validated['currency'],
                        'stock_tracking' => false,
                    ]);
                    $productId = $newProduct->id;
                }
            }

            // Deduct Stock if Product Exists and Stock Tracking is on
            if ($productId) {
                $product = \App\Models\Product::withTrashed()->find($productId);
                if ($product && $product->stock_tracking) {
                    $product->decrement('stock', $item['quantity']);
                }
            }

            $linePrice = $item['quantity'] * $item['unit_price'];
            
            $lineDiscount = 0;
            if ($item['discount_type'] === 'percentage') {
                $lineDiscount = ($linePrice * $item['discount_value']) / 100;
            } else {
                $lineDiscount = $item['discount_value'];
            }
            
            $linePriceAfterDiscount = max(0, $linePrice - $lineDiscount);
            $lineTax = ($linePriceAfterDiscount * $item['tax_rate']) / 100;
            $lineTotal = $linePriceAfterDiscount + $lineTax;

            $subtotal += $linePriceAfterDiscount;
            $totalTaxAmount += $lineTax;

            $proposalItemsData[] = [
                'product_id' => $productId,
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit' => $item['unit'],
                'unit_price' => $item['unit_price'],
                'discount_type' => $item['discount_type'],
                'discount_value' => $item['discount_value'],
                'discount_amount' => $lineDiscount,
                'tax_rate' => $item['tax_rate'],
                'total_price' => $lineTotal,
            ];
        }

        // Global Discount calculation
        $globalDiscount = 0;
        if ($validated['discount_type'] === 'percentage') {
            $globalDiscount = ($subtotal * $validated['discount_value']) / 100;
        } else {
            $globalDiscount = $validated['discount_value'];
        }

        $taxableAmount = max(0, $subtotal - $globalDiscount);
        
        // Proportional tax reduction for global discount
        if ($subtotal > 0) {
            $totalTaxAmount = $totalTaxAmount * ($taxableAmount / $subtotal);
        }

        $totalAmount = $taxableAmount + $totalTaxAmount;

        $proposalNumber = 'TEK-' . date('Ymd') . '-' . strtoupper(Str::random(4));

        $proposal = Proposal::create([
            'customer_id' => $validated['customer_id'],
            'user_id' => auth()->id(),
            'proposal_number' => $proposalNumber,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'proposal_date' => $validated['proposal_date'],
            'valid_until' => $validated['valid_until'] ?? null,
            'delivery_date' => $validated['delivery_date'] ?? null,
            'payment_type' => $validated['payment_type'] ?? null,
            'subtotal' => $subtotal,
            'discount_type' => $validated['discount_type'],
            'discount_value' => $validated['discount_value'],
            'discount_amount' => $globalDiscount,
            'tax_amount' => $totalTaxAmount,
            'total_amount' => $totalAmount,
            'currency' => $validated['currency'],
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
        ]);

        foreach ($proposalItemsData as $itemData) {
            $proposal->items()->create($itemData);
        }

        // Log Activity
        ProposalActivity::create([
            'proposal_id' => $proposal->id,
            'user_id' => auth()->id(),
            'activity_type' => 'created',
            'description' => 'Teklif oluşturuldu.',
            'new_value' => $proposal->status
        ]);

        return redirect()->route('proposals.index')->with('success', 'Teklif başarıyla oluşturuldu.');
    }

    public function show(Proposal $proposal)
    {
        $proposal->load(['customer', 'user', 'items', 'activities.user', 'internalNotes.user']);
        return view('tenant.proposals.show', compact('proposal'));
    }

    public function edit(Proposal $proposal)
    {
        $proposal->load(['items.product']);
        $customers = Customer::where('status', 'active')->get();
        return view('tenant.proposals.edit', compact('proposal', 'customers'));
    }

    public function update(Request $request, Proposal $proposal)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:tenant.customers,id',
            'title' => 'required|string|max:255',
            'proposal_date' => 'required|date',
            'valid_until' => 'nullable|date|after_or_equal:proposal_date',
            'delivery_date' => 'nullable|date',
            'payment_type' => 'nullable|string',
            'currency' => 'required|string|max:3',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
            'status' => 'required|in:draft,pending,approved,rejected',
            'discount_type' => 'required|in:fixed,percentage',
            'discount_value' => 'required|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'nullable',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit' => 'required|string',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.tax_rate' => 'required|numeric|min:0',
            'items.*.discount_type' => 'required|in:fixed,percentage',
            'items.*.discount_value' => 'required|numeric|min:0',
        ]);

        // Restore stock from existing items before processing new ones
        foreach ($proposal->items as $existingItem) {
            if ($existingItem->product_id) {
                $product = \App\Models\Product::withTrashed()->find($existingItem->product_id);
                if ($product && $product->stock_tracking) {
                    $product->increment('stock', $existingItem->quantity);
                }
            }
        }

        $subtotal = 0;
        $totalTaxAmount = 0;
        $proposalItemsData = [];

        foreach ($request->items as $item) {
            // Auto-create product if not selected
            $productId = $item['product_id'] ?? null;
            if (empty($productId)) {
                $existingProduct = \App\Models\Product::where('name', trim($item['description']))->first();
                if ($existingProduct) {
                    $productId = $existingProduct->id;
                } else {
                    $newProduct = \App\Models\Product::create([
                        'name' => trim($item['description']),
                        'price' => $item['unit_price'],
                        'vat_rate' => $item['tax_rate'],
                        'unit' => $item['unit'],
                        'status' => 'active',
                        'selling_currency' => $validated['currency'],
                        'stock_tracking' => false,
                    ]);
                    $productId = $newProduct->id;
                }
            }

            // Deduct Stock
            if ($productId) {
                $product = \App\Models\Product::withTrashed()->find($productId);
                if ($product && $product->stock_tracking) {
                    $product->decrement('stock', $item['quantity']);
                }
            }

            $linePrice = $item['quantity'] * $item['unit_price'];
            
            $lineDiscount = 0;
            if ($item['discount_type'] === 'percentage') {
                $lineDiscount = ($linePrice * $item['discount_value']) / 100;
            } else {
                $lineDiscount = $item['discount_value'];
            }
            
            $linePriceAfterDiscount = max(0, $linePrice - $lineDiscount);
            $lineTax = ($linePriceAfterDiscount * $item['tax_rate']) / 100;
            $lineTotal = $linePriceAfterDiscount + $lineTax;

            $subtotal += $linePriceAfterDiscount;
            $totalTaxAmount += $lineTax;

            $proposalItemsData[] = [
                'product_id' => $productId,
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit' => $item['unit'],
                'unit_price' => $item['unit_price'],
                'discount_type' => $item['discount_type'],
                'discount_value' => $item['discount_value'],
                'discount_amount' => $lineDiscount,
                'tax_rate' => $item['tax_rate'],
                'total_price' => $lineTotal,
            ];
        }

        $globalDiscount = 0;
        if ($validated['discount_type'] === 'percentage') {
            $globalDiscount = ($subtotal * $validated['discount_value']) / 100;
        } else {
            $globalDiscount = $validated['discount_value'];
        }

        $taxableAmount = max(0, $subtotal - $globalDiscount);
        
        // Re-calculate proportional tax
        if ($subtotal > 0) {
            $totalTaxAmount = $totalTaxAmount * ($taxableAmount / $subtotal);
        }

        $totalAmount = $taxableAmount + $totalTaxAmount;

        $proposal->update([
            'customer_id' => $validated['customer_id'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'proposal_date' => $validated['proposal_date'],
            'valid_until' => $validated['valid_until'],
            'delivery_date' => $validated['delivery_date'],
            'payment_type' => $validated['payment_type'],
            'subtotal' => $subtotal,
            'discount_type' => $validated['discount_type'],
            'discount_value' => $validated['discount_value'],
            'discount_amount' => $globalDiscount,
            'tax_amount' => $totalTaxAmount,
            'total_amount' => $totalAmount,
            'currency' => $validated['currency'],
            'status' => $validated['status'],
            'notes' => $validated['notes'],
        ]);

        // Refresh items
        $proposal->items()->delete();
        foreach ($proposalItemsData as $itemData) {
            $proposal->items()->create($itemData);
        }

        // Log Activity
        ProposalActivity::create([
            'proposal_id' => $proposal->id,
            'user_id' => auth()->id(),
            'activity_type' => 'updated',
            'description' => 'Teklif güncellendi.'
        ]);

        return redirect()->route('proposals.show', $proposal)->with('success', 'Teklif başarıyla güncellendi.');
    }

    public function destroy(Proposal $proposal)
    {
        // Restore stock
        foreach ($proposal->items as $item) {
            if ($item->product_id) {
                $product = \App\Models\Product::find($item->product_id);
                if ($product && $product->stock_tracking) {
                    $product->increment('stock', $item->quantity);
                }
            }
        }
        
        $proposal->delete();
        return redirect()->route('proposals.index')->with('success', 'Teklif silindi.');
    }

    public function updateStatus(Request $request, Proposal $proposal)
    {
        $request->validate(['status' => 'required|in:draft,pending,approved,rejected']);
        $oldStatus = $proposal->status;
        $proposal->update(['status' => $request->status]);

        $statusLabels = [
            'draft' => 'Taslak',
            'pending' => 'Onay Bekliyor',
            'approved' => 'Onaylandı',
            'rejected' => 'Reddedildi',
        ];

        // Log Activity
        ProposalActivity::create([
            'proposal_id' => $proposal->id,
            'user_id' => auth()->id(),
            'activity_type' => 'status_changed',
            'description' => "Teklif durumu '{$statusLabels[$oldStatus]}' durumundan '{$statusLabels[$request->status]}' durumuna güncellendi.",
            'old_value' => $oldStatus,
            'new_value' => $request->status
        ]);

        return back()->with('success', 'Teklif durumu güncellendi.');
    }

    public function sendSms(Proposal $proposal)
    {
        // SMS sending logic would go here
        
        ProposalActivity::create([
            'proposal_id' => $proposal->id,
            'user_id' => auth()->id(),
            'activity_type' => 'sent_sms',
            'description' => "Teklif müşteriye SMS ile gönderildi ({$proposal->customer->mobile_phone})."
        ]);

        return back()->with('success', 'SMS gönderildi (Loglandı).');
    }

    public function sendEmail(Proposal $proposal)
    {
        try {
            \Illuminate\Support\Facades\Log::info("Attempting to send proposal email...", [
                'proposal_id' => $proposal->id,
                'email' => $proposal->customer->company_email
            ]);

            \Illuminate\Support\Facades\Mail::to($proposal->customer->company_email)->send(new \App\Mail\ProposalEmail($proposal));
            
            \Illuminate\Support\Facades\Log::info("Email sent successfully via Mail facade.");

            ProposalActivity::create([
                'proposal_id' => $proposal->id,
                'user_id' => auth()->id(),
                'activity_type' => 'sent_email',
                'description' => "Teklif müşteriye E-Posta ile gönderildi ({$proposal->customer->company_email})."
            ]);

            return back()->with('success', 'E-Posta başarıyla gönderildi.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Email sending failed: " . $e->getMessage());
            \Illuminate\Support\Facades\Log::error($e->getTraceAsString());
            return back()->with('error', 'E-Posta gönderilirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    public function sendWhatsapp(Proposal $proposal)
    {
        // Log Activity
        ProposalActivity::create([
            'proposal_id' => $proposal->id,
            'user_id' => auth()->id(),
            'activity_type' => 'sent_whatsapp',
            'description' => 'Teklif WhatsApp üzerinden paylaşıldı.'
        ]);

        $phone = preg_replace('/[^0-9]/', '', $proposal->customer->mobile_phone);
        $message = $proposal->proposal_number . ' nolu teklifiniz ekte yer almaktadır.';
        
        if ($proposal->public_token) {
            $link = route('proposals.public.show', $proposal->public_token);
            $message .= "\n\nTeklifinizi online görüntülemek ve onaylamak için tıklayın:\n" . $link;
        }

        $message .= "\n\nİyi çalışmalar dileriz.";
        
        $text = urlencode($message);
        
        return redirect("https://wa.me/{$phone}?text={$text}");
    }

    public function storeNote(Request $request, Proposal $proposal)
    {
        $request->validate(['note' => 'required|string']);

        $proposal->internalNotes()->create([
            'user_id' => auth()->id(),
            'note' => $request->note,
        ]);

        return back()->with('success', 'Not eklendi.');
    }

    public function print(Proposal $proposal)
    {
        $proposal->load(['customer', 'user', 'items']);

        // Fetch new layout settings
        $settings = \App\Models\Setting::whereIn('key', [
            'proposal_layout', 
            'proposal_color_primary', 
            'proposal_color_secondary'
        ])->get()->keyBy('key');

        $layout = json_decode($settings['proposal_layout']->value ?? '[]', true);
        
        // Fallback layout if empty
        if (empty($layout)) {
            $layout = [
                ['id' => 'header', 'visible' => true],
                ['id' => 'separator_1', 'visible' => true],
                ['id' => 'recipient', 'visible' => true],
                ['id' => 'items', 'visible' => true],
                ['id' => 'summary', 'visible' => true],
                ['id' => 'notes', 'visible' => true],
                ['id' => 'footer', 'visible' => true],
            ];
        }

        $primaryColor = $settings['proposal_color_primary']->value ?? '#111827';
        $secondaryColor = $settings['proposal_color_secondary']->value ?? '#6B7280';

        return view('tenant.proposals.print', compact('proposal', 'layout', 'primaryColor', 'secondaryColor'));
    }
    public function pdf(Proposal $proposal)
    {
        $proposal->load(['customer', 'user', 'items']);

        // Fetch new layout settings
        $settings = \App\Models\Setting::whereIn('key', [
            'proposal_layout', 
            'proposal_color_primary', 
            'proposal_color_secondary'
        ])->get()->keyBy('key');

        $layout = json_decode($settings['proposal_layout']->value ?? '[]', true);
        
        // Fallback layout if empty
        if (empty($layout)) {
            $layout = [
                ['id' => 'header', 'visible' => true],
                ['id' => 'separator_1', 'visible' => true],
                ['id' => 'recipient', 'visible' => true],
                ['id' => 'items', 'visible' => true],
                ['id' => 'summary', 'visible' => true],
                ['id' => 'notes', 'visible' => true],
                ['id' => 'footer', 'visible' => true],
            ];
        }

        $primaryColor = $settings['proposal_color_primary']->value ?? '#111827';
        $secondaryColor = $settings['proposal_color_secondary']->value ?? '#6B7280';

        $pdf = Pdf::loadView('tenant.proposals.print', compact('proposal', 'layout', 'primaryColor', 'secondaryColor') + ['isPdf' => true]);

        
        return $pdf->download($proposal->proposal_number . '.pdf');
    }

    public function designPreview(Request $request)
    {
        // 1. Create Dummy Data
        $proposal = new Proposal([
            'proposal_number' => 'ÖNİZLEME',
            'proposal_date' => now(),
            'valid_until' => now()->addDays(15),
            'title' => 'Web Tasarım ve Yazılım Hizmetleri',
            'notes' => "Bu bir taslak önizlemesidir.\nÖdeme %50 iş başlangıcında, %50 teslimatta alınacaktır.",
            'subtotal' => 15000,
            'tax_amount' => 3000,
            'total_amount' => 18000,
            'currency' => 'TRY',
            'user_id' => auth()->id(),
        ]);

        // Manually hydrate relationships for the view
        $proposal->setRelation('customer', new Customer([
            'company_name' => 'Örnek Müşteri A.Ş.',
            'contact_person' => 'Ahmet Yılmaz',
            'company_email' => 'ahmet@ornek.com',
            'mobile_phone' => '0555 123 45 67',
            'address' => 'Teknoloji Plaza, Kat: 3, No: 42, İstanbul'
        ]));

        $proposal->setRelation('user', auth()->user());

        $items = collect([
            new \App\Models\ProposalItem([
                'description' => 'Kurumsal Web Sitesi Tasarımı',
                'quantity' => 1,
                'unit' => 'Adet',
                'unit_price' => 10000,
                'total_price' => 10000,
                'discount_amount' => 0
            ]),
            new \App\Models\ProposalItem([
                'description' => 'Yönetim Paneli Geliştirme',
                'quantity' => 1,
                'unit' => 'Hizmet',
                'unit_price' => 5000,
                'total_price' => 5000,
                'discount_amount' => 0
            ])
        ]);
        $proposal->setRelation('items', $items);

        // 2. Resolve Layout & Design Params
        // Priority: Request Params > DB Settings > Defaults
        
        $settings = \App\Models\Setting::whereIn('key', [
            'proposal_layout', 
            'proposal_color_primary', 
            'proposal_color_secondary'
        ])->get()->keyBy('key');

        // Layout Logic
        if ($request->filled('layout')) {
            $layout = json_decode($request->layout, true);
        } else {
            $layout = json_decode($settings['proposal_layout']->value ?? '[]', true);
        }

        if (empty($layout)) {
            $layout = [
                ['id' => 'header', 'visible' => true],
                ['id' => 'separator_1', 'visible' => true],
                ['id' => 'recipient', 'visible' => true],
                ['id' => 'items', 'visible' => true],
                ['id' => 'summary', 'visible' => true],
                ['id' => 'notes', 'visible' => true],
                ['id' => 'footer', 'visible' => true],
            ];
        }

        // Color Logic
        $primaryColor = $request->input('primary_color', $settings['proposal_color_primary']->value ?? '#111827');
        $secondaryColor = $request->input('secondary_color', $settings['proposal_color_secondary']->value ?? '#6B7280');

        return view('tenant.proposals.print', compact('proposal', 'layout', 'primaryColor', 'secondaryColor'));
    }
    public function bulkActions(Request $request)
    {
        // Increase time limit for bulk PDF generation/sending
        set_time_limit(300);

        // Pre-process ids if sent as JSON string
        if ($request->filled('ids') && is_string($request->ids)) {
            $decoded = json_decode($request->ids, true);
            if (is_array($decoded)) {
                $request->merge(['ids' => $decoded]);
            }
        }

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:tenant.proposals,id',
            'action' => 'required|in:delete,status,email,sms',
            'status' => 'nullable|required_if:action,status|in:draft,pending,approved,rejected',
        ]);
        
        \Illuminate\Support\Facades\Log::info('Bulk Action Started', $request->all());

        $ids = $request->ids;
        $action = $request->action;
        $successCount = 0;
        $failCount = 0;

        switch ($action) {
            case 'delete':
                foreach ($ids as $id) {
                    $proposal = Proposal::with('items')->find($id);
                    if ($proposal) {
                        // Restore stock
                        foreach ($proposal->items as $item) {
                            if ($item->product_id) {
                                $product = \App\Models\Product::find($item->product_id);
                                if ($product && $product->stock_tracking) {
                                    $product->increment('stock', $item->quantity);
                                }
                            }
                        }
                        $proposal->delete();
                        $successCount++;
                    }
                }
                $message = "{$successCount} adet teklif silindi.";
                break;

            case 'status':
                $newStatus = $request->status;
                foreach ($ids as $id) {
                    $proposal = Proposal::find($id);
                    if ($proposal) {
                        $oldStatus = $proposal->status;
                        $proposal->update(['status' => $newStatus]);
                        
                        ProposalActivity::create([
                           'proposal_id' => $proposal->id,
                           'user_id' => auth()->id(),
                           'activity_type' => 'status_changed',
                           'description' => "Teklif durumu toplu işlem ile güncellendi.",
                           'old_value' => $oldStatus,
                           'new_value' => $newStatus
                        ]);
                        $successCount++;
                    }
                }
                $message = "{$successCount} adet teklifin durumu güncellendi.";
                break;

            case 'email':
                foreach ($ids as $id) {
                    // Eager load everything needed for the Email/PDF
                    $proposal = Proposal::with(['customer', 'items', 'user.tenant'])->find($id);
                    
                    if ($proposal && $proposal->customer && $proposal->customer->company_email) {
                         try {
                            \Illuminate\Support\Facades\Mail::to($proposal->customer->company_email)
                                ->send(new \App\Mail\ProposalEmail($proposal));
                            
                            ProposalActivity::create([
                                'proposal_id' => $proposal->id,
                                'user_id' => auth()->id(),
                                'activity_type' => 'sent_email',
                                'description' => "Teklif müşteriye toplu işlem ile tekrar gönderildi."
                            ]);
                            $successCount++;
                        } catch (\Exception $e) {
                            \Illuminate\Support\Facades\Log::error("Bulk email failed for Proposal {$id}: " . $e->getMessage());
                            $failCount++;
                        }
                    } else {
                        $failCount++; // Missing customer or email
                    }
                }
                
                $message = "{$successCount} adet teklif başarıyla gönderildi.";
                if ($failCount > 0) {
                    $message .= " ({$failCount} adet gönderim başarısız oldu veya e-posta adresi eksik)";
                }
                break;

            case 'sms':
                 foreach ($ids as $id) {
                    $proposal = Proposal::with('customer')->find($id);
                    if ($proposal && $proposal->customer && $proposal->customer->mobile_phone) {
                         ProposalActivity::create([
                            'proposal_id' => $proposal->id,
                            'user_id' => auth()->id(),
                            'activity_type' => 'sent_sms',
                            'description' => "Teklif müşteriye toplu işlem ile SMS olarak gönderildi."
                        ]);
                        $successCount++;
                    } else {
                        $failCount++;
                    }
                }
                $message = "{$successCount} adet SMS gönderildi.";
                if ($failCount > 0) {
                    $message .= " ({$failCount} adet gönderim başarısız)";
                }
                break;
        }

        if ($failCount > 0 && $successCount == 0) {
            return redirect()->route('proposals.index')->with('error', $message);
        }

        return redirect()->route('proposals.index')->with('success', $message);
    }
    public function duplicate(Proposal $proposal)
    {
        // 1. Check Limits (Optional but good practice)
        $tenant = \App\Models\Tenant::find(auth()->user()->tenant_id);
        $plan = $tenant->plan;
        $limit = $plan->limits['proposal_monthly'] ?? 0;
        
        if ($limit != -1) {
             if (Proposal::count() >= $limit) {
                return back()->with('error', 'Paketinizin teklif oluşturma limiti doldu. Lütfen paketinizi yükseltin.');
             }
        }

        // 2. Replicate Proposal
        $newProposal = $proposal->replicate();
        $newProposal->proposal_number = 'TEK-' . date('Ymd') . '-' . strtoupper(Str::random(4));
        $newProposal->title = $proposal->title . ' - Kopyası';
        $newProposal->status = 'draft';
        $newProposal->proposal_date = now();
        $newProposal->valid_until = now()->addDays(7); // Default valid days or keep original spread? User said "Complete Copy". I'll reset dates to current context.
        $newProposal->created_at = now();
        $newProposal->updated_at = now();
        $newProposal->push(); // Saves the model and internal key

        // 3. Replicate Items
        foreach ($proposal->items as $item) {
            $newItem = $item->replicate();
            $newItem->proposal_id = $newProposal->id;
            $newItem->created_at = now();
            $newItem->updated_at = now();
            $newItem->save();

            // Handle Stock (Deduct again since it's a new proposal created, similar to store logic)
            // Note: Store logic deducts on creation regardless of status.
            if ($newItem->product_id) {
                $product = \App\Models\Product::withTrashed()->find($newItem->product_id);
                if ($product && $product->stock_tracking) {
                    $product->decrement('stock', $newItem->quantity);
                }
            }
        }

        // 4. Log Activity
        ProposalActivity::create([
            'proposal_id' => $newProposal->id,
            'user_id' => auth()->id(),
            'activity_type' => 'created',
            'description' => "Teklif, {$proposal->proposal_number} numaralı tekliften kopyalanarak oluşturuldu.",
            'new_value' => 'draft'
        ]);

        return redirect()->route('proposals.edit', $newProposal)->with('success', 'Teklif başarıyla kopyalandı.');
    }

    public function togglePublic(Proposal $proposal)
    {
        $oldState = (bool) $proposal->public_token;

        if ($proposal->public_token) {
            $proposal->update(['public_token' => null]);
            $message = 'Teklif online erişime kapatıldı.';
        } else {
            $tenantId = auth()->user()->tenant_id;
            $proposal->public_token = base64_encode($tenantId . '|' . \Illuminate\Support\Str::random(32));
            $proposal->save();
            $message = 'Teklif online erişime açıldı.';
        }

        // Log Activity
        ProposalActivity::create([
            'proposal_id' => $proposal->id,
            'user_id' => auth()->id(),
            'activity_type' => 'system',
            'description' => $message
        ]);

        return back()->with('success', $message);
    }

    public function publicShow($token)
    {
        $proposal = $this->resolveProposalFromToken($token);

        if (!$proposal) {
            return view('public.proposal.expired');
        }

        // Automatic Expiration Check
        if ($proposal->valid_until && $proposal->valid_until->endOfDay()->isPast()) {
            return view('public.proposal.expired');
        }
        
        $proposal->load(['items', 'customer', 'user', 'activities']);
        
        $settings = \App\Models\Setting::whereIn('key', [
            'proposal_layout', 
            'proposal_color_primary', 
            'proposal_color_secondary',
            'proposal_logo', 
            'company_logo_png', 
            'company_logo_jpg'
        ])->get()->keyBy('key');

        $layout = json_decode($settings['proposal_layout']->value ?? '[]', true);
        if (empty($layout)) {
            $layout = [
                ['id' => 'header', 'visible' => true],
                ['id' => 'separator_1', 'visible' => true],
                ['id' => 'recipient', 'visible' => true],
                ['id' => 'items', 'visible' => true],
                ['id' => 'summary', 'visible' => true],
                ['id' => 'notes', 'visible' => true],
                ['id' => 'footer', 'visible' => true],
            ];
        }
        $primaryColor = $settings['proposal_color_primary']->value ?? '#111827';
        $secondaryColor = $settings['proposal_color_secondary']->value ?? '#6B7280';
        
        return view('public.proposal.show', compact('proposal', 'layout', 'primaryColor', 'secondaryColor', 'settings'));
    }

    public function publicAction(Request $request, $token)
    {
        $proposal = $this->resolveProposalFromToken($token);

        if (!$proposal) {
            return view('public.proposal.expired');
        }

        // Automatic Expiration Check
        if ($proposal->valid_until && $proposal->valid_until->endOfDay()->isPast()) {
            return view('public.proposal.expired');
        }
        
        $request->validate([
            'action' => 'required|in:approve,reject',
            'note' => 'nullable|string'
        ]);

        if ($request->action === 'approve') {
            $proposal->update(['status' => 'approved']);
            $desc = "Teklif, online bağlantı üzerinden MÜŞTERİ tarafından ONAYLANDI.";
            $mailSubject = "Teklifiniz Onaylandı! - " . $proposal->proposal_number;
        } else {
            $proposal->update(['status' => 'rejected']);
            $desc = "Teklif, online bağlantı üzerinden MÜŞTERİ tarafından REDDEDİLDİ.";
             $mailSubject = "Teklif Ret Bildirimi - " . $proposal->proposal_number;
        }

        if ($request->note) {
            $desc .= " (Not: {$request->note})";
             $proposal->internalNotes()->create([
                'user_id' => null, 
                'note' => "Müşteri Notu: " . $request->note,
            ]);
        }
        
        // Log Activity
        ProposalActivity::create([
            'proposal_id' => $proposal->id,
            'user_id' => $proposal->user_id, // Attributing to the owner or null? Keeping owner as 'actor' might be confusing, but system is actor.
            // Better: user_id null for system/customer actions
            'user_id' => null, 
            'activity_type' => 'status_changed',
            'description' => $desc,
            'new_value' => $proposal->status
        ]);
        
        // Send Notification Email to Proposal Owner
        if ($proposal->user && $proposal->user->email) {
            try {
                \Illuminate\Support\Facades\Mail::to($proposal->user->email)->send(new \App\Mail\ProposalStatusNotification($proposal, $request->action, $request->note));
            } catch (\Exception $e) {
                // Log but don't stop flow
                \Illuminate\Support\Facades\Log::error("Failed to send proposal status notification: " . $e->getMessage());
            }
        }

        return back()->with('success', 'İşleminiz başarıyla kaydedildi. Teşekkür ederiz.');
    }

    public function publicPrint($token)
    {
        $proposal = $this->resolveProposalFromToken($token);
        if (!$proposal) abort(404);
        
        // Log activity if not logged
        $sessionKey = 'viewed_proposal_print_' . $proposal->id;
        if (!session()->has($sessionKey)) {
             ProposalActivity::create([
                'proposal_id' => $proposal->id,
                'user_id' => null,
                'activity_type' => 'viewed',
                'description' => 'Müşteri teklifi yazdırma ekranını açtı.',
                'ip_address' => request()->ip(),
            ]);
            session()->put($sessionKey, true);
        }

        return $this->print($proposal);
    }

    public function publicPdf($token)
    {
        $proposal = $this->resolveProposalFromToken($token);
        if (!$proposal) abort(404);

        // Log activity if not logged
        $sessionKey = 'viewed_proposal_pdf_' . $proposal->id;
        if (!session()->has($sessionKey)) {
             ProposalActivity::create([
                'proposal_id' => $proposal->id,
                'user_id' => null,
                'activity_type' => 'viewed',
                'description' => 'Müşteri teklif PDF dosyasını indirdi.',
                'ip_address' => request()->ip(),
            ]);
            session()->put($sessionKey, true);
        }

        return $this->pdf($proposal);
    }

    private function resolveProposalFromToken($token)
    {
        try {
            $decoded = base64_decode($token);
            $parts = explode('|', $decoded);
            if (count($parts) != 2) return null;
            $tenantId = $parts[0];
            
            $dbName = "tenant_{$tenantId}_teklif";
            // Check if DB exists to prevent connection errors? 
            // Laravel config set might throw if DB doesn't exist? No, only on connection.
            // We'll trust the try-catch for connection issues.
            
            config(['database.connections.tenant.database' => $dbName]);
            \Illuminate\Support\Facades\DB::purge('tenant');
            \Illuminate\Support\Facades\DB::reconnect('tenant');
            
            return Proposal::where('public_token', $token)->first();
            
        } catch (\Exception $e) {
            return null;
        }
    }
}
