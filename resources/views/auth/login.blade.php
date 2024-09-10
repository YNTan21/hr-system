<x-layout.master>
    <div class="container-fluid flex items-center justify-center min-h-screen">
        <div class="container">
            <div class="register">
                <div class="col-xl-4 mx-auto">
                    <div class="col text-center">
                        <h3 class="mb-4 text-2xl font-bold leading-none tracking-tight text-gray-900 md:text-5xl lg:text-6xl dark:text-white">
                            Login
                        </h3>
                    </div>
                    <div class="relative z-0 w-full mb-5 group">
                        <input type="email" name="floating_email" id="floating_email" class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" " required />
                        <label for="floating_email" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Email address</label>
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
                            <input type="email" class="form-control rounded-lg" value="{{old('email')}}" name="email">
                            @error('email')
                                <p class="error">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                        <div class="col p-2">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control rounded-lg" name="password">
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