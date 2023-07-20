<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Báo cáo toàn trung tâm ('.$startDate.' - '. $endDate.')') }}
        </h2>
    </x-slot>
    <style>
        input[type="text"] {
            width: 100%;
            height: 40px;
            font-size: 16px;
            transition: 0.6s;
            border: none;
            border-bottom: 1px solid #CCC;
            background-color: transparent;

            &:focus {
                outline: none;
                border-bottom: 1px solid #28a2a2;
            }
        }

        /* Các lớp CSS tùy chỉnh */
        .mb-2 {
            margin-bottom: 0.5rem;
        }

        .heading-style {
            margin: 10px 0;
            font-size: 21px;
        }

        .mb-4 {
            margin-bottom: 1rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .cong-viec-da-lam-row {
            display: flex;
            align-items: center;
            margin-bottom: 0.5rem;
        }

        .form-check {
            margin-left: 1rem;
        }

        .btn {
            margin-right: 1rem;
        }

        input[type="text"].custom-input {
            border-bottom: 1px solid #28a2a2;
            outline: none;
        }

        .title-style::before {
            content: counter(section) ". ";
            counter-increment: section;
            font-weight: bold;
            margin-right: 5px;
        }

        #report-form {
            counter-reset: section;
        }

        .custom-input:focus {
            border-bottom: 1px solid #28a2a2;
            box-shadow: none !important;
        }

        input[type="text"]:focus {
            border-bottom: 1px solid #28a2a2;
            box-shadow: none !important;
        }

        .custom-button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }

        .cong-viec-tuan-toi-row {
            display: flex;
            align-items: center;

        }

        .custom-button:hover {
            background-color: #45a049;
        }

        .alert-success {
            background: #c4e8c0;
            padding: 10px;
        }

        .alert-danger {
            background: #f1c9c9;
            padding: 10px;
        }

        .btn-PDF {
            top:50%;
            background-color:#fff;
            color: #0a0a23;
            border-radius:10px; 
            padding:8px;
            min-height:30px; 
            min-width: 120px;
            border: 1px solid #000;
            font-size: 14px;
            float: right;
        }
    </style>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
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
                @if($data)
                @foreach ($data as $item)
                    <div class="p-6 bg-white border-b border-gray-200">
                        @php
                            $rowCount = 1;
                            $rowCount2 = 1;
                        @endphp
                        <div class="mb-4">
                            <h1 class="mb-2 heading-style" style="text-align: center; font-size: 35px;">
                                {{ $item['DepartmentName'] }}</h1>
                                <!-- Trong file view.blade.php -->
                                <a href="{{ route('exportPDF', ['id' => $id]) }}" class="btn btn-primary">Xuất file PDF</a>
                            <h1 class="mb-2 heading-style">I. Công việc đã thực hiện</h1>
                            <div id="cong-viec-da-lam-container">
                                @if($item['WorkDone'])
                                    @foreach ($item['WorkDone'] as $key => $value)
                                        @php
                                            $workDone = $value['work_done'];
                                            $valueOfWork = $value['value_of_work'];
                                            $checked = $valueOfWork == '1' ? 'checked' : '';
                                        @endphp
                                        <div class="form-group cong-viec-da-lam-row">
                                            <span class="cong-viec-stt">{{ $rowCount++ }}</span>
                                            <input disabled style="flex: 4" value="{{ $workDone }}" type="text"
                                                name="cong_viec_da_lam[]" placeholder="Tiêu đề công việc"
                                                class="form-control" required>
                                            <div class="form-check" style="margin-top: 0; flex: 2;">
                                                <input {{ $checked }} disabled type="checkbox"
                                                    name="cong_viec_da_lam_completed[]"
                                                    value="{{ $value['value_of_work'] }}" class="form-check-input">
                                                <input type="hidden" id="hiddenInput" name="cong_viec_da_lam_values[]"
                                                    value="1">
                                                <label class="form-check-label">Đã hoàn thành</label>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <p>Không có dữ liệu</p>
                                @endif
                            </div>
                        </div>
                        <div class="mb-4">
                            <h1 class="mb-2 heading-style">II. Công việc dự kiến</h1>
                            <div id="cong-viec-tuan-toi-container">
                                @if ($item['ExpectedWork'])
                                    @foreach ($item['ExpectedWork'] as $key => $value)
                                    <div class="form-group  cong-viec-tuan-toi-row">
                                        <span class="cong-viec-stt">{{ $rowCount2++ }}</span>
                                        <input disabled value="{{ $value }}" style="flex: 4" type="text"
                                            name="cong_viec_tuan_toi[]" class="form-control" required>
                                        <div style="margin-top: 0; flex: 2;"></div>

                                    </div>
                                    @endforeach
                                @else
                                    <p>Không có dữ liệu</p>
                                @endif
                              
                            </div>
                        </div>
                        <div class="mb-4">
                            <h1 class="mb-2 heading-style">III. Kiến nghị</h1>
                            <div>
                                @if ($item['Request'])
                                    <p class="form-control" style="margin-bottom: 10px;">{{ $item['Request'] }}</p>
                                @else
                                    <p>Không có dữ liệu</p>
                                @endif
                            </div>

                        </div>

                    </div>
                @endforeach
                @else
                <div class="alert alert-danger">
                    <p>Không tồn tại dữ liệu</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
