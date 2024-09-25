@section('site-title', 'Dashboard')
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

                <form action="{{ route('admin.employee.update', $employee->id) }}" method="post">
                    @csrf
                    @method('PUT')
                    <div class="col px-5 pb-2">
                        <h3 class="font-bold text-xl text-center">
                            EMPLOYEE INFORMATION
                        </h3>
                    </div>

                    <div class="mb-3 px-5 py-2 flex">
                        <div class="w-1/2 pr-2">
                            <label for="username" class="form-label fw-bold">FULL NAME</label>
                            <input type="text" id="username" class="form-control rounded-lg" name="username" value="{{ old('username', $employee->username ?? '') }}" required>
                            @error('username')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="w-1/2 pl-2">
                            <label for="email" class="form-label fw-bold">EMAIL</label>
                            <input type="email" id="email" class="form-control rounded-lg" name="email" value="{{ old('email', $employee->email ?? '') }}" required>
                            @error('email')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3 px-5 py-2 flex">
                        <div class="w-1/3 pr-2">
                            <label for="ic" class="form-label fw-bold">NRIC/PASSPORT</label>
                            <input type="text" id="ic" class="form-control rounded-lg" name="ic" value="{{ old('ic', $employee->ic) }}" required>
                            @error('ic')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
    
                        <div class="w-1/3 px-2">
                            <label for="phone" class="form-label fw-bold">PHONE</label>
                            <input type="tel" id="phone" class="form-control rounded-lg" name="phone" value="{{ old('phone', $employee->phone) }}" required>
                            @error('phone')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
    
                        <div class="w-1/3 pl-2">
                            <label for="gender" class="form-label fw-bold">GENDER</label>
                            <select id="gender" name="gender" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
                                <option value="">Select Gender</option>
                                <option value="male" {{ old('gender', $employee->gender) == 'male' ? 'selected' : '' }}>MALE</option>
                                <option value="female" {{ old('gender', $employee->gender) == 'female' ? 'selected' : '' }}>FEMALE</option>
                            </select>
                            @error('gender')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3 px-5 py-2">
                            <label for="username" class="form-label fw-bold">ADDRESS</label>
                            <input type="text" id="address" class="form-control rounded-lg" name="address" value="{{ old('address', $employee->address) }}" required>
                            @error('address')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                    </div>

                    
                    <div class="mb-3 px-5 py-2">
                        <label class="form-label fw-bold">Status</label>
                        <div class="mt-2">
                            <div class="form-check inline-block mr-4">
                                <input class="form-check-input" type="radio" id="active" name="status" value="active" {{ old('status', $employee->status ?? '') == 'active' ? 'checked' : '' }} required>
                                <label class="form-check-label" for="active">
                                    Active
                                </label>
                            </div>
                            <div class="form-check inline-block">
                                <input class="form-check-input" type="radio" id="inactive" name="status" value="inactive" {{ old('status', $employee->status ?? '') == 'inactive' ? 'checked' : '' }}>
                                <label class="form-check-label" for="inactive">
                                    Inactive
                                </label>
                            </div>
                        </div>
                        @error('status')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Add submit button -->
                    <div class="col text-center p-2 px-5">
                        <button type="submit" class="btn btn-dark">UPDATE</button>
                    </div>
                </form>
                </div>
            </div>
        </div>
    </div>
</x-layout.master>


