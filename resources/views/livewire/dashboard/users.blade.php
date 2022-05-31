<div class="inset-0 bg-grid-slate-100 dark:bg-grid-slate-700/25 w-8/12">
    <div class="w-full text-right mb-4">
        <a id="create-user" class="inline-block rounded ml-auto p-2 px-4 bg-green-500 hover:bg-green-700 text-white" href="{{ route('dashboard.users.create') }}">+ Create User</a>
    </div>

    <table class="table-auto w-full bg-white rounded-lg">
        <thead>
            <tr>
                <th class="bg-slate-300 border-b font-bold p-4 pl-6 pb-3 text-slate-800 text-left">
                    Name
                </th>
                <th class="bg-slate-300 border-b font-bold p-4 pb-3 text-slate-800 text-left">
                    Email
                </th>
                <th class="bg-slate-300 border-b font-bold p-4 pb-3 text-slate-800 text-center">
                    Status
                </th>
                <th class="bg-slate-300 border-b font-bold p-4 pr-6 pb-3 text-slate-800 text-center">
                    Created At
                </th>
                <th class="bg-slate-300 border-b font-bold p-4 pr-6 pb-3 text-slate-800 text-center">

                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
            <tr class="hover:bg-slate-100">
                <td class="border-b border-slate-100 p-4 pl-6 text-slate-500">
                    {{ $user->name }}
                </td>
                <td class="border-b border-slate-100 p-4 text-slate-500">
                    {{ $user->email }}
                </td>
                <td class="border-b border-slate-100 p-4 text-slate-500 text-center">
                    <label class="rounded px-2 text-white @if($user->status->value === 'Suspended') bg-red-500 @else bg-green-500 @endif">
                        {{ $user->status->value }}
                    </label>
                </td>
                <td class="border-b border-slate-100 p-4 pr-6 text-slate-500 text-center">
                    {{ $user->created_at->toDayDateTimeString() }}
                </td>
                <td class="border-b border-slate-100 p-4 pr-6 text-slate-500 text-left">
                    @if (request()->user && request()->user->id !== $user->id)
                    <a href="{{ route('dashboard.users.edit', [$user->id]) }}" class="bg-green-500 hover:bg-green-700 text-white px-2 text-xs rounded">edit</a>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="p-4">
        {{ $users->links() }}
    </div>
</div>
