@section('site-title', 'Dashboard')
<x-layout.master>
    <div class="container-fluid">
        <div class="row px-3">
            <div class="col-xl-2 col-lg-3">
                <div class="d-none d-xl-block d-lg-block">
                    <x-dashboard.sidebar></x-dashboard.sidebar>
                </div>
            </div>
            <!-- Main Content -->
            <div class="col-xl-10 col-lg-9">
                <div class="leaveType-create p-4 border shadow-sm" style="background-color=#fff">
                    <form action="" method="post">
                        @csrf
                        <div class="col px-5 pb-2">
                            <h3 class="title text-center">
                                Create Leave Type
                            </h3>
                        </div>
                        <div class="mb-3 px-5 py-2">
                            <label for="leaveType" class="form-label fw-bold">Leave Type :</label>
                            <input type="text" id="leaveType" class="form-control" name="leaveType" required>
                        </div>
                        <div class="mb-3 px-5">
                            <label for="leaveCode" class="form-label fw-bold">Code :</label>
                            <input type="text" id="leaveCode" class="form-control" name="leaveCode" required>
                        </div>
                        <div class="mb-3 px-5 py-2 d-flex">
                            <label class="form-label fw-bold me-3 mb-0">Status :</label>
                            <div class="d-flex">
                                <div class="form-check me-3">
                                    <input class="form-check-input" type="radio" id="active" name="status" value="active" required>
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
                        </div>
                        <!-- Checkbox for Deduct Non Working Day -->
                        <div class="mb-3 px-5">
                            <div class="form-check py-2">
                                <input class="form-check-input" type="checkbox" id="deductNonWorkingDay" name="deductNonWorkingDay">
                                <label class="form-check-label" for="deductNonWorkingDay">
                                    Deduct Non Working Day
                                </label>
                            </div>
                        </div>
                        <!-- Add submit button -->
                        <div class="col text-center p-2 px-5">
                            <button type="submit" class="btn btn-dark">Add Leave Type</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layout.master>

{{-- <x-layout.master>
    <div class="container-fluid">
        <div class="row px-3">
            <!-- Sidebar -->
            <div class="col-xl-2 col-lg-3 d-none d-lg-block">
                <x-dashboard.sidebar></x-dashboard.sidebar>
            </div>
            
        </div>
    </div>
</x-layout.master> --}}
