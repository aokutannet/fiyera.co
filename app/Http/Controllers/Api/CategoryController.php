<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('name')->get();
        return response()->json($categories);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $slug = Str::slug($validated['name']);
        
        // Ensure unique slug within tenant
        $originalSlug = $slug;
        $count = 1;
        while (Category::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        $category = Category::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'tenant_id' => auth()->user()->tenant_id,
        ]);

        // Log activity
        ActivityLog::create([
            'user_id' => auth()->id(),
            'subject_type' => Category::class,
            'subject_id' => $category->id,
            'event' => 'created',
            'description' => 'Kategori oluşturuldu (Mobil).',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'properties' => ['source' => 'mobile_api'],
        ]);

        return response()->json([
            'message' => 'Kategori başarıyla oluşturuldu.',
            'category' => $category
        ], 201);
    }
}
