@section('site-title', 'KPI Entry')
<x-layout.master>
    <div class="container-fluid">
        <div class="row">
            <x-sharedata.header></x-sharedata.header>
        </div>
        <!-- Main Content -->
        <div class="p-4 sm:ml-64">
            <div class="p-4 border-2 border-gray-200 rounded-lg dark:border-gray-700 mt-14">
                <h1>Clock In / Clock Out</h1>

    <form action="{{ url('/api/clock-in-out') }}" method="POST">
        @csrf
        <label for="fingerprint_id">Fingerprint ID:</label>
        <input type="text" id="fingerprint_id" name="fingerprint_id" required>
        <button type="submit">Submit</button>
    </form>
    
    <p>Status: <span id="status"></span></p>
            </div>
        </div>
    </div>

    <script>
        const form = document.querySelector('form');
        form.onsubmit = async (e) => {
            e.preventDefault();
            const fingerprintId = document.getElementById('fingerprint_id').value;
            
            const response = await fetch('/api/clock-in-out', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ fingerprint_id: fingerprintId })
            });
            
            const data = await response.json();
            document.getElementById('status').innerText = data.message;
        };
    </script>
</x-layout.master>
