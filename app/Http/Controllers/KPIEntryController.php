<?php

namespace App\Http\Controllers;

use App\Models\KPIGoal;
use App\Models\KpiEntry;
use App\Models\User;
use App\Models\Position;
use App\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;

class KpiEntryController extends Controller
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
            'users_id' => 'required|exists:users,id',
            'goals_id' => 'required|exists:goals,id',
            'actual_result' => 'required|numeric',
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer',
        ]);

        $goal = KPIGoal::findOrFail($request->goals_id);
        $scores = $this->calculateScores($request->actual_result, $goal);

        KpiEntry::create([
            'users_id' => $request->users_id,
            'goals_id' => $request->goals_id,
            'actual_result' => $request->actual_result,
            'actual_score' => $scores['actual_score'],
            'final_score' => $scores['final_score'],
            'month' => $request->month,
            'year' => $request->year,
        ]);

        return redirect()->route('admin.kpi.kpiEntry.index', [
            'user_id' => $request->users_id,
            'month' => $request->month,
            'year' => $request->year,
        ])->with('success', 'KPI entry created successfully');
    }

    public function edit($id)
    {
        $entry = KPIEntry::with('goal')->findOrFail($id);
        return view('admin.kpi.kpiEntry.edit', compact('entry'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'actual_result' => 'required|numeric',
        ]);

        $entry = KPIEntry::findOrFail($id);
        $goal = KPIGoal::findOrFail($entry->goals_id);
        $scores = $this->calculateScores($request->actual_result, $goal);

        $entry->update([
            'actual_result' => $request->actual_result,
            'actual_score' => $scores['actual_score'],
            'final_score' => $scores['final_score'],
        ]);

        return redirect()->route('admin.kpi.kpiEntry.index', [
            'user_id' => $entry->users_id,
            'month' => $entry->month,
            'year' => $entry->year,
        ])->with('success', 'KPI entry updated successfully');
    }

    public function destroy($id)
    {
        $entry = KpiEntry::findOrFail($id);
        $entry->delete();

        return response()->json([
            'success' => true,
            'message' => 'KPI entry deleted successfully'
        ]);
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
}
