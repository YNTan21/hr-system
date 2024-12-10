@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Annual Leave Balance</h1>
    <form action="{{ route('admin.annual-leave-balance.update', $leaveBalance->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="form-group">
            <label for="user_id">User</label>
            <select name="user_id" id="user_id" class="form-control" required>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ $leaveBalance->user_id == $user->id ? 'selected' : '' }}>
                        {{ $user->username }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="annual_leave_balance">Annual Leave Balance</label>
            <input type="number" name="annual_leave_balance" id="annual_leave_balance" class="form-control" value="{{ $leaveBalance->annual_leave_balance }}" required min="0">
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>
@endsection