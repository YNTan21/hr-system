<x-layout.master>
    <div class="main-wrapper min-h-screen bg-gray-100 flex items-center">
        <div class="container mx-auto">
            <!-- Account Logo -->
            <!-- <div class="text-center mb-8">
                <a href="/">
                    <img src="{{ asset('assets/img/logo2.png') }}" alt="Logo" class="mx-auto h-16">
                </a>
            </div> -->

            <!-- Register Box -->
            <div class="max-w-md mx-auto bg-white rounded-lg shadow-lg p-8">
                <div class="text-center mb-8">
                    <h3 class="text-2xl font-bold mb-2">Create an Account</h3>
                    <!-- <p class="text-gray-600">Get started with our application</p> -->
                </div>

                <!-- Register Form -->
                <form action="{{ route('auth.register') }}" method="POST">
                    @csrf

                    @if (session('success'))
                        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- Full Name -->
                    <div class="mb-6">
                        <label for="username" class="block text-gray-700 text-sm font-bold mb-2">Full Name</label>
                        <input type="text" 
                               name="username" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 @error('username') border-red-500 @enderror" 
                               value="{{ old('username') }}" 
                               placeholder="Enter your full name">
                        @error('username')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="mb-6">
                        <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email Address</label>
                        <input type="email" 
                               name="email" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 @error('email') border-red-500 @enderror" 
                               value="{{ old('email') }}" 
                               placeholder="Enter your email">
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-6">
                        <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                        <input type="password" 
                               name="password" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 @error('password') border-red-500 @enderror" 
                               placeholder="Enter password">
                        @error('password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div class="mb-6">
                        <label for="password_confirmation" class="block text-gray-700 text-sm font-bold mb-2">Confirm Password</label>
                        <input type="password" 
                               name="password_confirmation" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" 
                               placeholder="Confirm your password">
                    </div>

                    <!-- Submit Button -->
                    <div class="mb-6">
                        <button type="submit" 
                                class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                            Register
                        </button>
                    </div>

                    <!-- Login Link -->
                    <div class="text-center text-gray-600">
                        Already have an account? 
                        <a href="{{ route('auth.login') }}" class="text-blue-600 hover:text-blue-800">
                            Login
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout.master>