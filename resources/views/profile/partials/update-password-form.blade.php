@php
    $inputClass = 'w-full px-3 py-2 border border-gray-300 rounded-md text-gray-900 text-sm focus:outline-none focus:ring-blue-600 focus:border-blue-600';
@endphp

<section>
    <header class="mb-5">
        <h2 class="text-base font-semibold text-gray-900">Password</h2>
        <p class="mt-1 text-sm text-gray-500">Use a long, random password to keep your account secure.</p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="space-y-4">
        @csrf
        @method('put')

        @foreach ([
            ['update_password_current_password', 'current_password', 'Current password', 'current-password'],
            ['update_password_password', 'password', 'New password', 'new-password'],
            ['update_password_password_confirmation', 'password_confirmation', 'Confirm new password', 'new-password'],
        ] as [$id, $name, $label, $autocomplete])
            <div>
                <label for="{{ $id }}" class="block text-sm font-medium text-gray-700 mb-1">{{ $label }}</label>
                <input id="{{ $id }}" name="{{ $name }}" type="password" autocomplete="{{ $autocomplete }}" class="{{ $inputClass }}">
                @foreach ($errors->updatePassword->get($name) as $message)
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @endforeach
            </div>
        @endforeach

        <button type="submit" class="bg-gray-900 text-white font-semibold px-6 py-2 rounded-full hover:bg-gray-800 transition text-sm">Update password</button>
    </form>
</section>
