@php
    $inputClass = 'w-full px-3 py-2 border border-gray-300 rounded-md text-gray-900 text-sm focus:outline-none focus:ring-blue-600 focus:border-blue-600';
@endphp

<section>
    <header class="mb-5">
        <h2 class="text-base font-semibold text-gray-900">Personal details</h2>
        <p class="mt-1 text-sm text-gray-500">Your name, contact and photo. Changing your email requires verifying it again.</p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-4">
        @csrf
        @method('patch')

        {{-- Photo --}}
        <div x-data="{ preview: @js($user->photo_url), removed: false }" class="flex items-center gap-4">
            <template x-if="preview">
                <img :src="preview" alt="" class="w-16 h-16 rounded-full object-cover ring-2 ring-blue-100">
            </template>
            <template x-if="!preview">
                <span class="w-16 h-16 rounded-full bg-blue-600 text-white flex items-center justify-center text-xl font-bold">{{ $user->initials }}</span>
            </template>
            <div class="min-w-0">
                <input type="hidden" name="remove_photo" :value="removed ? 1 : 0">
                <input
                    x-ref="photo" type="file" name="photo" accept="image/png,image/jpeg,image/webp"
                    @change="const f = $event.target.files[0]; if (f) { preview = URL.createObjectURL(f); removed = false }"
                    class="block w-full text-sm text-gray-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:bg-blue-50 file:text-blue-700 file:font-semibold hover:file:bg-blue-100"
                >
                <button type="button" x-show="preview" x-cloak @click="preview = null; removed = true; $refs.photo.value = ''" class="mt-1 text-sm text-red-600 hover:text-red-700">Remove photo</button>
                <p class="text-xs text-gray-400 mt-1">JPG, PNG or WebP, up to 2 MB.</p>
                @error('photo')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
            <input id="name" name="name" type="text" required autocomplete="name" class="{{ $inputClass }}" value="{{ old('name', $user->name) }}">
            @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input id="email" name="email" type="email" required autocomplete="username" class="{{ $inputClass }}" value="{{ old('email', $user->email) }}">
            @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <p class="text-sm mt-2 text-gray-700">
                    Your email address is unverified.
                    <button form="send-verification" class="underline text-gray-600 hover:text-gray-900">Re-send the verification email.</button>
                </p>
                @if (session('status') === 'verification-link-sent')
                    <p class="mt-2 font-medium text-sm text-green-600">A new verification link has been sent to your email address.</p>
                @endif
            @endif
        </div>

        <div>
            <label for="contact" class="block text-sm font-medium text-gray-700 mb-1">Phone / WhatsApp <span class="text-gray-400 font-normal">(optional)</span></label>
            <input id="contact" name="contact" type="tel" autocomplete="tel" placeholder="+258 84 000 0000" class="{{ $inputClass }}" value="{{ old('contact', $user->contact) }}">
            @error('contact')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <button type="submit" class="bg-blue-600 text-white font-semibold px-6 py-2 rounded-full hover:bg-blue-700 transition text-sm">Save changes</button>
    </form>
</section>
