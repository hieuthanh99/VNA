<x-app-layout>
    <x-slot name="header">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
        <div style="display: flex;
        align-items: center;
        justify-content: space-between;">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                @if(!empty($record)) 
                    {{ __('Báo cáo tuần Khối dịch vụ (' . $startDateOfWeekInput . ' đến ' . $endDateOfWeekInput . ')') }}
                @else 
                    {{ __('Báo cáo tuần Khối dịch vụ (' . $startDate . ' đến ' . $endDate . ')') }}
                @endif
            </h2>
            <div style="display: flex;">
                <!-- <form style="margin: 0 20px;" action="{{ route('centers.run') }}" method="POST">
                    @csrf
                    <button id="run-cronjob-button" class="custom-button">Run Job</button>
                </form> -->
                @if (isset($data) && isset($record))
                    <form action="{{ route('pdf') }}" method="GET">
                        @csrf
                        <button id="run-cronjob-button" class="custom-button">In PDF</button>
                    </form>
                    <form style="margin: 0 10px;" action="{{ route('word') }}" method="GET">
                        @csrf
                        <button id="run-cronjob-button" class="custom-button">In Word</button>
                    </form>
                    <form style="margin: 0 10px;" action="{{ route('excel') }}" method="GET">
                        @csrf
                        <button id="run-cronjob-button" class="custom-button">In Excel</button>
                    </form>
                    <form style="margin: 0 10px;" action="{{ route('delete.data') }}" method="GET">
                        @csrf
                        <button id="run-cronjob-button" class="custom-button">Xóa dữ liệu tuần này</button>
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
            /* background-color: #4CAF50; */
            background-image: linear-gradient(195deg,#006885 0%,#006885 100%);
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
            opacity: 0.75;
        }

        .content-date {
            display: flex;
            margin: 10px 0;
            padding:15px 18px;
        }

        .alert-success {
            margin-top: 30px;
            background: #c4e8c0;
            padding: 10px;
        }

        .alert-danger {
            margin-top: 30px;
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
        .center-edit {
            text-align: right;
        }
        .report-center-edit {
            display: inline-block;
            padding: 10px 20px; 
            background-image: linear-gradient(195deg,#006885 0%,#006885 100%); 
            color: #fff; 
            text-decoration: none; 
            border: none; 
            border-radius: 5px; 
            cursor: pointer;
            font-weight: bold; 
            margin-top: 22px;

            margin-right: 15px;
        }

        .report-center-edit:hover {
            opacity: 0.75;
        }
    </style>
    <div class="py-12">
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
                <form action="{{ route('centers.update', ['center' => $record->id]) }}" method="POST" class="mt-4" id="report-form">
                @csrf
                @method('PUT')
                    <div class="p-6 text-gray-900">
                        <div class="container mt-4">
                            <div class="accordion" id="reportsAccordion">
                                @php
                                    $id = 0;
                                    $Week = 1;
                                @endphp
                            @if (isset($dataCenter))
                                    @php
                                        $rowCount = 1;
                                        $rowCount2 = 1;
                                    @endphp

                                    <div class="accordion-item">

                                        @php
                                            $Week = 1;
                                            $createdDate = \Carbon\Carbon::parse($record->created_at);
                                            // dd($createdDate);
                                            $weekNumber = $createdDate->weekOfYear;
                                            $startDateOfWeek = $createdDate->startOfWeek()->format('d-m-Y');
                                            $endDateOfWeek = $createdDate->endOfWeek()->format('d-m-Y');
                                            $dataCenter = json_decode($dataCenter->values);
                                        @endphp
                                        <h2 class="accordion-header" id="heading{{ $record->id }}">
                                            <button class="accordion-button collapsed" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#collapse{{ $record->id }}"
                                                aria-expanded="false" aria-controls="collapse{{ $record->id }}">
                                                @if(!empty($record)) 
                                                    <span style="font-size: 20px;">Báo cáo tuần Khối dịch vụ (Từ
                                                        ngày
                                                        {{ $startDateOfWeekInput }} đến {{ $endDateOfWeekInput }})</span>
                                                @else 
                                                    <span style="font-size: 20px;">Báo cáo tuần Khối dịch vụ (Từ
                                                        ngày
                                                        {{ $startDate }} đến {{ $endDate }})</span>
                                                @endif
                                            </button>
                                        </h2>
                                        <div id="collapse{{ $record->id }}" class="accordion-collapse collapse"
                                            style="visibility: unset" aria-labelledby="heading{{ $record->id }}"
                                            data-bs-parent="#reportsAccordion">
                                            <div class="center-edit">
                                                <button id="get-data-button" class="report-center-edit" type="submit">Cập nhật</button>
                                            </div>
                                            @foreach ($dataCenter as $key => $array)
                                                @php
                                                    $workDone = !empty($array->WorkDone) ? $array->WorkDone : [];
                                                    $expectedWork = !empty($array->ExpectedWork) ? $array->ExpectedWork : [];
                                                    $requestWork = !empty($array->Request) ? $array->Request : [];
                                                    $STT = 1;
                                                    $STT_NEXT = 1;
                                                    $nameDepartment = $array->DepartmentName ?? '';
                                                @endphp
                                                <div class="accordion-body">

                                                    <h2 class="name-department" style="text-align: center">{{ $nameDepartment }}</h2>
                                                    <!-- Hiển thị thông tin về công việc đã làm (Mục I) -->
                                                    {{-- {{dd($item)}} --}}
                                                    <div id="workDoneSection">
                                                        <h3>Mục I: Công việc đã làm</h3>
                                                        @if (!empty($workDone))
                                                            @foreach ($workDone as $keyWorkDone => $work)
                                                                <div id="workDone" style="padding: 10px;">
                                                                    <div style="display: flex;">
                                                                        <span
                                                                            style="font-size:  20px; font-weight: bold; display: flex">{{ $STT++ }}.
                                                                        </span>
                                                                        <input  value="{{ $work->work_done }}" style="font-size:  20px; font-weight: bold; margin-left: 14px; padding-bottom: 8px; padding-top: 0; " type="text" name="cong_viec_da_lam*_*{{$key}}*_*WorkDone*_*{{$keyWorkDone}}" placeholder="Nhập tiêu đề công việc" class="form-control" required>

                                                                    </div>
                                                                    {{-- value_of_work --}}
                                                                    <div style="display: flex; margin: 10px; padding: 10px 0;">
                                                                        @if(!empty($work->start_date))
                                                                            <div
                                                                                style="display: flex; flex: 2; align-items: center">
                                                                                <span
                                                                                    style="width: 35%;  font-size:  18px; font-weight: bold">Ngày
                                                                                    bắt
                                                                                    đầu:</span>
                                                                                <input style="padding-left: 10px;" type="date" name="start_date_tuan_nay*_*{{$key}}*_*WorkDone*_*{{$keyWorkDone}}" id="start_date[]" value="{{ $work->start_date }}" class="form-control">
                                                                            </div>
                                                                        @endif
                                                                        @if(!empty($work->end_date))
                                                                            <div
                                                                                style="display: flex; flex: 2; align-items: center; padding-left: 15px">
                                                                                <span
                                                                                    style="width: 35%; font-size:  18px; font-weight: bold">Ngày
                                                                                    kết
                                                                                    thúc:</span>
                                                                                <input style="padding-left: 10px;" type="date" name="end_date_tuan_nay*_*{{$key}}*_*WorkDone*_*{{$keyWorkDone}}" value="{{ $work->end_date }}" id="end_date[]" class="form-control" >
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                    @if(!empty($work->status_work))
                                                                        <div style="display: flex; margin: 10px; padding: 10px 0;">

                                                                            <span style="width: 10%; font-size:  18px; font-weight: bold">Tiến
                                                                                độ:</span>
                                                                            <input style="padding-left: 10px;" type="text"
                                                                                name="trangthai_cong_viec_tuan_nay*_*{{$key}}*_*WorkDone*_*{{$keyWorkDone}}" placeholder="Nhập tiến độ công việc"
                                                                                class="form-control" value="{{ $work->status_work }}">
                                                                        </div>
                                                                    @endif
                                                                    <div style="display: flex; margin: 10px; padding: 10px 0; ">

                                                                        <span style="font-size:  18px; font-weight: bold">Nội
                                                                            dung:</span>
                                                                        <textarea style="width: 90%; height: 120px; margin-left:30px;" name="noi_dung_cong_viec_tuan_nay*_*{{$key}}*_*WorkDone*_*{{$keyWorkDone}}" 
                                                                        placeholder="Nhập nội dung công việc" class="form-control" style="margin-bottom: 10px;">{{$work->description}}</textarea>
                                                                    </div>

                                                                </div>
                                                            @endforeach
                                                        @else
                                                            <span>Chưa báo cáo</span>
                                                        @endif
                                                    </div>
                                                    <!-- Hiển thị thông tin về công việc tuần tới (Mục II) -->
                                                    <div id="expectedWorkSection">
                                                        <h3>Mục II: Công việc tuần tới</h3>
                                                        @if (!empty($expectedWork))
                                                            @foreach ($expectedWork as $keyExpectedWord => $work)
                                                                <div id="expectedWork" style="padding: 10px;">
                                                                    <div style="display: flex;">
                                                                        <span
                                                                            style="font-size:  20px; font-weight: bold">{{ $STT_NEXT++ }}.
                                                                        </span>
                                                                            <input  value="{{ $work->next_work }}" style="font-size:  20px; font-weight: bold; margin-left: 14px; padding-bottom: 8px; padding-top: 0; " type="text" name="tieu_de_cong_viec_tuan_toi*_*{{$key}}*_*ExpectedWork*_*{{$keyExpectedWord}}" placeholder="Nhập tiêu đề công việc" class="form-control" required>
                                                                    </div>
                                                                    {{-- value_of_work --}}
                                                                    <div style="display: flex; margin: 10px; padding: 10px 0;">
                                                                        @if(!empty($work->next_start_date))
                                                                            <div
                                                                                style="display: flex; flex: 2; align-items: center">
                                                                                <span
                                                                                    style="font-size:  18px; font-weight: bold">Ngày
                                                                                    bắt
                                                                                    đầu:</span>
                                                                                <input style="padding-left: 10px;" type="date" name="start_date_tuan_toi*_*{{$key}}*_*ExpectedWork*_*{{$keyExpectedWord}}" id="start_date_tuan_toi[]" value="{{ $work->next_start_date }}" class="form-control">
                                                                            </div>
                                                                        @endif
                                                                        @if(!empty($work->next_end_date))
                                                                        <div
                                                                            style="display: flex; flex: 2; align-items: center">
                                                                            <span
                                                                                style="font-size:  18px; font-weight: bold">Ngày
                                                                                kết
                                                                                thúc:</span>
                                                                            <input style="padding-left: 10px;" type="date" name="end_date_tuan_toi*_*{{$key}}*_*ExpectedWork*_*{{$keyExpectedWord}}" id="end_date_tuan_toi[]" value="{{ $work->next_end_date }}" class="form-control">

                                                                            <div style="padding-left: 10px;">
                                                                                
                                                                            </div>
                                                                        </div>
                                                                        @endif
                                                                    </div>
                                                                    @if(!empty($work->next_status_work))
                                                                        <div style="display: flex; margin: 10px; padding: 10px 0;">

                                                                            <span style="font-size:  18px; font-weight: bold">Tiến
                                                                                độ:</span>
                                                                            <input style="flex:4; padding-left: 10px;" type="text" value="{{ $work->next_status_work }}"   name="trangthai_congviec_tuan_toi*_*{{$key}}*_*ExpectedWork*_*{{$keyExpectedWord}}" placeholder="Nhập tiến độ công việc" class="form-control">
                                                                        </div>
                                                                    @endif
                                                                    <div style="display: flex; margin: 10px; padding: 10px 0;">

                                                                        <span style="font-size:  18px; font-weight: bold">Nội
                                                                            dung:</span>
                                                                        <textarea style="width: 90%; height: 120px; margin-left:30px;" name="noi_dung_cong_viec_tuan_toi*_*{{$key}}*_*ExpectedWork*_*{{$keyExpectedWord}}" placeholder="Nhập nội dung công việc" class="form-control">{{$work->next_description}}</textarea>

                                                                    </div>

                                                                </div>
                                                            @endforeach
                                                        @else
                                                            <span>Chưa báo cáo</span>
                                                        @endif
                                                    </div>
                                                    <!-- Hiển thị thông tin về kiến nghị (Mục III) -->
                                                    <div id="requestWorkSection">
                                                        <h3>Mục III: Kiến nghị</h3>
                                                        @if (!empty($requestWork))    
                                                            <textarea style="width: 96%; height: 120px; margin-left:30px; margin-top: 20px; margin-bottom: 20px;" id="kien_nghi[]" name="kien_nghi*_*{{$key}}" placeholder="Nhập ý kiến" class="form-control">{{$requestWork}}</textarea>
                                                        @else
                                                            <span>Chưa báo cáo</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <p style="border-bottom: 1px solid #e5e7eb;"></p>
                                            @endforeach
                                        </div>

                                    </div>

                            </div>
                            @else
                            <div class="alert alert-danger">
                                <span>Chưa báo cáo</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</x-app-layout>
