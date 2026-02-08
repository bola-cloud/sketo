<x-guest-layout>
    <div class="pt-4 bg-gray-100 min-h-screen">
        <div class="flex flex-col items-center pt-6 sm:pt-0">
            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg text-center">
                <h2 class="text-2xl font-bold text-orange-600 mb-4">{{ __('Account Suspended') }}</h2>
                <p class="mb-6 text-gray-600">
                    {{ __('Your business account has been suspended. Please contact support for more information.') }}
                </p>

                <div class="flex justify-center">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="underline text-sm text-gray-600 hover:text-gray-900">
                            {{ __('Log Out') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>