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
        h1 {
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 10px;
        }
        h2 {
            font-size: 19px;
            font-weight: bold;
            margin-top: 30px;
            margin-bottom: 10px;
        }
        ul {
            list-style: none;
            padding-left: 0;
        }
        li {
            margin-bottom: 10px;
        }
        p {
            margin-bottom: 10px;
        }
        hr {
            border: 1px solid #ccc;
        }
    </style>
</head>
<body>
    <div class="container">
        @if(!empty($startDateOfWeekInput && !empty($endDateOfWeekInput)) )
            <h1>BÁO CÁO CÔNG VIỆC TUẦN ({{ $startDateOfWeekInput }} - {{ $endDateOfWeekInput }})</h1>
        @else
            <h1>BÁO CÁO CÔNG VIỆC TUẦN</h1>
        @endif

        <h1>{{ $departmentName }}</h1>

        <h2>I. Công việc đã thực hiện</h2>
        @if (!empty($data['WorkDone']))
            @if (count($data['WorkDone']) > 0)
                <ul>
                    @foreach ($data['WorkDone'] as $work)
                        <li>
                            <strong>Tiêu đề:</strong> {{ $work['work_done'] }}<br>
                            <strong>Nội dung:</strong> {{ $work['description'] }}<br>
                            <strong>Ngày bắt đầu:</strong> {{ $work['start_date'] }}<br>
                            <strong>Kết thúc:</strong> {{ $work['end_date'] }}<br>
                            <strong>Tiến độ:</strong> {{ $work['status_work'] }}
                        </li>
                    @endforeach
                </ul>
            @else
                <p>Không có công việc đã thực hiện.</p>
            @endif
        @else
            <p>Không có dữ liệu công việc đã thực hiện</p>
        @endif


        <h2>II. Công việc dự kiến</h2>
        @if (!empty($data['ExpectedWork']))
            @if (count($data['ExpectedWork']) > 0)
                <ul>
                    @foreach ($data['ExpectedWork'] as $work)
                        <li>
                            <strong>Tiêu đề:</strong> {{ $work['next_work'] }}<br>
                            <strong>Nội dung:</strong> {{ $work['next_description'] }}<br>
                            <strong>Ngày bắt đầu:</strong> {{ $work['next_start_date'] }}<br>
                            <strong>Kết thúc:</strong> {{ $work['next_end_date'] }}<br>
                            <strong>Tiến độ:</strong> {{ $work['next_status_work'] }}
                        </li>
                    @endforeach
                </ul>
            @else
                <p>Không có công việc dự kiến.</p>
            @endif
        @else
            <p>Không có dữ liệu công việc dự kiến</p>
        @endif

        <h2>III. Kiến nghị</h2>
        @if(!empty($data['Request']))
            <p>{{ $data['Request'] }}</p>
        @else
            <p>Không có kiến nghị nào.</p>
        @endif
        <hr>
    </div>
</body>
</html>