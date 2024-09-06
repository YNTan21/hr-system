@section('site-title', 'home')
<x-layout.master>
    @auth
        Logged In hehehe
    @endauth
    @guest
        Guest
    @endguest
</x-layout.master>