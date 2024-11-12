@section('site-title', 'Dashboard')
<x-layout.master>
    <div class="container-fluid">
        <div class="row">
            <x-sharedata.header></x-sharedata.header>
        </div>
        <!-- Main Content -->
        <div class="p-4 sm:ml-64">
            <div class="p-4 border-2 border-gray-200 rounded-lg dark:border-gray-700 mt-14">

                <h2>KPI Management</h2>

                <!-- Rating Category Button -->
                <div class="mb-3">
                    <a href="" class="btn btn-secondary">Rating Category</a>
                </div>

                <!-- Table for positions and actions -->
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Position Name</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($positions as $position)
                            <tr>
                                <td>{{ $position->position_name }}</td>
                                <td>
                                    @if ($position->kpis->isEmpty())
                                        <p>No KPI assigned</p>
                                    @else
                                        <ul>
                                            @foreach ($positions->kpis as $kpi)
                                                <li>
                                                    <a href="" class="btn btn-info">View</a>
                                                    {{-- {{ route('admin.kpi.show', $kpi->id) }} --}}
                                                    <a href="" class="btn btn-warning">Edit</a>
                                                    {{-- {{ route('admin.kpi.edit', $kpi->id) }} --}}

                                                    <!-- Delete KPI form (if needed) -->
                                                    <form action="{{ route('admin.kpi.destroy', $kpi->id) }}" method="POST" style="display:inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">Delete</button>
                                                    </form>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif

                                    <!-- Arrow Button for Position KPI -->
                                    <a href="{{ route('admin.kpi.manage', $position->id) }}" class="btn btn-success">→ Manage KPI</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</x-layout.master>
