<x-guest-layout>
    <div class="max-w-md mx-auto bg-white p-8 rounded-lg">
        <!-- Centered Register Title -->
        <h2 class="text-2xl font-semibold text-center text-gray-800 mb-6">{{ __('Create an Account') }}</h2>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Name -->
            <div class="mb-4">
                <x-input-label for="name" :value="__('Full Name')" />
                <x-text-input id="name" class="block mt-1 w-full p-3 border border-gray-300 rounded-lg focus:outline-none" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                <x-input-error :messages="$errors->get('name')" class="mt-2 text-sm text-red-600" />
            </div>

            <!-- Email Address -->
            <div class="mb-4">
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="block mt-1 w-full p-3 border border-gray-300 rounded-lg focus:outline-none" type="email" name="email" :value="old('email')" required autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm text-red-600" />
            </div>

            <!-- Password -->
            <div class="mb-4">
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password" class="block mt-1 w-full p-3 border border-gray-300 rounded-lg focus:outline-none" type="password" name="password" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm text-red-600" />
            </div>

            <!-- Confirm Password -->
            <div class="mb-6">
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                <x-text-input id="password_confirmation" class="block mt-1 w-full p-3 border border-gray-300 rounded-lg focus:outline-none" type="password" name="password_confirmation" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-sm text-red-600" />
            </div>

            <!-- Submit Button -->
            <x-primary-button class="w-full h-12 bg-indigo-600 text-white px-6 rounded-lg flex items-center justify-center hover:bg-indigo-700 focus:outline-none btn-link">
                {{ __('Register') }}
            </x-primary-button>

            <!-- Already Registered Link below the Button -->
            <div class="text-center mt-4">
                <a href="{{ route('login') }}" class="text-sm text-indigo-600 btn-link">
                    {{ __('Already registered?') }}
                </a>
            </div>
        </form>
    </div>

    <style>
        #name:focus, #email:focus, #password:focus, #password_confirmation:focus {
            outline: none;
            border-color: #41ca96; 
            box-shadow: 0 0 0 2px #41ca96;
        }

        input[type="checkbox"]:focus {
            outline: none;
            box-shadow: 0 0 0 2px #41ca96;
        }

        .btn-link:focus,.btn-link:focus {
            outline: none;
            box-shadow: 0 0 0 2px #41ca96; 
        }
       .btn-link {
            color: #41ca96;
        }
    </style>
</x-guest-layout>
