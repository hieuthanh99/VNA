<!DOCTYPE html>
<html>
<head>
    <title>Báo cáo chi tiết</title>
    <style>
        body {
            font-family: 'SVN-Times New Roman 2.ttf', sans-serif;
        }
    </style>
</head>
<body>
    <h1>Báo cáo chi tiết</h1>
    <table>
        <tr>
            <th>ID</th>
            <th>Report ID</th>
            <th>Task Title</th>
            <th>Reports ID</th>
            <th>Status</th>
            <th>Created At</th>
            <th>Updated At</th>
        </tr>
        @foreach($data as $task)
        <tr>
            <td>{{ $task->id }}</td>
            <td>{{ $task->report_id }}</td>
            <td>{{ $task->title }}</td>
            <td>{{ $task->report_id }}</td>
            <td>{{ $task->status }}</td>
            <td>{{ $task->created_at }}</td>
            <td>{{ $task->updated_at }}</td>
        </tr>
        @endforeach
    </table>

</body>
</html>
