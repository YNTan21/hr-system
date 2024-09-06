<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Bootstrap demo</title>
        {{-- bootstrap --}}
        <link href="{{asset('bootstrap/css/bootstrap.min.css')}}" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        {{-- custom --}}
        <link rel="stylesheet" href="{{ asset('css/main.css') }}">
        {{-- FontAwesome --}}
        <link rel="stylesheet" href="{{ asset('fontawesome/css/all.min.css') }}">
    </head>
    <body>
        <x-sharedata.header></x-sharedata.header>
        <main>
            {{ $slot }}
        </main>
        {{-- <x-sharedata.footer></x-sharedata.footer> --}}
        {{-- bootstrap --}}
        <script src="{{asset('bootstrap/js/bootstrap.bundle.min.js')}}" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
        {{-- fontawesome --}}
        <script src="{{ asset('fontawesome/js/all.min.js') }}"></script>
    </body>
</html>