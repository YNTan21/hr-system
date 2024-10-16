@section('site-title', 'View Employee')
<x-layout.master>
    <div class="container-fluid">
        <div class="row">
            <x-sharedata.header></x-sharedata.header>
        </div>
        <!-- Main Content -->
        <div class="p-4 sm:ml-64">
            <div class="p-4 border-2 border-gray-200 rounded-lg dark:border-gray-700 mt-14">
                
                {{-- Success Message --}}
                @if (session('success'))
                <div class="alert alert-success text-center">
                    {{ session('success') }}
                </div>
                @endif

                <div class="col px-5 pb-2">
                    <h3 class="font-bold text-xl text-center">
                        VIEW EMPLOYEE
                    </h3>
                </div>

                <div class="form-group py-3">
                    <label for="username" class="font-bold pb-1">USERNAME</label>
                    <input type="text" id="username" class="form-control rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-gray-100" value="{{ $employee->username }}" readonly>
                </div>

                <div class="form-group pb-3">
                    <label for="email" class="font-bold pb-1">EMAIL</label>
                    <input type="email" id="email" class="form-control rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-gray-100" value="{{ $employee->email }}" readonly>
                </div>

                <div class="form-group pb-3">
                    <label for="position" class="font-bold pb-1">POSITION</label>
                    <input type="text" id="position" class="form-control rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-gray-100" value="{{ $employee->position }}" readonly>
                </div>

                <div class="form-group pb-3">
                    <label for="type" class="font-bold pb-1">TYPE</label>
                    <input type="text" id="type" class="form-control rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-gray-100" value="{{ $employee->type }}" readonly>
                </div>

                <div class="form-group pb-3">
                    <label for="hire_date" class="font-bold pb-1">HIRE DATE</label>
                    <input type="date" id="hire_date" class="form-control rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-gray-100" value="{{ $employee->hire_date }}" readonly>
                </div>

                <div class="form-group pb-3">
                    <label for="status" class="font-bold pb-1">STATUS</label>
                    <input type="text" id="status" class="form-control rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-gray-100" value="{{ $employee->status }}" readonly>
                </div>

                <div class="form-group pb-3">
                    <label for="phone" class="font-bold pb-1">PHONE</label>
                    <input type="text" id="phone" class="form-control rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-gray-100" value="{{ $employee->phone }}" readonly>
                </div>

                <div class="form-group pb-3">
                    <label for="address" class="font-bold pb-1">ADDRESS</label>
                    <textarea id="address" class="form-control block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-gray-100" readonly>{{ $employee->address }}</textarea>
                </div>

                <div class="form-group pb-3">
                    <label for="ic" class="font-bold pb-1">IC</label>
                    <input type="text" id="ic" class="form-control rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-gray-100" value="{{ $employee->ic }}" readonly>
                </div>

                <div class="form-group pb-3">
                    <label for="dob" class="font-bold pb-1">DATE OF BIRTH</label>
                    <input type="date" id="dob" class="form-control rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-gray-100" value="{{ $employee->dob }}" readonly>
                </div>

                <div class="form-group pb-3">
                    <label for="gender" class="font-bold pb-1">GENDER</label>
                    <input type="text" id="gender" class="form-control rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-gray-100" value="{{ $employee->gender }}" readonly>
                </div>

                <div class="form-group pb-3">
                    <label for="marital_status" class="font-bold pb-1">MARITAL STATUS</label>
                    <input type="text" id="marital_status" class="form-control rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-gray-100" value="{{ $employee->marital_status }}" readonly>
                </div>

                <div class="form-group pb-3">
                    <label for="nationality" class="font-bold pb-1">NATIONALITY</label>
                    <input type="text" id="nationality" class="form-control rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-gray-100" value="{{ $employee->nationality }}" readonly>
                </div>

                <div class="form-group pb-3">
                    <label for="bank_account_holder_name" class="font-bold pb-1">BANK ACCOUNT HOLDER NAME</label>
                    <input type="text" id="bank_account_holder_name" class="form-control rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-gray-100" value="{{ $employee->bank_account_holder_name }}" readonly>
                </div>

                <div class="form-group pb-3">
                    <label for="bank_name" class="font-bold pb-1">BANK NAME</label>
                    <input type="text" id="bank_name" class="form-control rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-gray-100" value="{{ $employee->bank_name }}" readonly>
                </div>

                <div class="form-group pb-3">
                    <label for="bank_account_number" class="font-bold pb-1">BANK ACCOUNT NUMBER</label>
                    <input type="text" id="bank_account_number" class="form-control rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-gray-100" value="{{ $employee->bank_account_number }}" readonly>
                </div>

                <div class="col text-center p-2 px-5 pb-3">
                    <a href="{{ route('admin.employee.index') }}" class="btn btn-dark font-bold">BACK TO LIST</a>
                </div>

            </div>
        </div>
    </div>
</x-layout.master>
