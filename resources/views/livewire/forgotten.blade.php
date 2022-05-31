<div class="w-96 mx-auto sm:px-6 lg:px-8">
    <div class="mt-8 bg-white dark:bg-gray-800 overflow-hidden shadow sm:rounded-lg">
        <div class="grid grid-cols-1">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="text-lg leading-7 font-semibold">
                        Forgot your password?
                    </div>
                </div>

                <div class="mt-5">
                    <div class="mt-2 text-gray-600 dark:text-gray-400 text-sm">
                        <p class="mb-3">
                            Enter your email address and we'll send you a link to reset your password.
                        </p>

                        <form wire:submit.prevent="forgot" autocomplete="off">
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="forgotEmail">
                                    Email
                                </label>
                                <input wire:model.debounce.500ms="email" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="forgotEmail" type="email" placeholder="name@company.com" autocomplete="email">
                                @error('email')<span class="block text-red-500 font-sm p-1 px-2"> {{ $message }} </span>@enderror
                            </div>

                            <div class="flex items-center justify-between">
                                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                                    Send link
                                </button>

                                <a class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800" href="{{ route('login') }}">
                                    Sign in
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
