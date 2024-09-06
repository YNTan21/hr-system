@section('site-title', 'Dashboard')
<x-layout.master>
    <div class="container-fluid">
        <div class="row px-3">
            <div class="col-xl-2 col-lg-3 border">
                <div class="d-none d-xl-block d-lg-block">
                    <x-dashboard.sidebar></x-dashboard.sidebar>
                </div>
            </div>
            <div class="col-xl-10 col-lg-9 border">
                Content
            </div>
        </div>
    </div>
</x-layout.master>