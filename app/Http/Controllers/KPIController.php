<?php

namespace App\Http\Controllers;

use App\Models\KPI;
use App\Models\Position;
use App\Models\KPICategory;
use App\Models\KPIGoal;
use App\Models\KPIGoalRatingCategory;
use App\Models\KPIRatingThreshold;
use Illuminate\Http\Request;

class KPIController extends Controller
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
            'categories.*.goals.*.score' => 'required|numeric',
            'categories.*.goals.*.goal_type' => 'required|in:monthly,yearly',
            'categories.*.rating_categories' => 'required|array',
            'categories.*.rating_categories.*.min_score' => 'required|numeric',
            'categories.*.rating_categories.*.max_score' => 'required|numeric',
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
        // Fetch positions with their KPIs and goals
        $positions = Position::with(['kpis', 'goals'])
            ->when(request('position_id'), function($query, $positionId) {
                return $query->where('id', $positionId);
            })
            ->get();

        // Transform the data to include goal counts
        $positions->each(function ($position) {
            // Count goals directly associated with this position
            $goalsCount = $position->goals()->count();
            $position->goals_count = $goalsCount;
            
            // Set KPI status based on goals existence
            $position->has_kpi = $goalsCount > 0;
        });

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

    public function update(Request $request, $position_id, $id)
    {
        // Validate the incoming request data
        $request->validate([
            'goal' => 'required|string',
            'score' => 'required|numeric',
            'goal_type' => 'required|in:monthly,yearly',
        ]);

        // Find the goal by ID
        $goal = KPIGoal::findOrFail($id);

        // Update the goal with the validated data
        $goal->update([
            'goal' => $request->input('goal'),
            'score' => $request->input('score'),
            'goal_type' => $request->input('goal_type'),
        ]);

        // Redirect back to the manage page with a success message
        return redirect()->route('admin.kpi.manage', ['position_id' => $position_id])
                         ->with('success', 'Goal updated successfully!');
    }

    public function export()
    {
        try {
            $positions = Position::with('kpis')->get();

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename=kpi_records.csv',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Pragma' => 'public'
            ];

            $output = fopen('php://output', 'w');
            fputs($output, chr(0xEF) . chr(0xBB) . chr(0xBF)); // Add BOM for Excel

            // Write headers
            fputcsv($output, [
                'Position Name',
                'Goal Name',
                'Goal Score',
                'Goal Type',
                'Goal Unit',
                'Category Ranges',
                'Created Date'
            ]);

            // Write data
            foreach ($positions as $position) {
                foreach ($position->kpis as $kpi) {
                    $goals = KPIGoal::where('position_id', $position->id)->get();
                    foreach ($goals as $goal) {
                        fputcsv($output, [
                            $position->position_name,
                            $goal->goal_name,
                            $goal->goal_score,
                            $goal->goal_type,
                            $goal->goal_unit,
                            json_encode($goal->category_score_ranges),
                            date('d/m/Y', strtotime($goal->created_at))
                        ]);
                    }
                }
            }

            fclose($output);

            return response()->stream(
                function() {
                    // Data has already been written to output
                },
                200,
                $headers
            );

        } catch (\Exception $e) {
            \Log::error('Export Error', [
                'error' => $e->getMessage()
            ]);
            return back()->withErrors(['error' => 'Failed to export data']);
        }
    }

    public function exportManage($position_id)
    {
        try {
            $position = Position::findOrFail($position_id);
            $goals = KPIGoal::where('position_id', $position_id)->get();

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename=kpi_goals_' . $position->position_name . '.csv',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Pragma' => 'public'
            ];

            $output = fopen('php://output', 'w');
            fputs($output, chr(0xEF) . chr(0xBB) . chr(0xBF)); // Add BOM for Excel

            // Write headers
            fputcsv($output, [
                'Goal Name',
                'Type',
                'Unit',
                'Score',
                'Failed Range',
                'Below Expectation Range',
                'Threshold Range',
                'Meet Target Range',
                'Excellence Range',
                'Created Date'
            ]);

            // Write data
            foreach ($goals as $goal) {
                $categoryScoreRanges = json_decode($goal->category_score_ranges, true);
                
                fputcsv($output, [
                    $goal->goal_name,
                    ucfirst($goal->goal_type),
                    $goal->goal_unit,
                    $goal->goal_score,
                    ($categoryScoreRanges['category_1']['min'] ?? 'N/A') . ' - ' . ($categoryScoreRanges['category_1']['max'] ?? 'N/A'),
                    ($categoryScoreRanges['category_2']['min'] ?? 'N/A') . ' - ' . ($categoryScoreRanges['category_2']['max'] ?? 'N/A'),
                    ($categoryScoreRanges['category_3']['min'] ?? 'N/A') . ' - ' . ($categoryScoreRanges['category_3']['max'] ?? 'N/A'),
                    ($categoryScoreRanges['category_4']['min'] ?? 'N/A') . ' - ' . ($categoryScoreRanges['category_4']['max'] ?? 'N/A'),
                    ($categoryScoreRanges['category_5']['min'] ?? 'N/A') . ' - ' . ($categoryScoreRanges['category_5']['max'] ?? 'N/A'),
                    date('d/m/Y', strtotime($goal->created_at))
                ]);
            }

            fclose($output);

            return response()->stream(
                function() {
                    // Data has already been written to output
                },
                200,
                $headers
            );

        } catch (\Exception $e) {
            \Log::error('Export Error', [
                'error' => $e->getMessage()
            ]);
            return back()->withErrors(['error' => 'Failed to export data']);
        }
    }
}
