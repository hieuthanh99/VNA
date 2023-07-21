<!DOCTYPE html>
<html>
<head>
    <title>Báo cáo tuần</title>
    <!-- Thêm CSS của Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        * { 
            font-family: DejaVu Sans;
            font-size: 12px;
        }
        .container {
            padding: 20px;
        }
        .mb-30 {
            margin-bottom: 30px;
        }
        .h1-custom {
            font-size: 24px;
            font-weight: bold;
        }
        .h2-custom {
            font-size: 20px;
            font-weight: bold;
            margin-top: 30px;
            margin-bottom: 10px;
        }
        .ul-custom {
            list-style: none;
            padding-left: 0;
        }
        .li-custom {
            margin-bottom: 10px;
        }
        .p-custom {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container mb-30">
        <h1 style= "text-align: center; margin-bottom: 10px;" class="h1-custom">Báo cáo tuần </h1>
        <h2 style= "text-align: center; margin-top: 0px;" class="h2-custom">(Từ ngày{{ $department['startDate']->format('d-m-Y') }} đến {{ $department['endDateWeek']->format('d-m-Y') }})</h2>
        <br>
        <br>
        @foreach ($department['mergedArray'] as $department)
        <h1 class="h1-custom">{{ $department['DepartmentName'] }}</h1>

        <h2 class="h2-custom">I. Công việc đã thực hiện</h2>
        @if (count($department['WorkDone']) > 0)
            <ul class="ul-custom">
                @foreach ($department['WorkDone'] as $work)
                    <li class="li-custom">
                        <strong>Tiêu đề:</strong> {{ $work['work_done'] }}<br>
                        <strong>Nội dung:</strong> {{ $work['description'] }}<br>
                        <strong>Ngày bắt đầu:</strong> {{ $work['start_date'] }}<br>
                        <strong>Kết thúc:</strong> {{ $work['end_date'] }}<br>
                        <strong>Tiến độ:</strong> {{ $work['status_work'] }}
                    </li>
                @endforeach
            </ul>
        @else
            <p class="p-custom">Không có công việc đã thực hiện.</p>
        @endif

        <h2 class="h2-custom">II. Công việc dự kiến</h2>
        @if (count($department['ExpectedWork']) > 0)
            <ul class="ul-custom">
                @foreach ($department['ExpectedWork'] as $work)
                    <li class="li-custom">
                        <strong>Tiêu đề:</strong> {{ $work['next_work'] }}<br>
                        <strong>Nội dung:</strong> {{ $work['next_description'] }}<br>
                        <strong>Ngày bắt đầu:</strong> {{ $work['next_start_date'] }}<br>
                        <strong>Kết thúc:</strong> {{ $work['next_end_date'] }}<br>
                        <strong>Tiến độ:</strong> {{ $work['next_status_work'] }}
                    </li>
                @endforeach
            </ul>
        @else
            <p class="p-custom">Không có công việc dự kiến.</p>
        @endif

        <h2 class="h2-custom">III. Kiến nghị</h2>
        <p class="p-custom">{{ $department['Request'] }}</p>
        @endforeach
    </div>
</body>
</html>
