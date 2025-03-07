<x-layout.master>
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-md mx-auto bg-white rounded-lg shadow-lg p-8">
            <div class="text-center mb-8">
                <h3 class="text-2xl font-bold">Verify Your Email Address</h3>
            </div>

            @if (session('resent'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                    A fresh verification link has been sent to your email address.
                </div>
            @endif

            <p class="mb-4">
                Before proceeding, please check your email for a verification link.
                If you did not receive the email,
            </p>

            <form method="POST" action="{{ route('verification.resend') }}">
                @csrf
                <button type="submit" 
                        class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700">
                    Click here to request another
                </button>
            </form>
        </div>
    </div>
</x-layout.master> 