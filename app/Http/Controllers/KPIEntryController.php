<?php

namespace App\Http\Controllers;

use App\Models\KPIGoal;
use App\Models\KpiEntry;
use App\Models\User;
use App\Models\Position;
use App\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;

class KPIEntryController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Get users and selected user
            $users = User::all();
            $selectedUser = $request->filled('user_id') ? User::find($request->user_id) : auth()->user();

            // Get current month and year
            $currentMonth = $request->filled('month') ? $request->month : Carbon::now()->month;
            $currentYear = $request->filled('year') ? $request->year : Carbon::now()->year;

            // Debug user's position
            \Log::info('Selected User:', [
                'user_id' => $selectedUser->id,
                'position_id' => $selectedUser->position_id
            ]);

            // Get goals based on user's position_id
            $goals = KPIGoal::where('position_id', $selectedUser->position_id)
                        ->orderBy('goal_name')
                        ->get();

            // Debug goals
            \Log::info('Goals found:', [
                'count' => $goals->count(),
                'position_id' => $selectedUser->position_id,
                'goals' => $goals->pluck('goal_name', 'id')
            ]);

            // Get existing KPI entries
            $existingEntries = KpiEntry::where('users_id', $selectedUser->id)
                ->where('month', $currentMonth)
                ->where('year', $currentYear)
                ->get()
                ->keyBy('goals_id');

            $months = [
                1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
                5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
                9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
            ];

            $years = range(date('Y'), date('Y') + 10);

            return view('admin.kpi.kpiEntry.index', compact(
                'goals',
                'existingEntries',
                'users',
                'selectedUser',
                'months',
                'years',
                'currentMonth',
                'currentYear'
            ));

        } catch (\Exception $e) {
            \Log::error('Error in KpiEntryController@index: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while loading the KPI entries.');
        }
    }

    public function create(Request $request)
    {
        $goal = KPIGoal::findOrFail($request->goal_id);
        $user_id = $request->user_id;
        $month = $request->month;
        $year = $request->year;

        return view('admin.kpi.kpiEntry.create', compact('goal', 'user_id', 'month', 'year'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'users_id' => 'required',
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
            'users_id' => $request->users_id,
            'goals_id' => $request->goals_id,
            'actual_result' => $request->actual_result,
            'actual_score' => (int)$actual_score,
            'final_score' => (int)$final_score,
            'month' => $request->month,
            'year' => $request->year,
        ]);

        return redirect()->route('admin.kpi.kpiEntry.index', [
            'user_id' => $request->users_id,
            'month' => $request->month,
            'year' => $request->year
        ])->with('success', 'KPI Entry created successfully');
    }

    public function edit($id)
    {
        $entry = KPIEntry::with('goal')->findOrFail($id);
        return view('admin.kpi.kpiEntry.edit', compact('entry'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'actual_result' => 'required|numeric|between:0,999999.99',
        ]);

        $entry = KpiEntry::findOrFail($id);
        $goal = KPIGoal::findOrFail($entry->goals_id);
        $ranges = json_decode($goal->category_score_ranges, true);
        
        $actual_score = $this->calculateCategoryScore($request->actual_result, $ranges);
        
        // Calculate final score: (actual_score / 4) * goal_score
        $final_score = round(($actual_score / 4) * $goal->goal_score);

        $entry->update([
            'actual_result' => $request->actual_result,
            'actual_score' => (int)$actual_score,
            'final_score' => (int)$final_score,
        ]);

        return redirect()->route('admin.kpi.kpiEntry.index', [
            'user_id' => $entry->users_id,
            'month' => $entry->month,
            'year' => $entry->year,
        ])->with('success', 'KPI Entry updated successfully');
    }

    public function destroy($id)
    {
        try {
            $entry = KpiEntry::findOrFail($id);
            $userId = $entry->users_id;
            $month = $entry->month;
            $year = $entry->year;
            
            $entry->delete();

            return redirect()->route('admin.kpi.kpiEntry.index', [
                'user_id' => $userId,
                'month' => $month,
                'year' => $year
            ])->with('success', 'KPI entry deleted successfully');
        } catch (\Exception $e) {
            \Log::error('Delete Error', [
                'error' => $e->getMessage()
            ]);
            return back()->withErrors(['error' => 'Failed to delete entry']);
        }
    }

    public function getEntry($id)
    {
        $entry = KpiEntry::with('goal')->findOrFail($id);
        return response()->json($entry);
    }

    public function getRanges($goalId)
    {
        $goal = KPIGoal::findOrFail($goalId);
        return response()->json([
            'ranges' => json_decode($goal->category_score_ranges, true),
            'goal_score' => $goal->goal_score
        ]);
    }

    private function calculateScores($actualResult, $goal)
    {
        $ranges = json_decode($goal->category_score_ranges, true);
        $category = '1'; // Default lowest category

        foreach ($ranges as $key => $range) {
            if ($actualResult >= $range['min'] && $actualResult <= $range['max']) {
                $category = str_replace('category_', '', $key);
                break;
            }
        }

        $scoreMultipliers = [
            '1' => 0.2,
            '2' => 0.4,
            '3' => 0.6,
            '4' => 0.8,
            '5' => 1.0,
        ];

        $actualScore = $goal->goal_score * ($scoreMultipliers[$category] ?? 0.2);
        $finalScore = $actualScore;

        return [
            'actual_score' => $actualScore,
            'final_score' => $finalScore,
        ];
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

    public function export(Request $request)
    {
        try {
            $user = User::findOrFail($request->user_id);
            $month = $request->month;
            $year = $request->year;

            $goals = KPIGoal::where('position_id', $user->position_id)->get();
            $entries = KpiEntry::where('users_id', $user->id)
                ->where('month', $month)
                ->where('year', $year)
                ->get()
                ->keyBy('goals_id');

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=kpi_entries_{$user->username}_{$month}_{$year}.csv",
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Pragma' => 'public'
            ];

            $output = fopen('php://output', 'w');
            fputs($output, chr(0xEF) . chr(0xBB) . chr(0xBF)); // Add BOM for Excel

            // Write headers
            fputcsv($output, [
                'Goal Name',
                'Goal Score',
                'Category Ranges',
                'Actual Result',
                'Actual Score',
                'Final Score',
                'Month',
                'Year'
            ]);

            // Write data
            foreach ($goals as $goal) {
                $entry = $entries[$goal->id] ?? null;
                $ranges = json_decode($goal->category_score_ranges, true);
                $rangeText = '';
                
                foreach ($ranges as $category => $range) {
                    $rangeText .= "$category: {$range['min']}-{$range['max']}, ";
                }
                
                fputcsv($output, [
                    $goal->goal_name,
                    $goal->goal_score,
                    rtrim($rangeText, ', '),
                    $entry ? $entry->actual_result : 'N/A',
                    $entry ? $entry->actual_score : 'N/A',
                    $entry ? $entry->final_score : 'N/A',
                    $month,
                    $year
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
