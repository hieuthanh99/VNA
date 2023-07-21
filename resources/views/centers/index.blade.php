<x-app-layout>
    <x-slot name="header">
        <div style="display: flex;
        align-items: center;
        justify-content: space-between;">
            {{-- <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Báo cáo toàn trung tâm (' . $startDate . ' - ' . $endDate . ')') }}
            </h2> --}}
            <div style="display: flex;">
                <form style="margin: 0 20px;" action="{{ route('centers.run') }}" method="POST">
                    @csrf
                    <button  id="run-cronjob-button" class="custom-button">Run Job</button>
                </form>
                @if ($data)
                    <form action="{{ route('pdf') }}" method="GET">
                        @csrf
                        <button id="run-cronjob-button" class="custom-button">PDF</button>
                    </form>
                @endif
            </div>
         
        </div>
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
            padding-bottom: 15px;
        }

        .mb-4 {
            margin-bottom: 1rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .cong-viec-da-lam-row {
            flex-direction: column;
            display: flex;
            /* align-items: center; */
            margin-bottom: 0.5rem;
        }

        .header-report {
            display: flex;
            align-items: center;
        }

        .content-report {
            padding-left: 10px;
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
            /* align-items: center; */
            flex-direction: column;

        }

        .style-note {
            display: flex;
            align-items: center;
            margin: 10px 0;
            padding-left: 30px;
            margin-bottom: 50px;
        }

        .custom-button:hover {
            background-color: #45a049;
        }

        .content-date {
            display: flex;
            margin: 10px 0;
            padding:15px 18px;
        }

        .alert-success {
            background: #c4e8c0;
            padding: 10px;
        }

        .alert-danger {
            background: #f1c9c9;
            padding: 10px;
            position: relative;
            border-radius: 0.5rem;
            margin-top: 28px;
            margin-bottom: -10px;
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

        .report-title {
            position: relative;
            background-image: linear-gradient(195deg,#006885 0%,#006885 100%);
            padding: 16px;
            color: #fff;
            border-radius: 0.5rem;
            margin: 0 21%;
            margin-bottom: -28px;
        }

        .item-job {
            font-weight: 500;
        }
    </style>
    <div class="py-12">
        <h2 class="report-title font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Báo cáo toàn trung tâm (' . $startDate . ' - ' . $endDate . ')') }}
        </h2>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg" style="background: rgb(243 244 246 / var(--tw-bg-opacity)); box-shadow: none;">
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
                @if ($data)

                    @foreach ($data as $item)
                        <div class="p-6 bg-white border-b border-gray-200">
                            @php
                                $rowCount = 1;
                                $rowCount2 = 1;
                            @endphp
                            <div class="mb-4">
                                <h1 class="mb-2 heading-style" style="text-align: center; font-size: 35px;">
                                    {{ $item['DepartmentName'] }}</h1>
                                <h1 class="mb-2 heading-style">I. Công việc đã thực hiện</h1>
                                <div id="cong-viec-da-lam-container">
                                    @if ($item['WorkDone'])
                                        @foreach ($item['WorkDone'] as $key => $value)
                                            @php
                                                $workDone = $value['work_done'];
                                                $valueOfWork = $value['value_of_work'];
                                                $checked = $valueOfWork == '1' ? 'checked' : '';
                                            @endphp
                                            <div class="form-group cong-viec-da-lam-row">
                                                <div class="form-group cong-viec-da-lam-row">
                                                    <div class="header-report form-group" style="padding-top: 15px;">
                                                        <span class="item-job" class="cong-viec-stt">{{ $rowCount++ }}. </span>
                                                        <label style="padding-left: 20px;" class="item-job" for="cong_viec_da_lam">Tiêu đề:</label>
                                                        <input disabled style="flex: 4; margin-left: 44px;" type="text"
                                                            name="cong_viec_da_lam[]" value="{{ $value['work_done'] }}"
                                                            placeholder="Tiêu đề công việc" class="form-control"
                                                            required>
                                                        <div class="form-check" style="margin-top: 0; flex: 2;">
                                                            <input disabled {{ $checked }} disabled type="checkbox"
                                                                name="cong_viec_da_lam_completed[]"
                                                                value="{{ $value['value_of_work'] }}"
                                                                class="form-check-input disabled">
                                                            <input disabled type="hidden" id="hiddenInput"
                                                                name="cong_viec_da_lam_values[]">
                                                            <label class="form-check-label">Đã hoàn thành</label>
                                                        </div>
                                                    </div>
                                                    <div class="content-report form-group">
                                                        <label class="item-job" style="vertical-align: top; padding-left: 20px;" for="noi_dung_cong_viec">Nội dung:</label>
                                                        <textarea disabled required style="width: 67%; height: 200px; margin-left: 28px" name="noi_dung_cong_viec[]"
                                                            placeholder="Nhập nội dung tiêu đề" class="form-control" style="margin-bottom: 10px;">{{ $value['description'] }}</textarea>
                                                        <div class="content-date">
                                                            <div>
                                                                <label class="item-job" for="ngay_sinh">Ngày bắt đầu:</label>
                                                                <input disabled required type="date"
                                                                    name="start_date[]" id="start_date[]"
                                                                    class="form-control"
                                                                    value="{{ $value['start_date'] }}">

                                                            </div>
                                                            <div style="margin-left: 50px;">
                                                                <label class="item-job" for="ngay_sinh">Kết thúc:</label>
                                                                <input disabled required type="date"
                                                                    name="end_date[]" id="end_date[]"
                                                                    class="form-control"
                                                                    value="{{ $value['end_date'] }}">

                                                            </div>
                                                        </div>

                                                    </div>
                                                    <div class="form-group style-note">
                                                        <label class="item-job" for="trangthai_congviec">Tiến độ:</label>
                                                        <input disabled required style="flex:4;  margin-left: 44px;" type="text"
                                                            name="trangthai_congviec[]" placeholder="Tiêu đề công việc"
                                                            class="form-control" required
                                                            value="{{ $value['status_work'] }}">
                                                        <div class="form-check" style="margin-top: 0; flex: 2;">
                                                        </div>
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
                                                <div class="header-report form-group" style="padding-top: 15px;">
                                                    <span class="item-job" class="cong-viec-stt">{{ $rowCount2++ }}. </span>
                                                    <label style="padding-left: 20px;" class="item-job" for="cong_viec_tuan_toi">Tiêu đề:</label>
                                                    <input disabled readonly type="text" name="cong_viec_tuan_toi[]"
                                                        style="flex:4; margin-left: 44px;" value=" {{ $value['next_work'] }}"
                                                        class="form-control custom-input disabled" readonly> <span
                                                        style="flex:2"></span>
                                                </div>
                                                <div class="content-report form-group">
                                                    <label class="item-job" style="vertical-align: top; padding-left: 20px;" for="noi_dung_cong_viec">Nội dung:</label>
                                                    <textarea disabled readonly style="width: 67%; height: 200px; margin-left: 28px" name="noi_dung_cong_viec_tuan_toi[]"
                                                        placeholder="Nhập nội dung" class="form-control" style="margin-bottom: 10px;">{{ $value['next_description'] }}</textarea>
                                                    <div class="content-date">
                                                        <div>
                                                            <label class="item-job" for="ngay_sinh">Ngày bắt đầu:</label>
                                                            <input disabled readonly type="date"
                                                                name="start_date_tuan_toi[]" id="start_date_tuan_toi[]"
                                                                value="{{ $value['next_start_date'] }}"
                                                                class="form-control">
                                                        </div>
                                                        <div style="margin-left: 50px;">
                                                            <label class="item-job" for="ngay_sinh">Kết thúc:</label>
                                                            <input disabled readonly type="date"
                                                                name="end_date_tuan_toi[]"
                                                                value="{{ $value['next_end_date'] }}" id="end_date[]"
                                                                class="form-control">

                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group style-note">
                                                    <label class="item-job" for="trangthai_congviec">Tiến độ:</label>
                                                    <input disabled readonly style="flex:4; margin-left: 44px;" type="text"
                                                        value="{{ $value['next_status_work'] }}"
                                                        name="trangthai_congviec_tuan_toi[]"
                                                        placeholder="Tiêu đề công việc" class="form-control" required>
                                                    <div class="form-check" style="margin-top: 0; flex: 2;">
                                                    </div>
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
                                        <p class="form-control" style="margin-bottom: 10px;">{{ $item['Request'] }}
                                        </p>
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
