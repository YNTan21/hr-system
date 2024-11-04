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

                <form action="{{ route('admin.employee.positions.store') }}" method="post">
                    @csrf
                    <div class="col px-5 pb-2">
                        <h3 class="title text-center">
                            Create Position
                        </h3>
                    </div>
                    <div class="mb-3 px-5 py-2">
                        <label for="positionName" class="form-label fw-bold">Position Name :</label>
                        <input type="text" id="positionName" class="form-control rounded-lg" name="positionName" value="{{ old('positionName') }}" required>
                        @error('positionName')
                            <p class="error text-danger">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                    <div class="mb-3 px-5 py-2 d-flex">
                        <label class="form-label fw-bold me-3 mb-0">Status :</label>
                        <div class="d-flex">
                            <div class="form-check me-3">
                                <input class="form-check-input" type="radio" id="active" name="status" value="active" {{ old('status') == 'active' ? 'checked' : '' }} required>
                                <label class="form-check-label" for="active">
                                    Active
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" id="inactive" name="status" value="inactive">
                                <label class="form-check-label" for="inactive">
                                    Inactive
                                </label>
                            </div>
                        </div>
                        @error('status')
                        <p class="error text-danger">
                            {{ $message }}
                        </p>
                        @enderror
                    </div>
                    <!-- Add submit button -->
                    <div class="col text-center p-2 px-5">
                        <button type="submit" class="btn btn-dark">Add Position</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout.master>
