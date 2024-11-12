<?php

namespace App\Http\Controllers;

use App\Models\KPI;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function store(Request $request, $kpiId)
    {
        $validated = $request->validate([
            'category_name' => 'required|string|max:255',
        ]);

        $kpi = KPI::findOrFail($kpiId);

        // Create the category for the specific KPI
        $category = $kpi->categories()->create([
            'name' => $validated['category_name'],
        ]);

        return redirect()->route('admin.kpi.create', ['kpiId' => $kpiId, 'categoryId' => $category->id]);
    }
}
