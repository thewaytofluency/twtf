<x-guest-layout>
    <h2 class="text-center text-3xl font-extrabold text-gray-900">Create an Account</h2>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mt-4" role="alert">
            <span class="block sm:inline">{{ $errors->first() }}</span>
        </div>
    @endif

    {{-- The source SignupPage.tsx also has a "Plan" <select> (basic/premium) here, dropped for
         now since the User model has no plan column and it was flagged as out of scope. --}}
    <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-6">
        @csrf

        <div class="space-y-4">
            <div>
                <label for="name" class="sr-only">Name</label>
                <input
                    id="name"
                    name="name"
                    type="text"
                    required
                    autofocus
                    class="w-full px-4 py-2 border border-gray-300 rounded-md text-gray-900 focus:outline-none focus:ring-blue-600 focus:border-blue-600"
                    placeholder="Name"
                    value="{{ old('name') }}"
                >
            </div>
            <div>
                <label for="email-address" class="sr-only">Email</label>
                <input
                    id="email-address"
                    name="email"
                    type="email"
                    autocomplete="email"
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-md text-gray-900 focus:outline-none focus:ring-blue-600 focus:border-blue-600"
                    placeholder="Email"
                    value="{{ old('email') }}"
                >
            </div>
            <div>
                <label for="password" class="sr-only">Password</label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    autocomplete="new-password"
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-md text-gray-900 focus:outline-none focus:ring-blue-600 focus:border-blue-600"
                    placeholder="Password"
                >
            </div>
            <div>
                <label for="password_confirmation" class="sr-only">Confirm Password</label>
                <input
                    id="password_confirmation"
                    name="password_confirmation"
                    type="password"
                    autocomplete="new-password"
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-md text-gray-900 focus:outline-none focus:ring-blue-600 focus:border-blue-600"
                    placeholder="Confirm Password"
                >
            </div>
        </div>

        <button
            type="submit"
            class="w-full bg-blue-600 text-white font-bold py-2 rounded-full hover:bg-blue-700 transition disabled:opacity-50"
        >
            Sign Up
        </button>
    </form>

    {{-- Visual-only "Continue with Google" button, see login.blade.php for rationale. --}}
    <div class="mt-6">
        <div class="relative">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-gray-300"></div>
            </div>
            <div class="relative flex justify-center text-sm">
                <span class="px-2 bg-white text-gray-500">Or continue with</span>
            </div>
        </div>

        <button
            type="button"
            disabled
            class="w-full mt-4 flex items-center justify-center py-2 px-4 border border-gray-300 rounded-full shadow-md bg-white text-gray-700 cursor-not-allowed opacity-50"
        >
            <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24">
                <path fill="#EA4335" d="M12.545,10.239v3.821h5.445c-0.712,2.315-2.647,3.972-5.445,3.972c-3.332,0-6.033-2.701-6.033-6.032s2.701-6.032,6.033-6.032c1.498,0,2.866,0.549,3.921,1.453l2.814-2.814C17.503,2.988,15.139,2,12.545,2C7.021,2,2.543,6.477,2.543,12s4.478,10,10.002,10c8.396,0,10.249-7.85,9.426-11.748L12.545,10.239z" />
            </svg>
            Continue with Google
        </button>
    </div>

    <div class="text-center mt-4">
        <span class="text-gray-500">Already have an account? </span>
        <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-700 font-medium">Login</a>
    </div>
</x-guest-layout>
