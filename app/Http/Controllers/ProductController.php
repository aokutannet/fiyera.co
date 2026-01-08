<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function search(Request $request)
    {
        $term = trim($request->query('q', ''));
        
        $query = Product::query()
            ->where('status', 'active');
            
        if ($term) {
            $query->where(function($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('code', 'like', "%{$term}%");
            });
        }
        
        $products = $query->limit(20)->get(['id', 'name', 'code', 'price', 'vat_rate', 'unit', 'description', 'stock', 'stock_tracking']);
        
        return response()->json($products);
    }

    public function index(Request $request)
    {
        $query = Product::latest();

        // Search
        if ($request->filled('search')) {
            $searchTerm = trim($request->search);
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('code', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }

        // Filter by Category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filter by Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $products = $query->with('productCategory')->paginate(10)->withQueryString();
        
        // Get categories for filter dropdown
        $categories = Category::orderBy('name')->get();

        return view('tenant.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('tenant.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:255',
            'category_id' => ['nullable', function ($attribute, $value, $fail) {
                if (!\Illuminate\Support\Facades\DB::connection('tenant')->table('categories')->where('id', $value)->exists()) {
                    $fail('Seçilen kategori geçersiz.');
                }
            }],
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048', // Max 2MB
            
            // Pricing
            'price' => 'required|numeric|min:0', // Selling Price (Excl Tax)
            'selling_currency' => 'required|string|size:3',
            'buying_price' => 'nullable|numeric|min:0',
            'buying_currency' => 'nullable|string|size:3',
            'vat_rate' => 'required|integer|min:0|max:100',
            'unit' => 'required|string|max:255',
            
            // Stock
            'stock_tracking' => 'boolean',
            'stock' => 'nullable|integer',
            'critical_stock_alert' => 'boolean',
            'critical_stock_quantity' => 'nullable|integer',
            
            'status' => 'required|in:active,passive',
        ]);

        if ($request->hasFile('image')) {
            $tenantId = auth()->user()->tenant_id;
            $path = $request->file('image')->store("tenants/{$tenantId}/products", 'uploads');
            $validated['image_path'] = $path;
        }

        // Handle checkboxes not sending 'false'
        $validated['stock_tracking'] = $request->has('stock_tracking');
        $validated['critical_stock_alert'] = $request->has('critical_stock_alert');
        $validated['stock'] = $validated['stock'] ?? 0;

        $product = Product::create($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($product);
        }

        return redirect()->route('products.show', $product)->with('success', 'Ürün başarıyla oluşturuldu.');
    }

    public function show(Product $product)
    {
        // Fetch last 10 proposals involving this product
        $history = $product->proposalItems()
            ->with(['proposal.customer'])
            ->latest()
            ->take(10)
            ->get();

        return view('tenant.products.show', compact('product', 'history'));
    }

    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->get();
        return view('tenant.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:255',
            'category_id' => ['nullable', function ($attribute, $value, $fail) {
                if (!\Illuminate\Support\Facades\DB::connection('tenant')->table('categories')->where('id', $value)->exists()) {
                    $fail('Seçilen kategori geçersiz.');
                }
            }],
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            
            'price' => 'required|numeric|min:0',
            'selling_currency' => 'required|string|size:3',
            'buying_price' => 'nullable|numeric|min:0',
            'buying_currency' => 'nullable|string|size:3',
            'vat_rate' => 'required|integer|min:0|max:100',
            'unit' => 'required|string|max:255',
            
            'stock_tracking' => 'boolean',
            'stock' => 'nullable|integer',
            'critical_stock_alert' => 'boolean',
            'critical_stock_quantity' => 'nullable|integer',
            
            'status' => 'required|in:active,passive',
        ]);

        if ($request->filled('delete_image') && $request->delete_image == '1') {
            if ($product->image_path) {
                Storage::disk('uploads')->delete($product->image_path);
                $validated['image_path'] = null;
            }
        }

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($product->image_path) {
                Storage::disk('uploads')->delete($product->image_path);
            }
            $tenantId = auth()->user()->tenant_id;
            $path = $request->file('image')->store("tenants/{$tenantId}/products", 'uploads');
            $validated['image_path'] = $path;
        }

        // Handle checkboxes
        $validated['stock_tracking'] = $request->has('stock_tracking');
        $validated['critical_stock_alert'] = $request->has('critical_stock_alert');

        $product->update($validated);

        return redirect()->route('products.show', $product)->with('success', 'Ürün bilgileri güncellendi.');
    }

    public function destroy(Product $product)
    {
        if ($product->image_path) {
            Storage::disk('uploads')->delete($product->image_path);
        }
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Ürün silindi.');
    }

    public function toggleStatus(Product $product)
    {
        $product->status = $product->status === 'active' ? 'passive' : 'active';
        $product->save();

        $message = $product->status === 'active' ? 'Ürün aktif edildi.' : 'Ürün pasife alındı.';
        return back()->with('success', $message);
    }
}
