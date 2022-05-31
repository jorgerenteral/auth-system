<form wire:submit.prevent="update" class="inset-0 bg-grid-slate-100 dark:bg-grid-slate-700/25 w-4/12" autocomplete="off">
    @csrf

    <table class="table-auto w-full bg-white rounded-lg">
        <thead>
            <tr>
                <th colspan="2" class="bg-slate-300 border-b font-bold p-4 pr-6 pb-3 text-slate-800 text-left">
                    <a href="{{ $backRoute ?? route('dashboard.users.index') }}" class="px-2 rounded text-sm bg-blue-500 hover:bg-blue-500 text-white text-center mr-3">
                        go back
                    </a>
                    Edit User
                </th>
            </tr>
        </thead>
        <tbody>
            <tr class="hover:bg-slate-100">
                <th class="bg-slate-300 w-36 border-b font-bold p-4 pr-6 pb-3 text-slate-800 text-left">
                    Name
                </th>
                <td class="border-b border-slate-100 p-4 text-slate-500">
                    {{ $user->name }}
                </td>
            </tr>

            <tr class="hover:bg-slate-100">
                <th class="bg-slate-300 w-36 border-b font-bold p-4 pr-6 pb-3 text-slate-800 text-left">
                    Email
                </th>
                <td class="border-b border-slate-100 p-4 text-slate-500">
                    <input wire:model.debounce.500ms="email" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="userEmail" type="email" placeholder="name@company.com" autocomplete="email">
                    @error('email')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                </td>
            </tr>

            @if ($currentUser)
            <tr class="hover:bg-slate-100">
                <th class="bg-slate-300 w-36 border-b font-bold p-4 pr-6 pb-3 text-slate-800 text-left">
                    Status
                </th>
                <td class="border-b border-slate-100 p-4 text-slate-500">
                    <select @if ($user->id === $currentUser->id) disabled @endif wire:model.debounce.500ms="status" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="userStatus">
                        @foreach ($statuses as $userStatus)
                        <option value="{{ $userStatus }}">{{ $userStatus }}</option>
                        @endforeach
                    </select>
                </td>
            </tr>
            @endif

            <tr>
                <td colspan="2" class="bg-slate-300 border-b border-slate-100 px-4 py-2 text-slate-500 text-right">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Save</button>
                </td>
            </tr>
        </tbody>
    </table>
</form>
