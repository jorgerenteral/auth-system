<div class="w-96 mx-auto sm:px-6 lg:px-8">
    <div class="mt-8 bg-white dark:bg-gray-800 overflow-hidden shadow sm:rounded-lg">
        <div class="grid grid-cols-1">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="text-lg leading-7 font-semibold">
                        Renew your password
                    </div>
                </div>

                <div class="mt-5">
                    <div class="mt-2 text-gray-600 dark:text-gray-400 text-sm">
                        <p class="mb-3">
                            Hi {{ explode(' ', $user->name)[0] }}! Enter your new password, confirm it and press the Renew button.
                        </p>

                        <form wire:submit.prevent="renewPassword" autocomplete="off">
                            <div class="mb-6">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="renewPasswordPassword">
                                    Password
                                </label>
                                <input wire:model.debounce.500ms="password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="renewPasswordPassword" type="password" placeholder="********" autocomplete="new-password">
                                @error('password')<span class="block text-red-500 font-sm p-1 px-2"> {{ $message }} </span>@enderror
                            </div>

                            <div class="mb-6">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="renewPasswordPasswordconfirmation">
                                    Confirm Password
                                </label>
                                <input wire:model.debounce.500ms="password_confirmation" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="renewPasswordPasswordconfirmation" type="password" placeholder="********" autocomplete="new-password">
                                @error('password_confirmation')<span class="block text-red-500 font-sm p-1 px-2"> {{ $message }} </span>@enderror
                            </div>

                            <div class="flex items-center justify-between">
                                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                                    Renew
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
