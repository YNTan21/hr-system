@section('site-title', 'home')
<x-layout.master>
    @auth
        Logged In
    @endauth
    @guest
        Guest
    @endguest
</x-layout.master>