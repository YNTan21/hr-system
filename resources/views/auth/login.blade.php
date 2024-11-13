<x-layout.master>
    <div class="main-wrapper min-h-screen bg-gray-100 flex items-center">
        <div class="container mx-auto">
            <!-- Account Logo -->
            <!-- <div class="text-center mb-8">
                <a href="/">
                    <img src="{{ asset('assets/img/logo2.png') }}" alt="Logo" class="mx-auto h-16">
                </a>
            </div> -->

            <!-- Login Box -->
            <div class="max-w-md mx-auto bg-white rounded-lg shadow-lg p-8">
                <div class="text-center mb-8">
                    <h3 class="text-2xl font-bold mb-2">Login</h3>
                    <!-- <p class="text-gray-600">Access to our dashboard</p> -->
                </div>

                <!-- Login Form -->
                <form action="{{ route('auth.login') }}" method="POST">
                    @csrf

                    @error('failed')
                        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                            {{ $message }}
                        </div>
                    @enderror

                    <!-- Email -->
                    <div class="mb-6">
                        <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email Address</label>
                        <input type="email" 
                               name="email" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 @error('email') border-red-500 @enderror" 
                               value="{{ old('email') }}" 
                               placeholder="Enter email">
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-6">
                        <div class="flex justify-between mb-2">
                            <label for="password" class="block text-gray-700 text-sm font-bold">Password</label>
                            {{-- Remove or update password reset link
                            <a href="{{ route('password.request') }}" class="text-sm text-blue-600 hover:text-blue-800">
                                Forgot password?
                            </a> 
                            --}}
                        </div>
                        <input type="password" 
                               name="password" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 @error('password') border-red-500 @enderror" 
                               placeholder="Enter Password">
                        @error('password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="mb-6">
                        <button type="submit" 
                                class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                            Login
                        </button>
                    </div>

                    <!-- Register Link -->
                    <div class="text-center text-gray-600">
                        Don't have an account yet? 
                        <a href="{{ route('auth.register') }}" class="text-blue-600 hover:text-blue-800">
                            Register
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout.master>