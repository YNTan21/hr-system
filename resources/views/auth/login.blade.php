<x-layout.master>
    <div class="container-fluid flex items-center justify-center min-h-screen">
        <div class="container">
            <div class="register">
                <div class="col-xl-4 mx-auto">
                    <div class="col text-center">
                        <h3 class="title">
                            Login
                        </h3>
                    </div>
                    <form action="{{route('auth.login')}}" method="post">
                        @csrf 
                        @error('failed')
                        <div class="col p-2">
                            <p class="error">
                                {{ $message }}
                            </p>
                        </div>
                        @enderror
                        <div class="col p-2">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" value="{{old('email')}}" name="email">
                            @error('email')
                                <p class="error">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                        <div class="col p-2">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" name="password">
                            @error('password')
                                <p class="error">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                        <div class="col text-center p-2">
                            <button class="btn btn-dark">
                                Login
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layout.master>