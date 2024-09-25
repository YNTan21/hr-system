<x-layout.master>
    <div class="container-fluid flex items-center justify-center min-h-screen">
        <div class="container">
            <div class="register">
                <div class="col-xl-4 mx-auto">
                    <div class="col text-center">
                        <h3 class="title">
                            Create an account
                        </h3>
                    </div>
                    <div class="col">
                        <form action="{{ route('auth.register') }}" method="post">
                            @csrf
                            @if (session('success'))
                                <div class="success">
                                    {{ session('success') }}
                                </div>
                            @endif
                            <div class="col p-2">
                                <label for="username" class="form-label">Full Name</label>
                                <input type="text" class="form-control" value="{{old('username')}}" name="username">
                                @error('username')
                                    <p class="error">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                            <div class="col p-2">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control"
                                value="{{old('email')}}" name="email">
                                @error('email')
                                    <p class="error">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                            <div class="col p-2">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" name="password">
                                @error('email')
                                    <p class="error">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                            <div class="col p-2">
                                <label for="password_confirmation" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" name="password_confirmation">
                            </div>
                            <div class="col text-center p-2">
                                <button class="btn btn-dark">Register</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout.master>