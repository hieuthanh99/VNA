<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Thông tin email') }}
        </h2>
    </x-slot>
    <div class="py-12 mt-6 space-y-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="">
                    <div class="new-row">
                        <a href="#" id="add-new-row">Thêm mới</a>
                    </div>
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                    @endif
                    <table class="w-full bg-white" style="text-align: center">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="py-2 px-4 font-semibold text-gray-600">Phòng ban</th>
                                <th class="py-2 px-4 font-semibold text-gray-600">Email</th>
                                <th class="py-2 px-4 font-semibold text-gray-600">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($emails as $email)
                                <tr class="user-row">
                                    <td class="py-2 px-4 border-b">{{ $email->department_name }}</td>
                                    <td class="py-2 px-4 border-b">{{ $email->email }}</td>
                                    <td class="py-2 px-4 border-b">
                                        <form action="{{ route('emails.destroy', ['email' => $email->id]) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit">Xóa</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <style>
        .new-row {
            text-align: right;
        }
        #add-new-row {
            display: inline-block;
            padding: 10px 20px; 
            background-image: linear-gradient(195deg,#006885 0%,#006885 100%); 
            color: #fff; 
            text-decoration: none; 
            border: none; 
            border-radius: 5px; 
            cursor: pointer;
            font-weight: bold; 
            margin-bottom: 15px;
        }

        #add-new-row:hover {
            opacity: 0.75;
        }
        .user-row:hover {
            background-color: #f2f2f2;
        }
        .user-row button:hover {
            background-color: #A9A9A9;
            color: #fff;
            border-color: #A9A9A9; 
            border-radius: 5px;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
        const addNewRowLink = document.getElementById('add-new-row');
        const table = document.querySelector('table tbody');

        let rowCount = 1;

        addNewRowLink.addEventListener('click', function (e) {
            e.preventDefault();

            // Create a new row element
            const newRow = document.createElement('tr');

            // Define the structure of the new row
            newRow.innerHTML = `
                <form id="emailForm" action="{{ route('emails.store') }}" method="POST">
                    @csrf
                    <td class="py-2 px-4 border-b">
                        <input type="text" name="department_name[]" placeholder="Phòng ban">
                    </td>
                    <td class="py-2 px-4 border-b">
                        <input type="text" name="email[]" placeholder="Email">
                    </td>
                    <td class="py-2 px-4 border-b">
                        <button class="submit action" type="submit">Lưu</button>
                        <a href="#" class="remove-row action">Xóa</a>
                    </td>
                </form>
                <style>
                .action:hover {
                    background-color: #A9A9A9;
                    color: #fff;
                    border-color: #A9A9A9; 
                    border-radius: 5px;
                }
                </style>
            `;

            const rowNumberCell = document.createElement('td');
            newRow.appendChild(rowNumberCell);

            newRow.querySelector('.remove-row').addEventListener('click', function (e) {
                e.preventDefault();
                newRow.remove();
            });

            const saveButton = newRow.querySelector('button[type="submit"]');
            saveButton.addEventListener('click', function (e) {
                e.preventDefault();

                const departmentInput = newRow.querySelector('input[name="department_name[]"]');
                const emailInput = newRow.querySelector('input[name="email[]"]');
                const departmentValue = departmentInput.value;
                const emailValue = emailInput.value;

                console.log('Phòng ban:', departmentValue);
                console.log('Email:', emailValue);
                let contentArray = [];
                let rowData = {};
                rowData.departmentValue = departmentValue; 
                rowData.emailValue = emailValue; 
                contentArray.push(rowData);
                const jsonArray = JSON.stringify(contentArray);
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                fetch('/emails', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': csrfToken
                    },
                    body: JSON.stringify({ jsonArray }),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.message === 'Dữ liệu đã được lưu thành công!') {
                        window.location.reload();
                    } else if (data === 500) {
                        alert('Lỗi khi thêm email!');
                        window.location.reload();
                    }
                })
                .catch(error => {
                    console.error('Lỗi:', error);
                });
            });

            table.appendChild(newRow);
            rowCount++;
        });
    });
    </script>
</x-app-layout>