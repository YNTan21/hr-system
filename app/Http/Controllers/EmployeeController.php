<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\LeaveType;
use App\Models\Leave;
use App\Models\Employee;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class EmployeeController extends Controller
{
    public function edit($id)
    {
        $employee = User::findOrFail($id);

        if (!auth()->user()->is_admin) {
            return redirect()->route('user.dashboard')->with('error', 'You do not have permission to edit employee profiles.');
        }

        return view('admin.employee.edit', compact('employee'));
    }

    public function update(Request $request, $id)
    {
        // Fetch user instead of employee
        $employee = User::findOrFail($id); 

        // Check if the authenticated user is the owner of the employee profile or an admin
        if ($employee->id !== auth()->id() && !auth()->user()->is_admin) {
            return redirect()->route('user.dashboard')->with('error', 'You do not have permission to update this employee.');
        }

        $validatedData = $request->validate([
            'phone' => 'required|string|max:255',
            'address' => 'required|string',
            'ic' => 'required|string|unique:users,ic,'.$employee->id,
            'dob' => 'required|date',
            'gender' => 'required|in:male,female,other',
            'marital_status' => 'required|in:single,married,divorced,widowed',
            'nationality' => 'required|string|max:255',
            'bank_account_holder_name' => 'required|string|max:255',
            'bank_name' => 'required|string|max:255',
            'bank_account_number' => 'required|string|max:255',
            'hire_date' => 'required|date',
            'position' => 'required|string|max:255',
            'type' => 'required|string|in:full-time,part-time',
            'status' => 'required|string|in:active,inactive',
        ]);

        // Update the user record
        $employee->update($validatedData);

        return redirect()->route('admin.employee.edit', $employee->id)->with('success', 'Employee profile updated successfully');
    }

    public function updateLeaveBalance(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'leave_type_id' => 'required|exists:leave_types,id',
            'days_available' => 'required|integer|min:0',
        ]);

        LeaveBalance::updateOrCreate(
            [
                'user_id' => $validatedData['user_id'],
                'leave_type_id' => $validatedData['leave_type_id'],
            ],
            ['days_available' => $validatedData['days_available']]
        );

        return redirect()->route('admin.employee.edit', $validatedData['user_id'])->with('success', 'Leave balance updated successfully');
    }

    public function index(Request $request)
    {
        $query = User::query();

        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->where('username', 'LIKE', "%{$searchTerm}%");
        }

        $employees = $query->paginate(10);

        return view('admin.employee.index', compact('employees'));
    }

    public function create()
    {
        if (!auth()->user()->is_admin) {
            return redirect()->route('user.dashboard')->with('error', 'You do not have permission to create employee profiles.');
        }

        $employees = Employee::all();
        return view('admin.employee.create', compact('employees'));
    }

    public function store(Request $request)
    {
        Log::info('Received employee creation request', $request->all());

        try {
            $validatedData = $request->validate([
                'username' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
                'position' => 'required|string|max:255',
                'type' => 'required|in:full-time,part-time',
                'hire_date' => 'required|date',
                'status' => 'required|in:active,inactive',
                // Optional fields
                'ic' => 'nullable|string|max:255',
                'dob' => 'nullable|date',
                'gender' => 'nullable|in:male,female,other',
                'phone' => 'nullable|string|max:255',
                'marital_status' => 'nullable|in:single,married,divorced,widowed',
                'nationality' => 'nullable|string|max:255',
                'address' => 'nullable|string',
                'bank_name' => 'nullable|string|max:255',
                'bank_account_holder_name' => 'nullable|string|max:255',
                'bank_account_number' => 'nullable|string|max:255',
                'profile_picture' => 'nullable|image|max:2048',
            ]);

            Log::info('Validation passed', $validatedData);

            $userData = [
                'username' => $validatedData['username'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
                'position' => $validatedData['position'],
                'type' => $validatedData['type'],
                'hire_date' => $validatedData['hire_date'],
                'status' => $validatedData['status'],
            ];

            // Add optional fields if they are present in the request
            $optionalFields = ['ic', 'dob', 'gender', 'phone', 'marital_status', 'nationality', 'address', 'bank_name', 'bank_account_holder_name', 'bank_account_number'];
            foreach ($optionalFields as $field) {
                if (isset($validatedData[$field])) {
                    $userData[$field] = $validatedData[$field];
                }
            }

            if ($request->hasFile('profile_picture')) {
                $path = $request->file('profile_picture')->store('profile_pictures', 'public');
                $userData['profile_picture'] = $path;
            }

            $user = User::create($userData);

            Log::info('User saved successfully', ['user_id' => $user->id]);

            return redirect()->route('admin.employee.index')->with('success', 'Employee created successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed', ['errors' => $e->errors()]);
            return redirect()->route('admin.employee.create')
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Error creating employee', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('admin.employee.create')
                ->with('error', 'An error occurred while creating the employee: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show($id)
    {
        $employee = User::findOrFail($id);

        // Check if the authenticated user is an admin or the employee themselves
        if (!auth()->user()->is_admin && auth()->id() !== $employee->id) {
            return redirect()->route('user.dashboard')->with('error', 'You do not have permission to view this employee profile.');
        }

        return view('admin.employee.show', compact('employee'));
    }

    public function destroy($id)
    {
        // Check if the authenticated user is an admin
        if (!auth()->user()->is_admin) {
            return redirect()->route('user.dashboard')->with('error', 'You do not have permission to delete employee profiles.');
        }

        $employee = User::findOrFail($id);
        
        // Delete associated records (if any)
        // For example, if you have leave balances:
        // $employee->leaveBalances()->delete();

        // Delete the employee
        $employee->delete();

        return redirect()->route('admin.employee.index')->with('success', 'Employee profile deleted successfully');
    }

    public function sCreate()
    {
        if (!auth()->user()->is_admin) {
            return redirect()->route('user.dashboard')->with('error', 'You do not have permission to create employee profiles.');
        }

        $employees = Employee::all();
        return view('admin.employee.sCreate', compact('employees'));
    }

    public function editPassword(Employee $employee)
    {
        return view('admin.employee.edit-password', compact('employee'));
    }

    public function updatePassword(Request $request, Employee $employee)
    {
        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $employee->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.employee.edit', $employee->id)
                        ->with('success', 'Password updated successfully');
}

}
