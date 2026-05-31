<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <!-- Profile Picture Section -->
        <div class="flex items-center gap-6 pb-4 border-b border-gray-100">
            <div class="relative group" x-data="{ avatarPreview: '{{ $user->avatar_url }}' }">
                <!-- Avatar image circle -->
                <img 
                    :src="avatarPreview" 
                    alt="{{ $user->name }}" 
                    class="w-24 h-24 rounded-full object-cover border-4 border-indigo-50 shadow-md transition-all duration-300 group-hover:scale-105 group-hover:border-indigo-200"
                />
                
                <!-- Tiny overlay camera button for interactive feel -->
                <label 
                    for="avatar" 
                    class="absolute bottom-0 right-0 bg-indigo-600 hover:bg-indigo-700 text-white p-2 rounded-full shadow-lg cursor-pointer transition-colors duration-200 flex items-center justify-center"
                    title="{{ __('Subir nueva foto') }}"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <input 
                        type="file" 
                        id="avatar" 
                        name="avatar" 
                        class="hidden" 
                        accept="image/*"
                        @change="const file = $event.target.files[0]; if (file) { avatarPreview = URL.createObjectURL(file) }"
                    />
                </label>
            </div>
            
            <div>
                <x-input-label for="avatar" :value="__('Foto de Perfil')" class="text-base font-semibold text-gray-800" />
                <p class="text-xs text-gray-500 mt-1">
                    {{ __('Formatos permitidos: JPG, JPEG, PNG o GIF. Máximo 2MB.') }}
                </p>
                <!-- Show validation error if any -->
                <x-input-error class="mt-1" :messages="$errors->get('avatar')" />
            </div>
        </div>

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" class="inline-flex items-center justify-center px-5 py-2.5 bg-blue-100 hover:bg-blue-600 text-blue-900 hover:text-white font-bold text-xs uppercase tracking-widest rounded-2xl hover:scale-[1.02] active:scale-[0.98] shadow-sm transition-all duration-150">
                {{ __('Save') }}
            </button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
