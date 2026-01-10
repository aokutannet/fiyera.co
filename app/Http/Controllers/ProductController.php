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

    public function export(Request $request)
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

        $products = $query->with('productCategory')->get();
        $format = $request->query('format', 'excel');

        // Prepare Data
        $data = [];
        foreach ($products as $product) {
            $vatMultiplier = 1 + ($product->vat_rate / 100);
            
            $data[] = [
                'ID' => $product->id,
                'Ürün Kodu' => $product->code,
                'Ürün Adı'  => $product->name,
                'Kategori'  => $product->productCategory->name ?? '',
                'Stok Adeti' => $product->stock,
                'Kdv Oranı' => $product->vat_rate,
                'Satış Birimi' => $product->unit,
                'Alış Fiyatı Vergiler Hariç' => number_format($product->buying_price, 2, ',', ''),
                'Alış Fiyatı Vergiler Dahil' => number_format($product->buying_price * $vatMultiplier, 2, ',', ''),
                'Satış Fiyatı Vergiler Hariç' => number_format($product->price, 2, ',', ''),
                'Satış Fiyatı Vergiler Dahil' => number_format($product->price * $vatMultiplier, 2, ',', ''),
                'Durum' => $product->status === 'active' ? 'Aktif' : 'Pasif'
            ];
        }

        $fileName = 'urunler_' . date('Y-m-d_H-i');

        if ($format === 'csv') {
            $headers = [
                "Content-type"        => "text/csv; charset=utf-8",
                "Content-Disposition" => "attachment; filename=$fileName.csv",
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            ];
            
            $callback = function() use ($data) {
                $file = fopen('php://output', 'w');
                
                // Add BOM for Excel UTF-8 compatibility
                fputs($file, "\xEF\xBB\xBF");
                
                if (!empty($data)) {
                    fputcsv($file, array_keys($data[0]), ";"); // Semicolon for Excel compatibility in TR
                    foreach ($data as $row) {
                        fputcsv($file, $row, ";");
                    }
                }
                fclose($file);
            };
            
            return response()->stream($callback, 200, $headers);

        } elseif ($format === 'xml') {
            $headers = [
                'Content-Type' => 'application/xml',
                'Content-Disposition' => "attachment; filename=$fileName.xml",
            ];

            $xml = new \SimpleXMLElement('<products/>');
            foreach ($data as $item) {
                $productNode = $xml->addChild('product');
                foreach ($item as $key => $value) {
                    // Safe key generation
                    $search = ['ç', 'Ç', 'ğ', 'Ğ', 'ı', 'İ', 'ö', 'Ö', 'ş', 'Ş', 'ü', 'Ü'];
                    $replace = ['c', 'C', 'g', 'G', 'i', 'I', 'o', 'O', 's', 'S', 'u', 'U'];
                    $key = str_replace($search, $replace, $key);
                    
                    $key = str_replace(' ', '_', $key);
                    $key = preg_replace('/[^a-z0-9_]/i', '', $key);
                    $productNode->addChild($key, htmlspecialchars($value ?? ''));
                }
            }

            return response($xml->asXML(), 200, $headers);

        } else {
            // Excel (HTML Table method)
            $headers = [
                "Content-Type" => "application/vnd.ms-excel; charset=utf-8",
                "Content-Disposition" => "attachment; filename=$fileName.xls",
                "Pragma" => "no-cache", 
                "Expires" => "0"
            ];

            $content = '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
            $content .= '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head>';
            $content .= '<body>';
            $content .= '<table border="1">';
            
            if (!empty($data)) {
                // Headings
                $content .= '<thead><tr>';
                foreach (array_keys($data[0]) as $key) {
                    $content .= '<th style="background-color:#f3f4f6; font-weight:bold;">' . $key . '</th>';
                }
                $content .= '</tr></thead>';
                
                // Body
                $content .= '<tbody>';
                foreach ($data as $row) {
                    $content .= '<tr>';
                    foreach ($row as $cell) {
                        $content .= '<td>' . htmlspecialchars($cell ?? '') . '</td>';
                    }
                    $content .= '</tr>';
                }
                $content .= '</tbody>';
            }
            
            $content .= '</table></body></html>';

            return response($content, 200, $headers);
        }


    }

    public function analyzeImport(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xml,xls,xlsx,html'
        ]);

        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();
        $path = $file->getRealPath();
        
        $items = [];

        // Parsing Logic
        if ($extension === 'csv' || $extension === 'txt') {
            if (($handle = fopen($path, 'r')) !== FALSE) {
                // Remove BOM
                $bom = "\xEF\xBB\xBF";
                $firstLine = fgets($handle);
                if (str_starts_with($firstLine, $bom)) {
                    $firstLine = substr($firstLine, 3);
                }
                // Rewind isn't enough because we read line 1. Reset manually or seek. 
                // Easier: just parse content.
                // Re-open clean
                fclose($handle);
                $handle = fopen($path, 'r');
                // Skip BOM if exists
                if (fgets($handle, 4) !== $bom) {
                   rewind($handle);
                }
                
                $headers = fgetcsv($handle, 1000, ";");
                
                // If headers not found or empty, try comma
                if (!$headers || count($headers) < 2) {
                    rewind($handle);
                    if (fgets($handle, 4) !== $bom) { rewind($handle); }
                    $headers = fgetcsv($handle, 1000, ",");
                }

                while (($data = fgetcsv($handle, 1000, str_contains(implode('', $headers ?? []), ';') ? ";" : ",")) !== FALSE) {
                    if (count($data) === count($headers)) {
                        $items[] = array_combine($headers, $data);
                    }
                }
                fclose($handle);
            }
        } elseif ($extension === 'xml') {
            $xml = simplexml_load_file($path);
            foreach ($xml->children() as $child) {
                $row = [];
                foreach ($child as $key => $value) {
                    $row[str_replace('_', ' ', $key)] = (string)$value;
                }
                $items[] = $row;
            }
        } else {
            // Assume HTML Table (XLS export format)
            $content = file_get_contents($path);
            $dom = new \DOMDocument;
            libxml_use_internal_errors(true);
            // Hack to force UTF-8
            $dom->loadHTML('<?xml encoding="utf-8" ?>' . $content);
            libxml_clear_errors();
            
            $rows = $dom->getElementsByTagName('tr');
            $headers = [];
            
            foreach ($rows as $index => $row) {
                $cells = $row->getElementsByTagName($index === 0 ? 'th' : 'td');
                $rowData = [];
                
                foreach ($cells as $cell) {
                    $val = trim($cell->nodeValue);
                    if ($index === 0) {
                        $headers[] = $val;
                    } else {
                        $rowData[] = $val;
                    }
                }
                
                if ($index > 0 && !empty($rowData)) {
                    // Fill missing keys if row shorter than header
                    $validRow = [];
                    foreach ($headers as $i => $h) {
                        $validRow[$h] = $rowData[$i] ?? '';
                    }
                    $items[] = $validRow;
                }
            }
        }

        // Detect Header Mismatch
        $firstItem = reset($items);
        $fileHeaders = $firstItem ? array_keys($firstItem) : [];
        $standardHeaders = ['ID', 'Ürün Kodu', 'Ürün Adı', 'Kategori', 'Stok Adeti', 'Kdv Oranı', 'Satış Birimi', 'Alış Fiyatı Vergiler Hariç', 'Satış Fiyatı Vergiler Hariç', 'Durum'];
        
        // Count matching headers
        $matches = count(array_intersect($fileHeaders, $standardHeaders));
        
        // If low match rate, suggest mapping
        if ($matches < 3 && count($items) > 0) {
            $key = \Illuminate\Support\Str::uuid();
            \Illuminate\Support\Facades\Cache::put("import_raw_{$key}", $items, 600);
            
            return response()->json([
                'status' => 'mapping_required',
                'key' => (string)$key,
                'headers' => $fileHeaders,
                'required_fields' => [
                    'code' => 'Ürün Kodu',
                    'name' => 'Ürün Adı (Zorunlu)',
                    'category_name' => 'Kategori',
                    'stock' => 'Stok Adeti',
                    'price' => 'Satış Fiyatı',
                    'buying_price' => 'Alış Fiyatı',
                    'vat_rate' => 'KDV Oranı',
                    'unit' => 'Birim',
                    'status' => 'Durum'
                ]
            ]);
        }

        // Standard Processing
        return $this->processItems($items);
    }

    public function mapImport(Request $request)
    {
        $key = $request->key;
        $items = \Illuminate\Support\Facades\Cache::get("import_raw_{$key}");
        $mapping = $request->mapping; // ['code' => 'FileHeader1', 'name' => 'FileHeader2']

        if (!$items) {
            return response()->json(['message' => 'Oturum süresi doldu.'], 404);
        }

        $remappedItems = [];
        foreach ($items as $item) {
            $newItem = [];
            
            // Map known standard keys from the user mapping
            $reverseMap = [
                'code' => 'Ürün Kodu',
                'name' => 'Ürün Adı',
                'category_name' => 'Kategori',
                'stock' => 'Stok Adeti',
                'price' => 'Satış Fiyatı Vergiler Hariç',
                'buying_price' => 'Alış Fiyatı Vergiler Hariç',
                'vat_rate' => 'Kdv Oranı',
                'unit' => 'Satış Birimi',
                'status' => 'Durum'
            ];

            foreach ($mapping as $systemField => $fileHeader) {
                if ($fileHeader && isset($item[$fileHeader])) {
                    // We map it to the "Standard Key" so processItems can read it uniformly
                    $standardKey = $reverseMap[$systemField] ?? null;
                    if ($standardKey) {
                        $newItem[$standardKey] = $item[$fileHeader];
                    }
                }
            }
            // Preserve ID if it happens to exist/be mapped (though usually manual map implies no system ID)
            if (isset($item['ID'])) $newItem['ID'] = $item['ID'];

            $remappedItems[] = $newItem;
        }

        return $this->processItems($remappedItems);
    }

    private function processItems($items)
    {
        $analysis = [
            'new_count' => 0,
            'update_count' => 0,
            'log' => []
        ];

        $parsedItems = [];
        $existingCodes = Product::pluck('id', 'code')->toArray();

        foreach ($items as $item) {
            // Normalize Keys (handle different casing/spacing if needed, but assuming strict from export)
            // Map Export Headers to DB Columns
            $id = $item['ID'] ?? $item['id'] ?? null;
            $code = $item['Ürün Kodu'] ?? $item['product_code'] ?? null;
            
            // Priority: ID check for Update
            $isUpdate = false;
            $existingId = null;

            if ($id && Product::where('id', $id)->exists()) {
                 $isUpdate = true;
                 $existingId = $id;
            } elseif ($code && isset($existingCodes[$code])) {
                 $isUpdate = true;
                 $existingId = $existingCodes[$code];
            }

            // Note: If no code and no ID, we might skip or create with null code. 
            // Assuming at least one identifier is needed or we treat as new with generated stuff?
            // User requirement implies we want to update if ID exists.
            
            if ($isUpdate) {
                $analysis['update_count']++;
            } else {
                $analysis['new_count']++;
            }

            // Prepare DB data
            $stock = (int)($item['Stok Adeti'] ?? 0);
            
            $productData = [
                'code' => $code,
                'name' => $item['Ürün Adı'] ?? $item['name'] ?? 'İsimsiz Ürün',
                'category_name' => $item['Kategori'] ?? '', // Will resolve ID later
                'stock' => $stock,
                'vat_rate' => (int)str_replace('%', '', $item['Kdv Oranı'] ?? '18'),
                'unit' => $item['Satış Birimi'] ?? 'Adet',
                'buying_price' => $this->parsePrice($item['Alış Fiyatı Vergiler Hariç'] ?? '0'),
                'price' => $this->parsePrice($item['Satış Fiyatı Vergiler Hariç'] ?? '0'),
                'status' => ($item['Durum'] ?? '') === 'Aktif' ? 'active' : 'passive',
                'selling_currency' => 'TRY', // Default
                'buying_currency' => 'TRY' // Default
            ];

            // User Rule: If stock is provided (>0), enable stock tracking automatically.
            if ($stock > 0) {
                $productData['stock_tracking'] = 1;
            }

            $parsedItems[] = [
                'is_update' => $isUpdate,
                'id' => $isUpdate ? $existingId : null,
                'data' => $productData
            ];
        }

        // Cache the parsed items for 10 minutes
        $key = \Illuminate\Support\Str::uuid();
        \Illuminate\Support\Facades\Cache::put("import_{$key}", $parsedItems, 600);

        return response()->json([
            'status' => 'ready',
            'key' => (string)$key,
            'stats' => $analysis
        ]);
    }

    private function parsePrice($priceStr)
    {
        // 1.250,50 -> 1250.50
        // Remove dots (thousands)
        $priceStr = str_replace('.', '', $priceStr);
        // Replace comma with dot
        $priceStr = str_replace(',', '.', $priceStr);
        return (float)$priceStr;
    }

    public function executeImport(Request $request)
    {
        $key = $request->key;
        $items = \Illuminate\Support\Facades\Cache::get("import_{$key}");

        if (!$items) {
            return response()->json(['message' => 'Oturum süresi doldu, lütfen dosyayı tekrar yükleyin.'], 404);
        }

        $count = 0;
        foreach ($items as $item) {
            $data = $item['data'];
            
            // Resolve Category
            if (!empty($data['category_name'])) {
                $category = Category::firstOrCreate(
                    ['name' => $data['category_name']],
                    ['status' => 'active'] // Defaults if creating
                );
                $data['category_id'] = $category->id;
            }
            unset($data['category_name']);

            if ($item['is_update']) {
                $product = Product::find($item['id']);
                if ($product) {
                    $product->update($data);
                }
            } else {
                $data['stock_tracking'] = 1; // Default ON for import
                Product::create($data);
            }
            $count++;
        }

        \Illuminate\Support\Facades\Cache::forget("import_{$key}");

        return response()->json(['message' => "{$count} ürün başarıyla işlendi.", 'count' => $count]);
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
    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:tenant.products,id'
        ]);

        $count = 0;
        foreach ($request->ids as $id) {
            $product = Product::find($id);
            if ($product) {
                if ($product->image_path) {
                    Storage::disk('uploads')->delete($product->image_path);
                }
                $product->delete();
                $count++;
            }
        }

        return redirect()->route('products.index')->with('success', "{$count} adet ürün başarıyla silindi.");
    }
}
