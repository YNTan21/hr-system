<?php

namespace App\Http\Controllers;

use App\Models\KPIGoal;
use App\Models\KpiEntry;
use App\Models\User;
use App\Models\Position;
use App\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Goal;
use Illuminate\Support\Facades\DB;

class KPIEntryController extends Controller
{
    public function index(Request $request)
    {
        // Define months array
        $months = [
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December'
        ];

        // Generate years array (5 years before and after current year)
        $currentYear = now()->year;
        $years = range($currentYear - 5, $currentYear + 5);

        // Get the selected month and year, default to current if not provided
        $currentMonth = $request->get('month', now()->month);
        $currentYear = $request->get('year', $currentYear);
        
        // Get the selected user, default to first admin if not provided
        $selectedUser = null;
        if ($request->has('user_id')) {
            $selectedUser = User::findOrFail($request->user_id);
        } else {
            // Redirect to the same page with a user_id parameter to prevent loops
            $firstUser = User::first();
            if ($firstUser) {
                return redirect()->route('admin.kpi.kpiEntry.index', [
                    'user_id' => $firstUser->id,
                    'month' => $currentMonth,
                    'year' => $currentYear
                ]);
            }
        }

        if (!$selectedUser) {
            return back()->with('error', 'No users found in the system.');
        }

        // Get goals based on user's position
        $goals = KPIGoal::where('position_id', $selectedUser->position_id)->get();

        // Get existing entries for the selected month/year/user
        $existingEntries = KpiEntry::where([
            'users_id' => $selectedUser->id,
            'month' => $currentMonth,
            'year' => $currentYear,
        ])->get()->keyBy('goals_id');

        // Get all users for the dropdown
        $users = User::all();

        // 检查每个目标是否有被还原的历史记录
        $hasRevertedHistory = [];
        foreach ($goals as $goal) {
            $hasRevertedHistory[$goal->id] = KpiEntry::onlyTrashed()  // 使用 onlyTrashed() 来查找被软删除的记录
                ->where([
                    'goals_id' => $goal->id,
                    'users_id' => $selectedUser->id,
                    'month' => $currentMonth,
                    'year' => $currentYear
                ])
                ->exists();  // 如果有被软删除的记录，说明有还原历史
        }

        return view('admin.kpi.kpiEntry.index', compact(
            'goals',
            'users',
            'selectedUser',
            'months',
            'years',
            'currentMonth',
            'currentYear',
            'existingEntries',
            'hasRevertedHistory'
        ));
    }

    public function create(Request $request)
    {
        $goalId = $request->goal_id;
        $month = $request->month;
        $year = $request->year;
        $userId = $request->user_id;

        // Check if there's an existing entry
        $existingEntry = KpiEntry::where([
            'goals_id' => $goalId,
            'users_id' => $userId,
            'month' => $month,
            'year' => $year
        ])->first();

        // Get the goal and user data
        $goal = KPIGoal::findOrFail($goalId);
        $user = User::findOrFail($userId);

        // If there's a reverted entry, update it instead of creating new
        if ($existingEntry && $existingEntry->status === 'reverted') {
            return view('admin.kpi.kpiEntry.edit', compact('entry', 'goal', 'month', 'year', 'user'));
        }

        // Otherwise proceed with normal create
        return view('admin.kpi.kpiEntry.create', compact('goal', 'month', 'year', 'user'));
    }

    public function store(Request $request)
    {
        try {
            \Log::info('Starting KPI Entry store', [
                'request_data' => $request->all()
            ]);

            $validated = $request->validate([
                'actual_result' => 'required|numeric',
                'goals_id' => 'required|exists:goals,id',
                'users_id' => 'required|exists:users,id',
                'month' => 'required',
                'year' => 'required'
            ]);

            $goal = KPIGoal::findOrFail($request->goals_id);
            $ranges = json_decode($goal->category_score_ranges, true);
            $actualScore = 0;
            
            // 根据实际结果和范围确定 category (0-4)
            foreach ($ranges as $category => $range) {
                if ($request->actual_result >= $range['min'] && $request->actual_result <= $range['max']) {
                    // 从 category_1 中提取数字并减1 (因为范围是0-4)
                    $actualScore = (int)substr($category, -1) - 1;
                    break;
                }
            }

            // 计算 final_score：(actual_score / 4) * goal_score
            $finalScore = ($actualScore / 4) * $goal->goal_score;

            DB::beginTransaction();
            try {
                $entry = new KpiEntry();
                $entry->users_id = $request->users_id;
                $entry->goals_id = $request->goals_id;
                $entry->actual_result = $request->actual_result;
                $entry->actual_score = $actualScore;
                $entry->final_score = $finalScore;
                $entry->month = $request->month;
                $entry->year = $request->year;
                $entry->status = 'pending';

                $saved = $entry->save();

                if (!$saved) {
                    throw new \Exception('Failed to save entry');
                }

                DB::commit();

                return redirect()->route('admin.kpi.kpiEntry.index', [
                    'user_id' => $request->users_id,
                    'month' => $request->month,
                    'year' => $request->year
                ])->with('success', 'KPI entry has been created successfully.');

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            \Log::error('KPI Entry Store Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withInput()
                ->with('error', 'Failed to create KPI entry: ' . $e->getMessage());
        }
    }

    private function calculateScore($actualResult, $goal)
    {
        $ranges = json_decode($goal->category_score_ranges, true);
        $score = 0;

        foreach ($ranges as $category => $range) {
            if ($actualResult >= $range['min'] && $actualResult <= $range['max']) {
                switch ($category) {
                    case 'category_1': $score = 0; break;
                    case 'category_2': $score = 1; break;
                    case 'category_3': $score = 2; break;
                    case 'category_4': $score = 3; break;
                    case 'category_5': $score = 4; break;
                }
                break;
            }
        }

        return $score;
    }

    public function edit($id)
    {
        $entry = KPIEntry::with('goal')->findOrFail($id);
        return view('admin.kpi.kpiEntry.edit', compact('entry'));
    }

    public function update(Request $request, $id)
    {
        try {
            $entry = KpiEntry::findOrFail($id);
            $goal = KPIGoal::findOrFail($entry->goals_id);
            
            $validated = $request->validate([
                'actual_result' => 'required|numeric|between:0,999999.99',
            ]);

            $ranges = json_decode($goal->category_score_ranges, true);
            $actualScore = 0;
            
            foreach ($ranges as $category => $range) {
                if ($request->actual_result >= $range['min'] && $request->actual_result <= $range['max']) {
                    $actualScore = $category;
                    break;
                }
            }

            $entry->actual_result = $request->actual_result;
            $entry->actual_score = $actualScore;
            $entry->final_score = $actualScore * ($goal->goal_score / 5);
            $entry->save();

            return redirect()->route('admin.kpi.kpiEntry.index', [
                'user_id' => $entry->users_id,
                'month' => $entry->month,
                'year' => $entry->year,
            ])->with('success', 'KPI Entry updated successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update KPI entry: ' . $e->getMessage());
        }
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

    public function approve($id)
    {
        $entry = KpiEntry::findOrFail($id);
        
        try {
            $entry->update([
                'status' => 'approved'
            ]);

            return redirect()->route('admin.kpi.kpiEntry.index', [
                'user_id' => $entry->users_id,
                'month' => $entry->month,
                'year' => $entry->year
            ])->with('success', 'KPI entry has been approved successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to approve KPI entry: ' . $e->getMessage());
        }
    }

    public function reject(KpiEntry $entry)
    {
        $entry->update([
            'status' => 'rejected'
        ]);

        return back()->with('success', 'KPI entry has been rejected. User can now edit the entry again.');
    }

    public function revert($id)
    {
        $entry = KpiEntry::findOrFail($id);
        
        try {
            // Store the current values in reverted fields before clearing
            $entry->update([
                'reverted_actual_result' => $entry->actual_result,
                'reverted_actual_score' => $entry->actual_score,
                'reverted_at' => now(),
            ]);

            // Delete the current entry
            $entry->delete();

            return redirect()->route('admin.kpi.kpiEntry.index', [
                'user_id' => $entry->users_id,
                'month' => $entry->month,
                'year' => $entry->year
            ])->with('success', 'KPI entry has been reverted to no entry status.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to revert KPI entry: ' . $e->getMessage());
        }
    }

    public function history($goal_id, $user_id, $month, $year)
    {
        $entries = KpiEntry::onlyTrashed()
            ->where([
                'goals_id' => $goal_id,
                'users_id' => $user_id,
                'month' => $month,
                'year' => $year
            ])
            ->orderBy('deleted_at', 'asc')  // 改为升序，最早的记录在前
            ->get();

        if ($entries->isEmpty()) {
            return response()->json([
                'entries' => []
            ]);
        }

        $formattedEntries = $entries->map(function($entry, $index) {
            return [
                'sequence' => $index + 1,  // 添加序号
                'date' => $entry->deleted_at->format('Y-m-d'),
                'result' => number_format($entry->actual_result, 2)
            ];
        });

        return response()->json([
            'entries' => $formattedEntries
        ]);
    }
}
