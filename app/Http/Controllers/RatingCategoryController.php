<?php

namespace App\Http\Controllers;

use App\Models\RatingCategory;
use Illuminate\Http\Request;

class RatingCategoryController extends Controller
{
    public function index()
    {
        $ratingCategories = RatingCategory::all();
        return view('rating_categories.index', compact('ratingCategories'));
    }

    public function create()
    {
        return view('rating_categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:rating_categories,name',
        ]);

        RatingCategory::create($request->all());

        return redirect()->route('rating_categories.index');
    }

    public function edit($id)
    {
        $ratingCategory = RatingCategory::findOrFail($id);
        return view('rating_categories.edit', compact('ratingCategory'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|unique:rating_categories,name,' . $id,
        ]);

        $ratingCategory = RatingCategory::findOrFail($id);
        $ratingCategory->update($request->all());

        return redirect()->route('rating_categories.index');
    }

    public function destroy($id)
    {
        RatingCategory::destroy($id);
        return redirect()->route('rating_categories.index');
    }
}
