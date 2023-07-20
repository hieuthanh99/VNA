<x-app-layout>
    <x-slot name="header">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Include Bootstrap JS and dependencies -->

        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Danh sách báo cáo') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="container mt-4">
                        <div class="accordion" id="reportsAccordion">
                            @php
                            $id = 0;
                            @endphp
                            @foreach ($reports as $report)
                                <div class="accordion-item">
                                
                                    @php
                                        $Week = 1;
                                        $values = $report->values;
                                        $arrayValues = json_decode($values, true)[$id++];
                                        $workDone = isset($arrayValues['WorkDone']) ? $arrayValues['WorkDone'] : [];
                                        $expectedWork = isset($arrayValues['ExpectedWork']) ? $arrayValues['ExpectedWork'] : [];
                                        $requestWork = isset($arrayValues['Request']) ? $arrayValues['Request'] : [];
                                        //    dd($workDone);
                                        $STT = 1;
                                        $STT_NEXT = 1;
                                        $nameDepartment = $arrayValues['DepartmentName'] ?? "";
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
                                            <span style="font-size: 20px;">Báo cáo tuần {{$Week++}} (Từ ngày {{ $startDateOfWeek }} đến {{ $endDateOfWeek }})</span>
                                         
                                        </button>
                                    </h2>
                                    <div id="collapse{{ $report->id }}" class="accordion-collapse collapse"
                                        style="visibility: unset" aria-labelledby="heading{{ $report->id }}"
                                        data-bs-parent="#reportsAccordion">
                                        @foreach ($department as $item)
                                            
                                            <div class="accordion-body">
                                                <h2 style="text-align: center">{{$item['name']}}</h2>
                                                <!-- Hiển thị thông tin về công việc đã làm (Mục I) -->
                                                {{-- {{dd($item)}} --}}
                                                @if($item['id'] == $arrayValues['DepartmentId'])
                                                    <h3>Mục I: Công việc đã làm</h3>
                                                    @if(isset($workDone))
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
                                                                        <span style="font-size:  18px; font-weight: bold">Ngày bắt
                                                                            đầu:</span>
                                                                        <div style="padding-left: 10px;">
                                                                            {{ $work['start_date'] }}
                                                                        </div>
                                                                    </div>
                                                                    <div style="display: flex; flex: 2; align-items: center">
                                                                        <span style="font-size:  18px; font-weight: bold">Ngày kết
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
                                                    @if(isset($expectedWork))
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
                                                                        <span style="font-size:  18px; font-weight: bold">Ngày bắt
                                                                            đầu:</span>
                                                                        <div style="padding-left: 10px;">
                                                                            {{ $work['next_start_date'] }}
                                                                        </div>
                                                                    </div>
                                                                    <div style="display: flex; flex: 2; align-items: center">
                                                                        <span style="font-size:  18px; font-weight: bold">Ngày kết
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
                                                    @if(isset($requestWork))
                                                        <p style="padding: 20px">{{ $requestWork }}</p>
                                                    @else
                                                        <span>Không tồn tại dữ liệu</span>
                                                    @endif
                                                    @else
                                                    <span>Không tồn tại dữ liệu</span>
                                                    @endif
                                            </div>

                                        @endforeach
                                      
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</x-app-layout>
