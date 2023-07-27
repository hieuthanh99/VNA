<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Thông tin các phòng của Ban Dịch vụ hành khách') }}
        </h2>
    </x-slot>
    <style>
        /* Định dạng cho h1 */
        h1 {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        /* Định dạng cho bảng */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        /* Định dạng cho tiêu đề của bảng */
        thead th {
            background-color: #f2f2f2;
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ccc;
        }

        /* Định dạng cho các dòng trong bảng */
        tbody td {
            padding: 10px;
            border-bottom: 1px solid #ccc;
        }

        .list-units {
            margin-left: 17%;
            margin-right: 17%;
            padding-top: 25px;
        }
    </style>
    <div class="list-units">
        <table>
            <thead>
                <tr>
                    <th>Danh sách phòng</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($departments as $department)
                    <tr>
                        <td>{{ $department->name }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>