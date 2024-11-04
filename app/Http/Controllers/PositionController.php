<?php

namespace App\Http\Controllers;
use App\Models\Position;

use Illuminate\Http\Request;

class PositionController extends Controller
{
    public function index()
    {
        $positions = Position::all();
        return view('admin.employee.positions.index', ['positions' => $positions]);
    }
    
    public function store(Request $request)
    {
        // \Log::info('Store method called', $request->all());

        $request->validate([
            'positionName' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        $fields = [
            'position_name' => $request->positionName,
            'status' => $request->status,
        ];

        Position::create($fields);

        return redirect()->route('admin.employee.positions.index')->with('success', 'Position created successfully.');
    }

    public function create()
    {
        return view('admin.employee.positions.create');
    }

    public function edit($id)
    {
        $position = Position::findOrFail($id);
        return view('admin.employee.positions.edit', compact('position'));
    }

    public function update(Request $request, $id)
    {
        $request -> validate([
            'positionName' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        $position = Position::findOrFail($id);

        $position->update([
            'position_name' => $request->positionName,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.employee.positions.index')->with('success', 'Position updated successfully.');
    }

    public function destroy($id)
    {
        $position = Position::findOrFail($id);
        $position->delete();

        return redirect()->route('admin.employee.positions.index')->with('success', 'Position deleted successfully.');
    }
}
