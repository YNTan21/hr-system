<?php

namespace App\Http\Controllers;

use App\Models\KPI;
use App\Models\Position;
use App\Models\KPICategory;
use App\Models\KPIGoal;
use App\Models\KPIGoalRatingCategory;
use App\Models\KPIRatingThreshold;
use Illuminate\Http\Request;

class KpiController extends Controller
{

    // Store the created KPI with categories, goals, and rating categories
    public function store(Request $request)
    {
        // Validate input data
        $request->validate([
            'position_id' => 'required|exists:positions,id',
            'categories' => 'required|array',
            'categories.*.name' => 'required|string',
            'categories.*.goals' => 'required|array',
            'categories.*.goals.*.goal' => 'required|string',
            'categories.*.goals.*.score' => 'required|integer',
            'categories.*.goals.*.goal_type' => 'required|in:monthly,yearly',
            'categories.*.rating_categories' => 'required|array',
            'categories.*.rating_categories.*.min_score' => 'required|integer',
            'categories.*.rating_categories.*.max_score' => 'required|integer',
        ]);

        // Create the KPI (linked to a Position)
        $kpi = Kpi::create([
            'position_id' => $request->position_id,
        ]);

        // Loop through categories and save them
        foreach ($request->categories as $categoryData) {
            // Create the category and associate it with the KPI
            $category = Category::create([
                'name' => $categoryData['name'],
                'kpi_id' => $kpi->id,
            ]);

            // Add goals for each category
            foreach ($categoryData['goals'] as $goalData) {
                $goal = Goal::create([
                    'category_id' => $category->id,
                    'goal' => $goalData['goal'],
                    'score' => $goalData['score'],
                    'goal_type' => $goalData['goal_type'],
                ]);

                // Loop through the rating categories and save thresholds for this goal
                foreach ($categoryData['rating_categories'] as $ratingCategoryData) {
                    // Save the rating category and its thresholds
                    $ratingCategory = RatingCategory::create([
                        'name' => $ratingCategoryData['name'],
                    ]);

                    // Save rating thresholds (min and max score)
                    RatingThreshold::create([
                        'rating_category_id' => $ratingCategory->id,
                        'goal_id' => $goal->id, // Link threshold to the goal
                        'min_score' => $ratingCategoryData['min_score'],
                        'max_score' => $ratingCategoryData['max_score'],
                    ]);
                }
            }
        }

        return redirect()->route('admin.kpi.index')->with('success', 'KPI created successfully!');
    }

    public function index()
    {
        $positions = Position::all();
        // Fetch all positions with their associated KPIs
        $positions = Position::with('kpis')->get(); // Assuming the relationship is defined in the Position model
        return view('admin.kpi.index', compact('positions'));
    }

    // Show the form to create a KPI
    public function create($position_id)
    {
        // $positions = Position::all(); // All positions
        // Retrieve the position by its ID
        $positions = Position::findOrFail($position_id);
        return view('admin.kpi.create', compact('positions'));
    }

    public function manage($position_id)
    {
        $positions = Position::findOrFail($position_id);
    
        // Directly fetch goals associated with this position
        $goals = KPIGoal::where('position_id', $position_id)->get();
        
        return view('admin.kpi.manage', compact('positions', 'goals'));
    }
}
