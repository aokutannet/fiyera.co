<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $search = $request->input('search');

        $query = Product::with('productCategory')
            ->orderBy('created_at', 'desc');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $products = $query->paginate($perPage);

        return response()->json($products);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:255|unique:tenant.products,code',
            'category_id' => 'nullable|exists:tenant.categories,id',
            'price' => 'required|numeric|min:0',
            'buying_price' => 'nullable|numeric|min:0',
            'vat_rate' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'unit' => 'required|string',
            'stock_tracking' => 'boolean',
            'stock' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,passive',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Defaults
        $validated['selling_currency'] = $validated['selling_currency'] ?? 'TRY';
        $validated['buying_currency'] = $validated['buying_currency'] ?? 'TRY';

        // Image Upload
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $tenantId = auth()->user()->tenant_id;
            $filename = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = 'tenants/' . $tenantId . '/products';
            
            // Move to public/uploads
            $file->move(public_path('uploads/' . $path), $filename);
            
            $validated['image_path'] = $path . '/' . $filename;
        }

        $product = Product::create($validated);

        // Log activity
        ActivityLog::create([
            'user_id' => auth()->id(),
            'subject_type' => Product::class,
            'subject_id' => $product->id,
            'event' => 'created',
            'description' => 'Ürün oluşturuldu (Mobil).',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'properties' => ['source' => 'mobile_api'],
        ]);

        return response()->json([
            'message' => 'Ürün başarıyla oluşturuldu.',
            'product' => $product
        ], 201);
    }
    public function show($id)
    {
        $product = Product::with('productCategory')->findOrFail($id);
        return response()->json($product);
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:255|unique:tenant.products,code,' . $id,
            'category_id' => 'nullable|exists:tenant.categories,id',
            'price' => 'required|numeric|min:0',
            'buying_price' => 'nullable|numeric|min:0',
            'vat_rate' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'unit' => 'required|string',
            'stock_tracking' => 'boolean',
            'stock' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,passive',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        
        // Defaults if missing (though update usually sends all or uses PATCH logic, standard here assumes PUT)
        $validated['selling_currency'] = $validated['selling_currency'] ?? 'TRY';
        $validated['buying_currency'] = $validated['buying_currency'] ?? 'TRY';

        // Image Upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($product->image_path && file_exists(public_path('uploads/' . $product->image_path))) {
                @unlink(public_path('uploads/' . $product->image_path));
            }

            $file = $request->file('image');
            $tenantId = auth()->user()->tenant_id;
            $filename = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = 'tenants/' . $tenantId . '/products';
            
            // Move to public/uploads
            $file->move(public_path('uploads/' . $path), $filename);
            
            $validated['image_path'] = $path . '/' . $filename;
        }

        $product->update($validated);

        // Log activity
        ActivityLog::create([
            'user_id' => auth()->id(),
            'subject_type' => Product::class,
            'subject_id' => $product->id,
            'event' => 'updated',
            'description' => 'Ürün güncellendi (Mobil).',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'properties' => ['source' => 'mobile_api'],
        ]);

        return response()->json([
            'message' => 'Ürün başarıyla güncellendi.',
            'product' => $product
        ]);
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        
        $product->delete();

        // Log activity
        ActivityLog::create([
            'user_id' => auth()->id(),
            'subject_type' => Product::class,
            'subject_id' => $id,
            'event' => 'deleted',
            'description' => 'Ürün silindi (Mobil).',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'properties' => ['source' => 'mobile_api'],
        ]);

        return response()->json(['message' => 'Ürün başarıyla silindi.']);
    }
}
