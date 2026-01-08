<?php

namespace App\Http\Controllers;

use App\Models\Proposal;
use App\Models\Customer;
use App\Models\ProposalActivity;
use App\Models\ProposalNote;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
            'proposal_date' => 'required|date',
            'valid_until' => 'nullable|date|after_or_equal:proposal_date',
            'delivery_date' => 'nullable|date',
            'payment_type' => 'nullable|string',
            'currency' => 'required|string|max:3',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
            'status' => 'required|in:draft,pending',
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

        $subtotal = 0;
        $totalTaxAmount = 0;

        $proposalItemsData = [];
        
        // Prepare items and Handle Stock/Product Creation
        foreach ($request->items as $item) {
            
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
        $proposal->load('items');
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
        // Email sending logic would go here
        
        ProposalActivity::create([
            'proposal_id' => $proposal->id,
            'user_id' => auth()->id(),
            'activity_type' => 'sent_email',
            'description' => "Teklif müşteriye E-Posta ile gönderildi ({$proposal->customer->company_email})."
        ]);

        return back()->with('success', 'E-Posta gönderildi (Loglandı).');
    }

    public function sendWhatsapp(Proposal $proposal)
    {
        ProposalActivity::create([
            'proposal_id' => $proposal->id,
            'user_id' => auth()->id(),
            'activity_type' => 'sent_whatsapp',
            'description' => "Teklif müşteriye WhatsApp üzerinden paylaşıldı."
        ]);

        $phone = preg_replace('/[^0-9]/', '', $proposal->customer->mobile_phone);
        $text = urlencode($proposal->proposal_number . ' nolu teklifiniz ekte yer almaktadır. İyi çalışmalar dileriz.');
        
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
}
