<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tổ Chức') }}
        </h2>
    </x-slot>

    <div class="py-12 mt-6 space-y-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="">
                    <table class="w-full bg-white" style="text-align: center">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="py-2 px-4 font-semibold text-gray-600">ID</th>
                                <th class="py-2 px-4 font-semibold text-gray-600">Họ và Tên</th>
                                <th class="py-2 px-4 font-semibold text-gray-600">Email</th>
                                <th class="py-2 px-4 font-semibold text-gray-600">Phòng</th>
                                <th class="py-2 px-4 font-semibold text-gray-600">Chức vụ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td class="py-2 px-4 border-b">{{ $user->id }}</td>
                                    <td class="py-2 px-4 border-b">{{ $user->name }}</td>
                                    <td class="py-2 px-4 border-b">{{ $user->email }}</td>
                                    <td class="py-2 px-4 border-b">
                                        @foreach ($departments as $item)
                                            @if ($item->id == $user->department)
                                                {{ $item->name }}
                                            @else
                                            @endif
                                        @endforeach
                                    </td>
                                    <td class="py-2 px-4 border-b">
                                        @if ($user->role == 'admin')
                                            Admin
                                        @else
                                            Nhân viên
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>
            </div>
        </div>

    </div>
</x-app-layout>
