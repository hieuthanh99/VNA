<x-app-layout>
    <x-slot name="header">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Include Bootstrap JS and dependencies -->
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Danh sách công việc') }}
        </h2>
        <style>
        </style>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="container mt-4">
                        @if(auth()->user()->role === 'staff')
                        <div style="margin: 20px 0">
                            <form action="{{ route('centers.search') }}" method="POST">
                                @csrf
                                <label for="start-date">Ngày bắt đầu:</label>
                                <input required type="date" id="start-date" name="start_date"  required>

                                <label for="end-date">Ngày kết thúc:</label>
                                <input required type="date" id="end-date" name="end_date"  required>

                                <button type="submit" class="btn btn-primary">Tìm Kiếm</button>
                            </form>
                        </div>
                        @endif
                        @if(auth()->user()->role === 'admin')
                            <div style="margin: 20px 0">
                                <form action="{{ route('search.report') }}" method="POST">
                                    @csrf
                                    <label for="start_date">Ngày bắt đầu:</label>
                                    @if(!empty($startDate))
                                        <input type="date" name="start_date" id="start_date_admin" value="{{ $startDate }}" @if(old('startDate', $startDate) == $startDate) selected="selected" @endif>
                                    @else
                                        <input type="date" name="start_date" id="start_date_admin">
                                    @endif

                                    <label for="end_date">Ngày kết thúc:</label>
                                    @if(!empty($endDate))
                                        <input type="date" name="end_date" id="end_date_admin" value="{{ $endDate }}" @if(old('endDate', $endDate) == $endDate) selected="selected" @endif>
                                    @else
                                        <input type="date" name="end_date" id="start_date_admin">
                                    @endif

                                    <label for="department">Đơn vị:</label>

                                    <input id="departmentInput" type="hidden" name="departmentInput" value="">
                                    <select id="departmentSelect" onchange="updateHiddenInput()">
                                        @if(!empty($departments) || !empty($departmentList))
                                            @if(empty($departmentId))
                                                <option value="">--Đơn vị--</option>
                                                @if(!empty($departmentList)) 
                                                    @foreach ($departmentList as $key => $list)
                                                        <option value="{{ $list->id }}">{{ $list->name }}</option>
                                                    @endforeach
                                                @elseif (!empty($departments)) 
                                                    @foreach ($departments as $key => $list)
                                                        <option value="{{ $list->id }}">{{ $list->name }}</option>
                                                    @endforeach
                                                @endif
                                            @else 
                                                @if(!empty($departmentList)) 
                                                    @foreach ($departmentList as $key => $list)
                                                        <option value="{{ $list->id }}" @if(old('departmentId', $departmentId) == $list->id) selected="selected" @endif>{{ $list->name }}</option>
                                                    @endforeach
                                                @elseif (!empty($departments)) 
                                                    @foreach ($departments as $key => $list)
                                                        <option value="{{ $list->id }}">{{ $list->name }}</option>
                                                    @endforeach
                                                @endif
                                            @endif
                                        @else 
                                                @dd('!empty($departments) || !empty($departmentList)');

                                        @endif
                                    </select>
                                    <button type="submit" class="btn btn-primary">Tìm Kiếm</button>
                                </form>
                            </div>
                        @endif
                        <div class="accordion" id="reportsAccordion">
                            @php
                                $id = 0;
                                $Week = 1;
                            @endphp
                            @if(auth()->user()->role === 'admin')
                            @if(empty($reports))
                                @if(!empty($reportCenter) && empty($departmentId))
                                    <div id="search-results-date">
                                        @foreach($reportCenter as $i)
                                            @php

                                                $createdDate = \Carbon\Carbon::parse($i->created_at);

                                                // dd($createdDate);
                                                $weekNumber = $createdDate->weekOfYear;

                                                $startDateOfWeek = $createdDate->startOfWeek()->format('d-m-Y');
                                                $endDateOfWeek = $createdDate->endOfWeek()->format('d-m-Y');
                                            @endphp
                                        @endforeach

                                        @if(!empty($reportCenter))
                                            @foreach ($reportCenter as $a)
                                                <div style="border: 1px solid rgb(243 244 246 / var(--tw-bg-opacity));">
                                                    <h2 class="accordion-header" id="heading{{ $a->id }}">
                                                            <button class="accordion-button collapsed" type="button"
                                                                data-bs-toggle="collapse" data-bs-target="#collapse{{ $a->id }}"
                                                                aria-expanded="false" aria-controls="collapse{{ $a->id }}">
                                                                <span style="font-size: 20px;">Báo cáo tuần (Từ
                                                                    ngày
                                                                    {{ $startDateOfWeek }} đến {{ $endDateOfWeek }})</span>

                                                            </button>
                                                        </h2>
                                                        <div id="collapse{{ $a->id }}" class="accordion-collapse collapse"
                                                            style="visibility: unset" aria-labelledby="heading{{ $a->id }}"
                                                            data-bs-parent="#reportsAccordion">
                                                            <div style="width: 100%; display: flex;justify-content: right;  align-items: center;">
                                                                <form style="text-align: right; padding: 20px;"
                                                                    action="{{ route('pdf.details', $a->id) }}" method="GET">
                                                                    @csrf
                                                                    <button id="run-cronjob-button" class="btn btn-primary">In PDF</button>
                                                                </form>
                                                                <form style="margin-right: 20px" action="{{ route('word.details', $a->id) }}" method="GET">
                                                                    @csrf
                                                                    <button id="run-cronjob-button" class="btn btn-primary">In Word</button>
                                                                </form>
                                                                <form style="margin-right: 20px" action="{{ route('excel.details', $a->id) }}" method="GET">
                                                                    @csrf
                                                                    <button id="run-cronjob-button" class="btn btn-primary">In Excel</button>
                                                                </form>
                                                            </div>

                                                            @php $list = json_decode($a->values, true);
                                                            @endphp
                                                            @foreach ($list as $arrayValues)

                                                                @php

                                                                    $workDone = !empty($arrayValues['WorkDone']) ? $arrayValues['WorkDone'] : [];
                                                                    $expectedWork = !empty($arrayValues['ExpectedWork']) ? $arrayValues['ExpectedWork'] : [];
                                                                    $requestWork = !empty($arrayValues['Request']) ? $arrayValues['Request'] : [];
                                                                    //    dd($workDone);
                                                                    $STT = 1;
                                                                    $STT_NEXT = 1;
                                                                    $nameDepartment = $arrayValues['DepartmentName'] ?? '';
                                                                @endphp
                                                                <div class="accordion-body">

                                                                    <h2 style="padding: 10px 0;">{{ $nameDepartment }}</h2>
                                                                    <!-- Hiển thị thông tin về công việc đã làm (Mục I) -->
                                                                    {{-- {{dd($item)}} --}}

                                                                    <h3>Mục I: Công việc đã làm</h3>
                                                                    @if (!empty($workDone))
                                                                        @foreach ($workDone as $work)
                                                                            <div style="padding: 10px;">
                                                                                <div>
                                                                                    <span
                                                                                        style="font-size:  20px; font-weight: bold">{{ $STT++ }}.
                                                                                        <span>{{ $work['work_done'] }}</span>
                                                                                    </span>
                                                                                </div>
                                                                                {{-- value_of_work --}}
                                                                                <div style="display: flex; margin: 10px">
                                                                                    <div
                                                                                        style="display: flex; flex: 2; align-items: center">
                                                                                        <span
                                                                                            style="font-size:  18px; font-weight: bold">Ngày
                                                                                            bắt
                                                                                            đầu:</span>
                                                                                        <div style="padding-left: 10px;">
                                                                                            {{ $work['start_date'] }}
                                                                                        </div>
                                                                                    </div>
                                                                                    <div
                                                                                        style="display: flex; flex: 2; align-items: center">
                                                                                        <span
                                                                                            style="font-size:  18px; font-weight: bold">Ngày
                                                                                            kết
                                                                                            thúc:</span>

                                                                                        <div style="padding-left: 10px;">
                                                                                            {{ $work['end_date'] }}
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                @if(!empty($work['status_work']))
                                                                                    <div style="display: flex; margin: 10px">

                                                                                        <span
                                                                                            style="font-size:  18px; font-weight: bold">Tiến
                                                                                            độ:</span>

                                                                                        <div style="padding-left: 10px;">
                                                                                            {{ $work['status_work'] }}
                                                                                        </div>
                                                                                    </div>
                                                                                @endif
                                                                                <div style="display: flex; margin: 10px">

                                                                                    <span
                                                                                        style="font-size:  18px; font-weight: bold">Nội
                                                                                        dung:</span>

                                                                                    <div style="padding-left: 10px;">
                                                                                        {{ $work['description'] }}
                                                                                    </div>
                                                                                </div>

                                                                            </div>
                                                                        @endforeach
                                                                    @else
                                                                        <span>Không tồn tại dữ liệu</span>
                                                                    @endif
                                                                    <!-- Hiển thị thông tin về công việc tuần tới (Mục II) -->
                                                                    <h3>Mục II: Công việc tuần tới</h3>
                                                                    @if (!empty($expectedWork))
                                                                        @foreach ($expectedWork as $work)
                                                                            <div style="padding: 10px;">
                                                                                <div>
                                                                                    <span
                                                                                        style="font-size:  20px; font-weight: bold">{{ $STT_NEXT++ }}.
                                                                                        <span>{{ $work['next_work'] }}</span>
                                                                                    </span>
                                                                                </div>
                                                                                {{-- value_of_work --}}
                                                                                <div style="display: flex; margin: 10px">
                                                                                    <div
                                                                                        style="display: flex; flex: 2; align-items: center">
                                                                                        <span
                                                                                            style="font-size:  18px; font-weight: bold">Ngày
                                                                                            bắt
                                                                                            đầu:</span>
                                                                                        <div style="padding-left: 10px;">
                                                                                            {{ $work['next_start_date'] }}
                                                                                        </div>
                                                                                    </div>
                                                                                    <div
                                                                                        style="display: flex; flex: 2; align-items: center">
                                                                                        <span
                                                                                            style="font-size:  18px; font-weight: bold">Ngày
                                                                                            kết
                                                                                            thúc:</span>

                                                                                        <div style="padding-left: 10px;">
                                                                                            {{ $work['next_end_date'] }}
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                @if(!empty($work['next_status_work']))
                                                                                    <div style="display: flex; margin: 10px">

                                                                                        <span
                                                                                            style="font-size:  18px; font-weight: bold">Tiến
                                                                                            độ:</span>

                                                                                        <div style="padding-left: 10px;">
                                                                                            {{ $work['next_status_work'] }}
                                                                                        </div>
                                                                                    </div>
                                                                                @endif
                                                                                <div style="display: flex; margin: 10px">

                                                                                    <span
                                                                                        style="font-size:  18px; font-weight: bold">Nội
                                                                                        dung:</span>

                                                                                    <div style="padding-left: 10px;">
                                                                                        {{ $work['next_description'] }}
                                                                                    </div>
                                                                                </div>

                                                                            </div>
                                                                        @endforeach
                                                                    @else
                                                                        <span>Không tồn tại dữ liệu</span>
                                                                    @endif
                                                                    <!-- Hiển thị thông tin về kiến nghị (Mục III) -->
                                                                    <h3>Mục III: Kiến nghị</h3>
                                                                    @if (!empty($requestWork))
                                                                        <p style="padding: 20px">{{ $requestWork }}</p>
                                                                    @else
                                                                        <span>Không tồn tại dữ liệu</span>
                                                                    @endif

                                                                </div>
                                                                <p style="border-bottom: 1px solid #e5e7eb; margin-bottom: 0;"></p>
                                                            @endforeach


                                                        </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                @endif

                                @if(!empty($departmentId) && isset($reportCenter))
                                    <div id="search-results-department">

                                        @if(!empty($departmentId))
                                            @if(!empty($dataDepartment))
                                                @foreach ($dataDepartment as $key => $a)
                                                    <div style="border: 1px solid rgb(243 244 246 / var(--tw-bg-opacity));">
                                                        <h2 class="accordion-header" id="heading{{ $key }}">

                                                            <button class="accordion-button collapsed" type="button"
                                                                    data-bs-toggle="collapse" data-bs-target="#collapse{{ $key }}"
                                                                    aria-expanded="false" aria-controls="collapse{{ $key }}">
                                                                    <span style="font-size: 20px;">{{ $departmentReportDate[$key] }}</span>
                                                            </button>
                                                            </h2>
                                                            <div id="collapse{{ $key }}" class="accordion-collapse collapse"
                                                                style="visibility: unset" aria-labelledby="heading{{ $key }}"
                                                                data-bs-parent="#reportsAccordion">
                                                                <h2 style="padding: 10px 0;">{{ $a['DepartmentName'] }}</h2>

                                                
                                                                    @php
                                                                        $arrayValues = $a;
                                                                        $workDone = !empty($arrayValues['WorkDone']) ? $arrayValues['WorkDone'] : [];
                                                
                                                                        $expectedWork = !empty($arrayValues['ExpectedWork']) ? $arrayValues['ExpectedWork'] : [];
                                                                        $requestWork = !empty($arrayValues['Request']) ? $arrayValues['Request'] : [];
                                                                        $STT = 1;
                                                                        $STT_NEXT = 1;
                                                                    @endphp
                                                                    
                                                                        <div class="accordion-body">
                                                                            <h3>Mục I: Công việc đã làm</h3>
                                                                            
                                                                                @if (!empty($arrayValues))
                                                                                    @foreach($a['WorkDone'] as $work)
                                                                                        <div style="padding: 10px;">
                                                                                            <div>
                                                                                                <span
                                                                                                    style="font-size:  20px; font-weight: bold">{{ $STT++ }}.
                                                                                                </span>
                                                                                                <span style="font-size:  20px; font-weight: bold">{{ $work['work_done'] }}</span>
                                                                                            </div>
                                                                                            {{-- value_of_work --}}
                                                                                            <div style="display: flex; margin: 10px">
                                                                                                <div
                                                                                                    style="display: flex; flex: 2; align-items: center">
                                                                                                    <span
                                                                                                        style="font-size:  18px; font-weight: bold">Ngày
                                                                                                        bắt
                                                                                                        đầu:</span>
                                                                                                    <div style="padding-left: 10px;">
                                                                                                        {{ $work['start_date'] }}
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div
                                                                                                    style="display: flex; flex: 2; align-items: center">
                                                                                                    <span
                                                                                                        style="font-size:  18px; font-weight: bold">Ngày
                                                                                                        kết
                                                                                                        thúc:</span>

                                                                                                    <div style="padding-left: 10px;">
                                                                                                        {{ $work['end_date'] }}
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                            @if(!empty($work['work_status']))
                                                                                            <div style="display: flex; margin: 10px">

                                                                                                <span
                                                                                                    style="font-size:  18px; font-weight: bold">Tiến
                                                                                                    độ:</span>

                                                                                                <div style="padding-left: 10px;">
                                                                                                    {{ $work['work_status'] }}
                                                                                                </div>
                                                                                            </div>
                                                                                            @endif
                                                                                            <div style="display: flex; margin: 10px">

                                                                                                <span
                                                                                                    style="font-size:  18px; font-weight: bold">Nội
                                                                                                    dung:</span>

                                                                                                <div style="padding-left: 10px;">
                                                                                                    {{ $work['description'] }}
                                                                                                </div>
                                                                                            </div>

                                                                                        </div>
                                                                                    @endforeach
                                                                                @else
                                                                                    <span>Không tồn tại dữ liệu</span>
                                                                                @endif
                                                                    
                                                                                @if (!empty($arrayValues))
                                                                                    <h3>Mục II: Công việc tuần tới</h3>
                                                                                    @foreach($a['ExpectedWork'] as $work)
                                                                                    <div style="padding: 10px;">
                                                                                        <div>
                                                                                            <span
                                                                                                style="font-size:  20px; font-weight: bold">{{ $STT++ }}.
                                                                                            </span>
                                                                                            <span style="font-size:  20px; font-weight: bold">{{ $work['next_work'] }}</span>
                                                                                        </div>
                                                                                        {{-- value_of_work --}}
                                                                                        <div style="display: flex; margin: 10px">
                                                                                            <div
                                                                                                style="display: flex; flex: 2; align-items: center">
                                                                                                <span
                                                                                                    style="font-size:  18px; font-weight: bold">Ngày
                                                                                                    bắt
                                                                                                    đầu:</span>
                                                                                                <div style="padding-left: 10px;">
                                                                                                    {{ $work['next_start_date'] }}
                                                                                                </div>
                                                                                            </div>
                                                                                            <div
                                                                                                style="display: flex; flex: 2; align-items: center">
                                                                                                <span
                                                                                                    style="font-size:  18px; font-weight: bold">Ngày
                                                                                                    kết
                                                                                                    thúc:</span>

                                                                                                <div style="padding-left: 10px;">
                                                                                                    {{ $work['next_end_date'] }}
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div style="display: flex; margin: 10px">

                                                                                            <span
                                                                                                style="font-size:  18px; font-weight: bold">Tiến
                                                                                                độ:</span>

                                                                                            <div style="padding-left: 10px;">
                                                                                                {{ $work['next_status_work'] }}
                                                                                            </div>
                                                                                        </div>
                                                                                        <div style="display: flex; margin: 10px">

                                                                                            <span
                                                                                                style="font-size:  18px; font-weight: bold">Nội
                                                                                                dung:</span>

                                                                                            <div style="padding-left: 10px;">
                                                                                                {{ $work['next_description'] }}
                                                                                            </div>
                                                                                        </div>

                                                                                    </div>
                                                                                    @endforeach
                                                                                @else
                                                                                    <span>Không tồn tại dữ liệu</span>
                                                                                @endif
                                                                        
                                                                            <h3>Mục III: Kiến nghị</h3>

                                                                            @if (!empty($a['Request']))
                                                                                <p style="padding: 20px">{{ $a['Request'] }}</p>
                                                                            @else
                                                                                <span>Không tồn tại dữ liệu</span>
                                                                            @endif
                                                                        
                                                                        <!-- Hiển thị thông tin về công việc tuần tới (Mục II) -->
                                                                        

                                                                        </div>
                                                                    
                                                                    
                                                                    <p style="border-bottom: 1px solid #e5e7eb; margin-bottom: 0;"></p>

                                                    
                                                            </div>
                                                
                                                    </div>
                                                @endforeach  
                                            @else
                                                <span>Không tồn tại dữ liệu</span>
                                            @endif              
                                        @endif    
                                    </div>
                                @endif

                                @if(!empty($resultLog))
                                    @php
                                        $createdDate = \Carbon\Carbon::parse($reportWork->created_at);
                                        $weekNumber = $createdDate->weekOfYear;
                                        $startDateOfWeek = $createdDate->startOfWeek()->format('d-m-Y');
                                        $endDateOfWeek = $createdDate->endOfWeek()->format('d-m-Y');
                                    @endphp
                                    <div id="search-results-department">
                                        <div style="border: 1px solid rgb(243 244 246 / var(--tw-bg-opacity));">
                                            <h2 class="accordion-header" id="heading{{ $reportWork['id'] }}">

                                                <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#collapse{{ $reportWork['id'] }}"
                                                        aria-expanded="false" aria-controls="collapse{{ $reportWork['id'] }}">
                                                        <span style="font-size: 20px;">Báo cáo tuần (Từ
                                                                    ngày
                                                                    {{ $startDateOfWeek }} đến {{ $endDateOfWeek }})</span>

                                                </button>
                                                </h2>
                                                <div id="collapse{{ $reportWork['id'] }}" class="accordion-collapse collapse"
                                                    style="visibility: unset" aria-labelledby="heading{{ $reportWork['id'] }}"
                                                    data-bs-parent="#reportsAccordion">
                                                    <h2 style="padding: 10px 0;">{{ $departmentName }}</h2>

                                    
                                                        @php
                                                            $arrayValue = $resultLog;
                                                            $workDone = !empty($arrayValues['WorkDone']) ? $arrayValues['WorkDone'] : [];
                                    
                                                            $expectedWork = !empty($arrayValues['ExpectedWork']) ? $arrayValues['ExpectedWork'] : [];
                                                            $requestWork = !empty($arrayValues['Request']) ? $arrayValues['Request'] : [];
                                                            $STT = 1;
                                                            $STT_NEXT = 1;
                                                        @endphp
                                                        @foreach($arrayValue as $arrayValues)
                                                            <div class="accordion-body">
                                                                <h3>Mục I: Công việc đã làm</h3>
                                                                    @if (!empty($arrayValues))
                                                                        @foreach($arrayValues['WorkDone'] as $work)
                                                                            <div style="padding: 10px;">
                                                                                <div>
                                                                                    <span
                                                                                        style="font-size:  20px; font-weight: bold">{{ $STT++ }}.
                                                                                    </span>
                                                                                    <span style="font-size:  20px; font-weight: bold">{{ $work['work_done'] }}</span>
                                                                                </div>
                                                                                {{-- value_of_work --}}
                                                                                <div style="display: flex; margin: 10px">
                                                                                    <div
                                                                                        style="display: flex; flex: 2; align-items: center">
                                                                                        <span
                                                                                            style="font-size:  18px; font-weight: bold">Ngày
                                                                                            bắt
                                                                                            đầu:</span>
                                                                                        <div style="padding-left: 10px;">
                                                                                            {{ $work['start_date'] }}
                                                                                        </div>
                                                                                    </div>
                                                                                    <div
                                                                                        style="display: flex; flex: 2; align-items: center">
                                                                                        <span
                                                                                            style="font-size:  18px; font-weight: bold">Ngày
                                                                                            kết
                                                                                            thúc:</span>

                                                                                        <div style="padding-left: 10px;">
                                                                                            {{ $work['end_date'] }}
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                @if(!empty($work['work_status']))
                                                                                <div style="display: flex; margin: 10px">

                                                                                    <span
                                                                                        style="font-size:  18px; font-weight: bold">Tiến
                                                                                        độ:</span>

                                                                                    <div style="padding-left: 10px;">
                                                                                        {{ $work['work_status'] }}
                                                                                    </div>
                                                                                </div>
                                                                                @endif
                                                                                <div style="display: flex; margin: 10px">

                                                                                    <span
                                                                                        style="font-size:  18px; font-weight: bold">Nội
                                                                                        dung:</span>

                                                                                    <div style="padding-left: 10px;">
                                                                                        {{ $work['description'] }}
                                                                                    </div>
                                                                                </div>

                                                                            </div>
                                                                        @endforeach
                                                                    @else
                                                                        <span>Không tồn tại dữ liệu</span>
                                                                    @endif
                                                        
                                                                    @if (!empty($arrayValues))
                                                                        <h3>Mục II: Công việc tuần tới</h3>
                                                                        @foreach($arrayValues['ExpectedWork'] as $work)
                                                                        <div style="padding: 10px;">
                                                                            <div>
                                                                                <span
                                                                                    style="font-size:  20px; font-weight: bold">{{ $STT++ }}.
                                                                                </span>
                                                                                <span style="font-size:  20px; font-weight: bold">{{ $work['next_work'] }}</span>
                                                                            </div>
                                                                            {{-- value_of_work --}}
                                                                            <div style="display: flex; margin: 10px">
                                                                                <div
                                                                                    style="display: flex; flex: 2; align-items: center">
                                                                                    <span
                                                                                        style="font-size:  18px; font-weight: bold">Ngày
                                                                                        bắt
                                                                                        đầu:</span>
                                                                                    <div style="padding-left: 10px;">
                                                                                        {{ $work['next_start_date'] }}
                                                                                    </div>
                                                                                </div>
                                                                                <div
                                                                                    style="display: flex; flex: 2; align-items: center">
                                                                                    <span
                                                                                        style="font-size:  18px; font-weight: bold">Ngày
                                                                                        kết
                                                                                        thúc:</span>

                                                                                    <div style="padding-left: 10px;">
                                                                                        {{ $work['next_end_date'] }}
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div style="display: flex; margin: 10px">

                                                                                <span
                                                                                    style="font-size:  18px; font-weight: bold">Tiến
                                                                                    độ:</span>

                                                                                <div style="padding-left: 10px;">
                                                                                    {{ $work['next_status_work'] }}
                                                                                </div>
                                                                            </div>
                                                                            <div style="display: flex; margin: 10px">

                                                                                <span
                                                                                    style="font-size:  18px; font-weight: bold">Nội
                                                                                    dung:</span>

                                                                                <div style="padding-left: 10px;">
                                                                                    {{ $work['next_description'] }}
                                                                                </div>
                                                                            </div>

                                                                        </div>
                                                                        @endforeach
                                                                    @else
                                                                        <span>Không tồn tại dữ liệu</span>
                                                                    @endif
                                                            
                                                                <h3>Mục III: Kiến nghị</h3>

                                                                @if (!empty($arrayValues['Request']))
                                                                    <p style="padding: 20px">{{ $arrayValues['Request'] }}</p>
                                                                @else
                                                                    <span>Không tồn tại dữ liệu</span>
                                                                @endif
                                                            
                                                            <!-- Hiển thị thông tin về công việc tuần tới (Mục II) -->
                                                            </div>
                                                        @endforeach
                                                        
                                                        
                                                        <p style="border-bottom: 1px solid #e5e7eb; margin-bottom: 0;"></p>

                                        
                                                </div>
                                    
                                        </div>   
                                    </div>
                                @endif
                            @endif
                            @if (!empty($reports))
                                @foreach ($reports as $report)
                                    <div class="accordion-item">
                                        @php
                                            $createdDate = \Carbon\Carbon::parse($report->created_at);
                                            // dd($createdDate);
                                            $weekNumber = $createdDate->weekOfYear;
                                            $startDateOfWeek = $createdDate->startOfWeek()->format('d-m-Y');
                                            $endDateOfWeek = $createdDate->endOfWeek()->format('d-m-Y');
                                            
                                        @endphp
                                        <h2 class="accordion-header" id="heading{{ $report->id }}">
                                            <button class="accordion-button collapsed" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#collapse{{ $report->id }}"
                                                aria-expanded="false" aria-controls="collapse{{ $report->id }}">
                                                <span style="font-size: 20px;">Báo cáo tuần {{ $Week++ }} (Từ
                                                    ngày
                                                    {{ $startDateOfWeek }} đến {{ $endDateOfWeek }})</span>

                                            </button>
                                        </h2>
                                        <div id="collapse{{ $report->id }}" class="accordion-collapse collapse"
                                            style="visibility: unset" aria-labelledby="heading{{ $report->id }}"
                                            data-bs-parent="#reportsAccordion">
                                            <div style="width: 100%; display: flex;justify-content: right;  align-items: center;">
                                                <form style="text-align: right; padding: 20px;"
                                                    action="{{ route('pdf.details', $report->id) }}" method="GET">
                                                    @csrf
                                                    <button id="run-cronjob-button" class="btn btn-primary">In PDF</button>
                                                </form>
                                                <form style="margin-right: 20px" action="{{ route('word.details', $report->id) }}" method="GET">
                                                    @csrf
                                                    <button id="run-cronjob-button" class="btn btn-primary">In Word</button>
                                                </form>
                                                <form style="margin-right: 20px" action="{{ route('excel.details', $report->id) }}" method="GET">
                                                    @csrf
                                                    <button id="run-cronjob-button" class="btn btn-primary">In Excel</button>
                                                </form>
                                            </div>

                                            @foreach (json_decode($report->values, true) as $repo)
                                                @php
                                                    $arrayValues = $repo;
                                                    
                                                    $workDone = !empty($arrayValues['WorkDone']) ? $arrayValues['WorkDone'] : [];
                                                    $expectedWork = !empty($arrayValues['ExpectedWork']) ? $arrayValues['ExpectedWork'] : [];
                                                    $requestWork = !empty($arrayValues['Request']) ? $arrayValues['Request'] : [];
                                                    //    dd($workDone);
                                                    $STT = 1;
                                                    $STT_NEXT = 1;
                                                    $nameDepartment = $arrayValues['DepartmentName'] ?? '';
                                                @endphp
                                                <div class="accordion-body">

                                                    <h2 style="padding: 10px 0;">{{ $nameDepartment }}</h2>
                                                    <!-- Hiển thị thông tin về công việc đã làm (Mục I) -->
                                                    {{-- {{dd($item)}} --}}

                                                    <h3>Mục I: Công việc đã làm</h3>
                                                    @if (!empty($workDone))
                                                        @foreach ($workDone as $work)
                                                            <div style="padding: 10px;">
                                                                <div>
                                                                    <span
                                                                        style="font-size:  20px; font-weight: bold">{{ $STT++ }}.
                                                                        <span>{{ $work['work_done'] }}</span>
                                                                    </span>
                                                                </div>
                                                                {{-- value_of_work --}}
                                                                <div style="display: flex; margin: 10px">
                                                                    <div
                                                                        style="display: flex; flex: 2; align-items: center">
                                                                        <span
                                                                            style="font-size:  18px; font-weight: bold">Ngày
                                                                            bắt
                                                                            đầu:</span>
                                                                        <div style="padding-left: 10px;">
                                                                            {{ $work['start_date'] }}
                                                                        </div>
                                                                    </div>
                                                                    <div
                                                                        style="display: flex; flex: 2; align-items: center">
                                                                        <span
                                                                            style="font-size:  18px; font-weight: bold">Ngày
                                                                            kết
                                                                            thúc:</span>

                                                                        <div style="padding-left: 10px;">
                                                                            {{ $work['end_date'] }}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                @if(!empty($work['status_work']))
                                                                    <div style="display: flex; margin: 10px">

                                                                        <span
                                                                            style="font-size:  18px; font-weight: bold">Tiến
                                                                            độ:</span>
                                                                        <div style="padding-left: 10px;">
                                                                            {{ $work['status_work'] }}
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                                <div style="display: flex; margin: 10px">

                                                                    <span
                                                                        style="font-size:  18px; font-weight: bold">Nội
                                                                        dung:</span>

                                                                    <div style="padding-left: 10px;">
                                                                        {{ $work['description'] }}
                                                                    </div>
                                                                </div>

                                                            </div>
                                                        @endforeach
                                                    @else
                                                        <span>Không tồn tại dữ liệu</span>
                                                    @endif
                                                    <!-- Hiển thị thông tin về công việc tuần tới (Mục II) -->
                                                    <h3>Mục II: Công việc tuần tới</h3>
                                                    @if (!empty($expectedWork))
                                                        @foreach ($expectedWork as $work)
                                                            <div style="padding: 10px;">
                                                                <div>
                                                                    <span
                                                                        style="font-size:  20px; font-weight: bold">{{ $STT_NEXT++ }}.
                                                                        <span>{{ $work['next_work'] }}</span>
                                                                    </span>
                                                                </div>
                                                                {{-- value_of_work --}}
                                                                <div style="display: flex; margin: 10px">
                                                                    <div
                                                                        style="display: flex; flex: 2; align-items: center">
                                                                        <span
                                                                            style="font-size:  18px; font-weight: bold">Ngày
                                                                            bắt
                                                                            đầu:</span>
                                                                        <div style="padding-left: 10px;">
                                                                            {{ $work['next_start_date'] }}
                                                                        </div>
                                                                    </div>
                                                                    <div
                                                                        style="display: flex; flex: 2; align-items: center">
                                                                        <span
                                                                            style="font-size:  18px; font-weight: bold">Ngày
                                                                            kết
                                                                            thúc:</span>

                                                                        <div style="padding-left: 10px;">
                                                                            {{ $work['next_end_date'] }}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                @if(!empty($work['next_status_work']))
                                                                <div style="display: flex; margin: 10px">

                                                                    <span
                                                                        style="font-size:  18px; font-weight: bold">Tiến
                                                                        độ:</span>

                                                                    <div style="padding-left: 10px;">
                                                                        {{ $work['next_status_work'] }}
                                                                    </div>
                                                                </div>
                                                                @endif
                                                                <div style="display: flex; margin: 10px">

                                                                    <span
                                                                        style="font-size:  18px; font-weight: bold">Nội
                                                                        dung:</span>

                                                                    <div style="padding-left: 10px;">
                                                                        {{ $work['next_description'] }}
                                                                    </div>
                                                                </div>

                                                            </div>
                                                        @endforeach
                                                    @else
                                                        <span>Không tồn tại dữ liệu</span>
                                                    @endif
                                                    <!-- Hiển thị thông tin về kiến nghị (Mục III) -->
                                                    <h3>Mục III: Kiến nghị</h3>
                                                    @if (!empty($requestWork))
                                                        <p style="padding: 20px">{{ $requestWork }}</p>
                                                    @else
                                                        <span>Không tồn tại dữ liệu</span>
                                                    @endif

                                                </div>
                                                <p style="border-bottom: 1px solid #e5e7eb; margin-bottom: 0;"></p>
                                            @endforeach


                                        </div>


                                    </div>
                                @endforeach
                            @else
                                <!-- <div class="alert alert-danger">
                                    <span>Không tồn tại dữ liệu</span>
                                </div> -->
                            @endif
                           @endif
                           @if(auth()->user()->role === 'staff')
                        @if (isset($array) )
                                @php
                                    $rowCount = 1;
                                    $rowCount2 = 1;
                                @endphp
                                  @foreach ($array as $array)
                                <div class="accordion-item">
                                  
                                    @php
                                        $Week = 1;
                                        // dd($array);
                                        $arrayValues = json_decode($array->values, true);
                                        
                                        $workDone = !empty($arrayValues['WorkDone']) ? $arrayValues['WorkDone'] : [];
                                        $expectedWork = !empty($arrayValues['ExpectedWork']) ? $arrayValues['ExpectedWork'] : [];
                                        $requestWork = !empty($arrayValues['Request']) ? $arrayValues['Request'] : [];
                                      
                                        $STT = 1;
                                        $STT_NEXT = 1;
                                        $nameDepartment = $arrayValues['DepartmentName'] ?? '';
                                        $createdDate = \Carbon\Carbon::parse($array->created_at);
                                        // dd($createdDate);
                                        $weekNumber = $createdDate->weekOfYear;
                                        $startDateOfWeek = $createdDate->startOfWeek()->format('d-m-Y');
                                        $endDateOfWeek = $createdDate->endOfWeek()->format('d-m-Y');
                                        
                                    @endphp
                                    <h2 class="accordion-header" id="heading{{ $array->id }}">
                                        <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#collapse{{ $array->id }}"
                                            aria-expanded="false" aria-controls="collapse{{ $array->id }}">
                                            <span style="font-size: 20px;">Công việc tuần {{ $Week++ }} (Từ ngày
                                                {{ $startDateOfWeek }} đến {{ $endDateOfWeek }})</span>

                                        </button>
                                    </h2>
                                    <div id="collapse{{ $array->id }}" class="accordion-collapse collapse"
                                        style="visibility: unset" aria-labelledby="heading{{ $array->id }}"
                                        data-bs-parent="#reportsAccordion">
                                        <div class="accordion-body">
                                            {{-- <h2 style="text-align: center">{{ $item['name'] }}</h2> --}}
                                            <!-- Hiển thị thông tin về công việc đã làm (Mục I) -->
                                            {{-- {{dd($item)}} --}}
                                            <h3>Mục I: Công việc đã làm</h3>
                                            @if (!empty($workDone))
                                                @foreach ($workDone as $work)
                                                    <div style="padding: 10px;">
                                                        <div>
                                                            <span
                                                                style="font-size:  20px; font-weight: bold">{{ $STT++ }}.
                                                                <span>{{ $work['work_done'] }}</span>
                                                            </span>
                                                        </div>
                                                        {{-- value_of_work --}}
                                                        <div style="display: flex; margin: 10px">
                                                            <div style="display: flex; flex: 2; align-items: center">
                                                                <span style="font-size:  18px; font-weight: bold">Ngày
                                                                    bắt
                                                                    đầu:</span>
                                                                <div style="padding-left: 10px;">
                                                                    {{ $work['start_date'] }}
                                                                </div>
                                                            </div>
                                                            <div style="display: flex; flex: 2; align-items: center">
                                                                <span style="font-size:  18px; font-weight: bold">Ngày
                                                                    kết
                                                                    thúc:</span>

                                                                <div style="padding-left: 10px;">
                                                                    {{ $work['end_date'] }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div style="display: flex; margin: 10px">

                                                            <span style="font-size:  18px; font-weight: bold">Tiến
                                                                độ:</span>

                                                            <div style="padding-left: 10px;">
                                                                {{ $work['status_work'] }}
                                                            </div>
                                                        </div>
                                                        <div style="display: flex; margin: 10px">

                                                            <span style="font-size:  18px; font-weight: bold">Nội
                                                                dung:</span>

                                                            <div style="padding-left: 10px;">
                                                                {{ $work['description'] }}
                                                            </div>
                                                        </div>

                                                    </div>
                                                @endforeach
                                            @else
                                                <span>Không tồn tại dữ liệu</span>
                                            @endif
                                            <!-- Hiển thị thông tin về công việc tuần tới (Mục II) -->
                                            <h3>Mục II: Công việc tuần tới</h3>
                                            @if (!empty($expectedWork))
                                                @foreach ($expectedWork as $work)
                                                    <div style="padding: 10px;">
                                                        <div>
                                                            <span
                                                                style="font-size:  20px; font-weight: bold">{{ $STT_NEXT++ }}.
                                                                <span>{{ $work['next_work'] }}</span>
                                                            </span>
                                                        </div>
                                                        {{-- value_of_work --}}
                                                        <div style="display: flex; margin: 10px">
                                                            <div style="display: flex; flex: 2; align-items: center">
                                                                <span style="font-size:  18px; font-weight: bold">Ngày
                                                                    bắt
                                                                    đầu:</span>
                                                                <div style="padding-left: 10px;">
                                                                    {{ $work['next_start_date'] }}
                                                                </div>
                                                            </div>
                                                            <div style="display: flex; flex: 2; align-items: center">
                                                                <span style="font-size:  18px; font-weight: bold">Ngày
                                                                    kết
                                                                    thúc:</span>

                                                                <div style="padding-left: 10px;">
                                                                    {{ $work['next_end_date'] }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div style="display: flex; margin: 10px">

                                                            <span style="font-size:  18px; font-weight: bold">Tiến
                                                                độ:</span>

                                                            <div style="padding-left: 10px;">
                                                                {{ $work['next_status_work'] }}
                                                            </div>
                                                        </div>
                                                        <div style="display: flex; margin: 10px">

                                                            <span style="font-size:  18px; font-weight: bold">Nội
                                                                dung:</span>

                                                            <div style="padding-left: 10px;">
                                                                {{ $work['next_description'] }}
                                                            </div>
                                                        </div>

                                                    </div>
                                                @endforeach
                                            @else
                                                <span>Không tồn tại dữ liệu</span>
                                            @endif
                                            <!-- Hiển thị thông tin về kiến nghị (Mục III) -->
                                            <h3>Mục III: Kiến nghị</h3>
                                            @if (!empty($requestWork))
                                                <p style="padding: 20px">{{ $requestWork }}</p>
                                            @else
                                                <span>Không tồn tại dữ liệu</span>
                                            @endif
                                        </div>

                                    </div>
                              
                                </div>
                                @endforeach
                        </div>
                    @else
                    <div class="alert alert-danger">
                        <span>Không tồn tại dữ liệu</span>
                    </div>
                        @endif
                    @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
    </div>
    <script>
        const startDateInput = document.getElementById("start-date");
        const endDateInput = document.getElementById("end-date");

        const currentDate = new Date();
        const monday = new Date(currentDate);
        monday.setDate(currentDate.getDate() - currentDate.getDay() + (currentDate.getDay() === 0 ? -6 : 1));
        const sunday = new Date(currentDate);
        sunday.setDate(currentDate.getDate() + (7 - currentDate.getDay()));
        startDateInput.setAttribute("value", formatDate(monday));
        endDateInput.setAttribute("value", formatDate(sunday));
        startDateInput.setAttribute("max", formatDate(currentDate));
        endDateInput.setAttribute("min", formatDate(currentDate));

        function formatDate(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, "0");
            const day = String(date.getDate()).padStart(2, "0");
            return `${year}-${month}-${day}`;
        }

        function updateHiddenInput() {
            var selectedOption = document.getElementById('departmentSelect').value;
            document.getElementById('departmentInput').value = selectedOption;
        }

        function updateEndDateHidden() {
            var endDateValue = document.getElementById('end_date_admin').value;
            document.getElementById('end_date_hidden').value = endDateValue;
        }

        function updateStartDateHidden() {
            var startDateValue = document.getElementById('start_date_admin').value;
            document.getElementById('start_date_hidden').value = startDateValue;
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</x-app-layout>
