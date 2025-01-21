<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\KPIGoal;
use App\Models\KpiEntry;
use Illuminate\Http\Request;
use Carbon\Carbon;

class UserKpiController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Get current year from request or default to current date
            $currentYear = $request->get('year', Carbon::now()->year);
            
            // Get all goals for user's position
            $goals = KPIGoal::where('position_id', auth()->user()->position_id)
                        ->orderBy('goal_name')
                        ->get();

            // Get existing KPI entries for the selected year
            $existingEntries = KpiEntry::where('users_id', auth()->user()->id)
                ->where('year', $currentYear)
                ->get()
                ->groupBy('month');

            // Months array
            $months = [
                1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
                5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
                9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
            ];

            // Years for dropdown (current year + 10 years)
            $years = range(date('Y'), date('Y') + 10);

            return view('user.kpi.index', compact(
                'goals',
                'existingEntries',
                'months',
                'years',
                'currentYear'
            ));

        } catch (\Exception $e) {
            \Log::error('Error in UserKpiController@index: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while loading the KPI entries.');
        }
    }

    public function manage($month, $year)
    {
        try {
            // Get goals for user's position
            $goals = KPIGoal::where('position_id', auth()->user()->position_id)
                        ->orderBy('goal_name')
                        ->get();

            // Get existing entries for the selected month and year
            $existingEntries = KpiEntry::where('users_id', auth()->user()->id)
                ->where('month', $month)
                ->where('year', $year)
                ->get()
                ->keyBy('goals_id');

            // Calculate totals
            $totalGoalScore = $goals->sum('goal_score');
            $totalFinalScore = $existingEntries->sum('final_score');

            // Get month name
            $months = [
                1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
                5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
                9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
            ];
            $monthName = $months[$month];

            return view('user.kpi.manage', compact(
                'goals',
                'existingEntries',
                'month',
                'year',
                'monthName',
                'totalGoalScore',
                'totalFinalScore'
            ));
        } catch (\Exception $e) {
            \Log::error('Error in UserKpiController@manage: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while loading the KPI management page.');
        }
    }

    public function create(Request $request)
    {
        $goal = KPIGoal::findOrFail($request->goal_id);
        $month = $request->month;
        $year = $request->year;

        return view('user.kpi.create', compact('goal', 'month', 'year'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'goals_id' => 'required',
            'actual_result' => 'required|numeric|between:0,999999.99',
            'month' => 'required',
            'year' => 'required',
        ]);

        $goal = KPIGoal::findOrFail($request->goals_id);
        $ranges = json_decode($goal->category_score_ranges, true);
        
        // Calculate the category score (0 for category 1, 1 for category 2, etc.)
        $actual_score = $this->calculateCategoryScore($request->actual_result, $ranges);
        
        // Calculate final score: (actual_score / 4) * goal_score
        $final_score = round(($actual_score / 4) * $goal->goal_score);

        KpiEntry::create([
            'users_id' => auth()->id(),
            'goals_id' => $request->goals_id,
            'actual_result' => $request->actual_result,
            'actual_score' => (int)$actual_score,
            'final_score' => (int)$final_score,
            'month' => $request->month,
            'year' => $request->year,
        ]);

        return redirect()->route('user.kpi.index', [
            'year' => $request->year
        ])->with('success', 'KPI Entry created successfully');
    }

    private function calculateCategoryScore($actualResult, $ranges)
    {
        foreach ($ranges as $category => $range) {
            if ($actualResult >= $range['min'] && $actualResult < $range['max']) {
                // Extract the category number and subtract 1
                // So category_1 becomes 0, category_2 becomes 1, etc.
                return (int)substr($category, -1) - 1;
            }
        }
        
        // Check if it equals the highest maximum value
        $lastRange = end($ranges);
        if ($actualResult == $lastRange['max']) {
            return count($ranges) - 1; // Return the highest category score (4 for 5 categories)
        }
        
        return 0; // Default to lowest category if no range matches
    }
}