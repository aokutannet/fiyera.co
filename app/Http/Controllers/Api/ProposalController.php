<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Proposal;
use App\Models\ProposalActivity;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ProposalController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $status = $request->input('status');
        $search = $request->input('search');

        $query = Proposal::with('customer', 'user')
            ->orderBy('created_at', 'desc');

        if ($status) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('proposal_number', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($cq) use ($search) {
                      $cq->where('company_name', 'like', "%{$search}%");
                  });
            });
        }

        $proposals = $query->paginate($perPage);

        return response()->json($proposals);
    }

    public function statuses()
    {
        return response()->json([
            ['id' => 'draft', 'label' => 'Taslak', 'color' => 'gray'],
            ['id' => 'pending', 'label' => 'Beklemede', 'color' => 'orange'],
            ['id' => 'approved', 'label' => 'Onaylandı', 'color' => 'green'],
            ['id' => 'rejected', 'label' => 'Reddedildi', 'color' => 'red'],
        ]);
    }

    public function show($id)
    {
        $proposal = Proposal::with(['customer', 'user', 'items', 'activities.user', 'internalNotes.user'])->findOrFail($id);
        return response()->json($proposal);
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

        try {
            DB::connection('tenant')->beginTransaction();

            $subtotal = 0;
            $totalTaxAmount = 0;
            $proposalItemsData = [];

            foreach ($request->items as $item) {
                // Item defaults
                $item['quantity'] = $item['quantity'] ?? 1;
                $item['unit'] = $item['unit'] ?? 'Adet';
                $item['unit_price'] = $item['unit_price'] ?? 0;
                $item['tax_rate'] = $item['tax_rate'] ?? 0;
                $item['discount_type'] = $item['discount_type'] ?? 'fixed';
                $item['discount_value'] = $item['discount_value'] ?? 0;

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
                'source' => 'mobile',
            ]);

            foreach ($proposalItemsData as $itemData) {
                $proposal->items()->create($itemData);
            }

            ProposalActivity::create([
                'proposal_id' => $proposal->id,
                'user_id' => auth()->id(),
                'activity_type' => 'created',
                'description' => 'Teklif mobil uygulama üzerinden oluşturuldu.',
                'new_value' => $proposal->status
            ]);

            // General Activity Log
            ActivityLog::create([
                'user_id' => auth()->id(),
                'subject_type' => Proposal::class,
                'subject_id' => $proposal->id,
                'event' => 'created',
                'description' => 'Teklif oluşturuldu (Mobil).',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'properties' => ['source' => 'mobile_api'],
            ]);

            DB::connection('tenant')->commit();

            return response()->json(['message' => 'Teklif başarıyla oluşturuldu.', 'proposal' => $proposal], 201);

        } catch (\Exception $e) {
            DB::connection('tenant')->rollBack();
            return response()->json(['message' => 'Bir hata oluştu: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $proposal = Proposal::where('id', $id)->firstOrFail();

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
            'status' => 'nullable|in:draft,pending,approved,rejected',
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

        try {
            DB::connection('tenant')->beginTransaction();

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
                // Item defaults
                $item['quantity'] = $item['quantity'] ?? 1;
                $item['unit'] = $item['unit'] ?? 'Adet';
                $item['unit_price'] = $item['unit_price'] ?? 0;
                $item['tax_rate'] = $item['tax_rate'] ?? 0;
                $item['discount_type'] = $item['discount_type'] ?? 'fixed';
                $item['discount_value'] = $item['discount_value'] ?? 0;

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
            
            if ($subtotal > 0) {
                $totalTaxAmount = $totalTaxAmount * ($taxableAmount / $subtotal);
            }

            $totalAmount = $taxableAmount + $totalTaxAmount;

            $proposal->update([
                'customer_id' => $validated['customer_id'],
                'user_id' => $request->user()->id ?? auth()->id(), // Update user to current editor
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

            $proposal->items()->delete();
            foreach ($proposalItemsData as $itemData) {
                $proposal->items()->create($itemData);
            }

            ProposalActivity::create([
                'proposal_id' => $proposal->id,
                'user_id' => $request->user()->id ?? auth()->id(),
                'activity_type' => 'updated',
                'description' => 'Teklif güncellendi (Mobil).',
                'new_value' => $proposal->status
            ]);

            // General Activity Log
            ActivityLog::create([
                'user_id' => $request->user()->id ?? auth()->id(),
                'subject_type' => Proposal::class,
                'subject_id' => $proposal->id,
                'event' => 'updated',
                'description' => 'Teklif güncellendi (Mobil).',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'properties' => ['source' => 'mobile_api'],
            ]);

            DB::connection('tenant')->commit();

            // Reload to get fresh data including items
            $proposal->load(['customer', 'items']);

            return response()->json(['message' => 'Teklif başarıyla güncellendi.', 'proposal' => $proposal], 200);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Bir hata oluştu: ' . $e->getMessage()], 500);
        }
    }

    public function history($id)
    {
        $proposal = Proposal::findOrFail($id);
        
        $activities = ProposalActivity::where('proposal_id', $proposal->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($activities);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:draft,pending,approved,rejected'
        ]);

        $proposal = Proposal::findOrFail($id);
        $oldStatus = $proposal->status;
        $newStatus = $request->status;

        if ($oldStatus === $newStatus) {
            return response()->json(['message' => 'Teklif durumu zaten ' . $newStatus], 422);
        }

        try {
            DB::connection('tenant')->beginTransaction();

            $proposal->update(['status' => $newStatus]);

            ProposalActivity::create([
                'proposal_id' => $proposal->id,
                'user_id' => $request->user()->id ?? auth()->id(),
                'activity_type' => 'status_updated',
                'description' => 'Teklif durumu güncellendi: ' . $oldStatus . ' -> ' . $newStatus,
                'old_value' => $oldStatus,
                'new_value' => $newStatus
            ]);

            // General Activity Log
            ActivityLog::create([
                'user_id' => $request->user()->id ?? auth()->id(),
                'subject_type' => Proposal::class,
                'subject_id' => $proposal->id,
                'event' => 'updated',
                'description' => 'Teklif durumu güncellendi: ' . $oldStatus . ' -> ' . $newStatus . ' (Mobil)',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'properties' => ['source' => 'mobile_api'],
            ]);

            DB::connection('tenant')->commit();

            return response()->json([
                'message' => 'Teklif durumu güncellendi.',
                'proposal' => $proposal
            ]);

        } catch (\Exception $e) {
            DB::connection('tenant')->rollBack();
            return response()->json(['message' => 'Bir hata oluştu: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            DB::connection('tenant')->beginTransaction();

            $proposal = Proposal::findOrFail($id);
            
            // Delete related items
            $proposal->items()->delete();
            
            // Delete the proposal
            $proposal->delete();

            // General Activity Log
            ActivityLog::create([
                'user_id' => auth()->id(),
                'subject_type' => Proposal::class,
                'subject_id' => $id, // ID since object is deleted
                'event' => 'deleted',
                'description' => 'Teklif silindi (Mobil).',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'properties' => ['source' => 'mobile_api'],
            ]);

            DB::connection('tenant')->commit();

            return response()->json(['message' => 'Teklif başarıyla silindi.']);

        } catch (\Exception $e) {
            DB::connection('tenant')->rollBack();
            return response()->json(['message' => 'Bir hata oluştu: ' . $e->getMessage()], 500);
        }
    }
}
