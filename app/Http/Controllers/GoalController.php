<?php

namespace App\Http\Controllers;

use App\Models\KPI;
use App\Models\Category;
use App\Models\KPIGoal;
use App\Models\Position;
use Illuminate\Http\Request;

class GoalController extends Controller
{
    public function create($position_id)
    {
        // Retrieve the position by ID
        $positions = Position::findOrFail($position_id);

        // Return the view with the position data
        return view('admin.kpi.create', compact('positions'));
    }
    
    public function store(Request $request, $position_id)
    {
        // Validate the form input
        $request->validate([
            'goal_name' => 'required|string|max:255',
            'goal_score' => 'required|integer',
            'goal_type' => 'required|in:monthly,yearly',
            'goal_unit' => 'required|string|max:255',
            'category_1_min' => 'nullable|integer',
            'category_1_max' => 'required|integer',
            'category_2_min' => 'required|integer',
            'category_2_max' => 'required|integer',
            'category_3_min' => 'required|integer',
            'category_3_max' => 'required|integer',
            'category_4_min' => 'required|integer',
            'category_4_max' => 'required|integer',
            'category_5_min' => 'required|integer',
            'category_5_max' => 'nullable|integer',
        ]);

        // Prepare category score ranges as an array
        $categoryScoreRanges = [
            'category_1' => ['min' => $request->category_1_min ?? null, 'max' => $request->category_1_max],
            'category_2' => ['min' => $request->category_2_min, 'max' => $request->category_2_max],
            'category_3' => ['min' => $request->category_3_min, 'max' => $request->category_3_max],
            'category_4' => ['min' => $request->category_4_min, 'max' => $request->category_4_max],
            'category_5' => ['min' => $request->category_5_min, 'max' => $request->category_5_max ?? null],
        ];
        

        // Create the Goal
        $goals = KPIGoal::create([
            'position_id' => $position_id,
            'goal_name' => $request->goal_name,
            'goal_score' => $request->goal_score, 
            'goal_type' => $request->goal_type,
            'goal_unit' => $request->goal_unit,
            'category_score_ranges' => json_encode($categoryScoreRanges), // Store the ranges as JSON
        ]);

        // Redirect back with a success message
        return redirect()->route('admin.kpi.manage', ['position_id' => $goals->position_id])
            ->with('success', 'Goal created successfully.');
    }

    public function edit($position_id, $id)
    {
        // Retrieve the position by ID
        $positions = Position::findOrFail($position_id);
        
        $goals = KPIGoal::findOrFail($id);
        return view('admin.kpi.edit', compact('positions','goals'));
    }

    public function update(Request $request, Goal $goal)
    {
        // Validate data, including goal_unit
        $request->validate([
            'goal_name' => 'required|string',
            'goal_score' => 'required|integer',
            'goal_type' => 'required|in:monthly,yearly',
            'goal_unit' => 'required|string|max:255',
            'category_1_min' => 'nullable|integer',
            'category_1_max' => 'required|integer',
            'category_2_min' => 'required|integer',
            'category_2_max' => 'required|integer',
            'category_3_min' => 'required|integer',
            'category_3_max' => 'required|integer',
            'category_4_min' => 'required|integer',
            'category_4_max' => 'required|integer',
            'category_5_min' => 'required|integer',
            'category_5_max' => 'nullable|integer',
        ]);

        // Prepare category score ranges as an array
        $categoryScoreRanges = [
            'category_1' => ['min' => $request->category_1_min, 'max' => $request->category_1_max],
            'category_2' => ['min' => $request->category_2_min, 'max' => $request->category_2_max],
            'category_3' => ['min' => $request->category_3_min, 'max' => $request->category_3_max],
            'category_4' => ['min' => $request->category_4_min, 'max' => $request->category_4_max],
            'category_5' => ['min' => $request->category_5_min, 'max' => $request->category_5_max],
        ];

        // Update the goal with new data
        $goals->update([
            'goal_name' => $request->goal_name,
            'goal_score' => $request->goal_score,
            'goal_type' => $request->goal_type,
            'goal_unit' => $request->goal_unit, 
            'category_score_ranges' => json_encode($categoryScoreRanges),
        ]);

        return redirect()->route('admin.kpi.manage', ['position_id' => $goal->position_id])
            ->with('success', 'Goal updated successfully.');
    }
}
