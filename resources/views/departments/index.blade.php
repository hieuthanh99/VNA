<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Phòng Ban') }}
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
                                <th class="py-2 px-4 font-semibold text-gray-600">Tên phòng ban</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($departments as $department)
                                <tr>
                                    <td class="py-2 px-4 border-b">{{ $department->id }}</td>
                                    <td class="py-2 px-4 border-b">
                                        @if($department->name == 'Ban Dịch vụ hành khách')
                                            <a href="{{ route('show.units') }}">{{ $department->name }}</a>
                                        @else 
                                            <a>{{ $department->name }}</a>
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
    <script>
        
    </script>
</x-app-layout>
