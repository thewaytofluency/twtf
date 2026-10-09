<section class="space-y-4">
    <header>
        <h2 class="text-base font-semibold text-gray-900">Delete account</h2>
        <p class="mt-1 text-sm text-gray-500">
            Once your account is deleted you will lose access to your lessons and progress.
            Download anything you want to keep first.
        </p>
    </header>

    <button
        type="button"
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="inline-flex items-center gap-2 px-5 py-2 rounded-full border border-red-300 text-red-600 text-sm font-semibold hover:bg-red-50 transition"
    >
        <x-lucide-trash-2 class="w-4 h-4" /> Delete account
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-semibold text-gray-900">Delete your account?</h2>
            <p class="mt-1 text-sm text-gray-600">Enter your password to confirm. This cannot be undone.</p>

            <div class="mt-6">
                <label for="password" class="sr-only">Password</label>
                <input id="password" name="password" type="password" placeholder="Password" class="w-3/4 px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-blue-600 focus:border-blue-600">
                @foreach ($errors->userDeletion->get('password') as $message)
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @endforeach
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close')" class="px-5 py-2 rounded-full border border-gray-300 text-gray-700 text-sm font-semibold hover:bg-gray-50">Cancel</button>
                <button type="submit" class="px-5 py-2 rounded-full bg-red-600 text-white text-sm font-semibold hover:bg-red-700">Delete account</button>
            </div>
        </form>
    </x-modal>
</section>
