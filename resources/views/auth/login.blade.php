<x-guest-layout>
    @push('head')
        <link rel="icon" href="{{ asset('iconlogo.ico') }}" type="image/x-icon">
    @endpush
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="max-w-md mx-auto bg-white p-8 rounded-lg">
        <!-- Centered Login Title -->
        <h2 class="text-2xl font-semibold text-center text-gray-800 mb-6">{{ __('Login to your account') }}</h2>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email Address -->
            <div class="mb-4">
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="block mt-1 w-full p-3 border border-gray-300 rounded-lg focus:outline-none" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm text-red-600" />
            </div>

            <!-- Password -->
            <div class="mb-6">
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password" class="block mt-1 w-full p-3 border border-gray-300 rounded-lg focus:outline-none" type="password" name="password" required autocomplete="current-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm text-red-600" />
            </div>

            <!-- Remember Me and Forgot Password -->
            <div class="flex justify-between items-center mb-6">
                <div class="flex items-center">
                    <label for="remember_me" class="inline-flex items-center">
                        <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm" name="remember">
                        <span class="ml-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                    </label>
                </div>

                @if (Route::has('password.request'))
                    <a id="forgot_pass" class="text-sm text-gray-600" href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif
            </div>

            <!-- Login Button -->
            <div class="flex items-center justify-between">
                <x-primary-button id="login" class="w-full h-12 bg-indigo-600 text-black px-6 rounded-lg flex items-center justify-center hover:bg-indigo-700 focus:outline-none">
                    {{ __('Log in') }}
                </x-primary-button>
            </div>
            <div class="text-center mt-4">
                <a
                    href="{{ route('register') }}"
                    class="rounded-md px-3 py-2 text-black btn-link">Register Account
                </a>
            </div>
        </form>
    </div>

    <style>
        #email:focus, #password:focus, #forgot_pass:focus {
            outline: none;
            border-color: #41ca96;
            box-shadow: 0 0 0 2px #41ca96;
        }

        input[type="checkbox"]:focus {
            outline: none;
            box-shadow: 0 0 0 2px #41ca96;
        }

        #login:focus {
            outline: none;
            box-shadow: 0 0 0 2px #41ca96;
        }

        .btn-link:focus {
            outline: none;
            box-shadow: 0 0 0 2px #41ca96;
        }
        .btn-link {
            color: #41ca96;
        }
    </style>
</x-guest-layout>
