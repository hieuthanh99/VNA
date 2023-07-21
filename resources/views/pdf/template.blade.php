<!DOCTYPE html>
<html>
<head>
    <title>Báo cáo tuần</title>
    <style>
        *{ font-family: DejaVu Sans; font-size: 12px;}
    </style>
</head>
<body>
    @foreach ($department as $department)
    <h1>Báo cáo tuần cho {{ $department['DepartmentName'] }}</h1>

    <h2>I. Công việc đã thực hiện</h2>
    @if (count($department['WorkDone']) > 0)
        <ul>
            @foreach ($department['WorkDone'] as $work)
                <li>
                    Tiêu đề: {{ $work['work_done'] }}
                    Nội dung: {{ $work['description'] }}
                    Ngày bắt đầu: {{ $work['start_date'] }}
                    Kết thúc: {{ $work['end_date'] }}
                    Tiến độ: {{ $work['status_work'] }}
                </li>
            @endforeach
        </ul>
    @else
        <p>Không có công việc đã thực hiện.</p>
    @endif

    <h2>II. Công việc dự kiến</h2>
    @if (count($department['ExpectedWork']) > 0)
        <ul>
            @foreach ($department['ExpectedWork'] as $work)
                <li>
                    Tiêu đề: {{ $work['next_work'] }}
                    Nội dung: {{ $work['next_description'] }}
                    Ngày bắt đầu: {{ $work['next_start_date'] }}
                    Kết thúc: {{ $work['next_end_date'] }}
                    Tiến độ: {{ $work['next_status_work'] }}
                </li>
            @endforeach
        </ul>
    @else
        <p>Không có công việc dự kiến.</p>
    @endif

    <h2>III. Kiến nghị</h2>
    <p>{{ $department['Request'] }}</p>
    @endforeach
   
</body>
</html>
