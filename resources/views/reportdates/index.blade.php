<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Đặt ngày báo cáo') }}
        </h2>
        <style>
            .form-add-date {
                margin-top: 20px;
            }

            .form-date {
                padding: 20px;
                border: 1px solid #ccc;
                border-radius: 5px;
                width: 50%;
                margin: auto;
            }

            label {
                font-weight: bold;
            }

            input[type="date"] {
                padding: 8px;
                border: 1px solid #ccc;
                border-radius: 5px;
                width: 100%;
            }

            button[type="submit"] {
                margin-top: 10px;
                padding: 10px 20px;
                background-color: #007bff;
                color: #fff;
                border: none;
                border-radius: 5px;
                cursor: pointer;
            }

            .show-date {
                padding: 20px 0;
                border-radius: 5px;
                width: 50%;
                margin: auto;
                display: flex;
            }

            .show-hour {
                padding: 20px 0;
                border-radius: 5px;
                width: 50%;
                margin: auto;
                display: flex;
            }

            .alert-success {
                width: 50%;
                margin: auto;
            }

            .newest-date {
                margin-left: 30px;
            }
        </style>
    </x-slot>

    <div class="form-add-date">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        <form class="form-date" action="{{ route('report-dates.store') }}" method="post">
            @csrf
            <div class="form-group">
                <label for="report_date">Ngày báo cáo:</label>
                <input type="date" name="report_date" id="report_date" class="form-control">
            </div>
            <div class="form-group">
                <label for="report_time">Chọn giờ báo cáo:</label>
                <input class="form-control" type="time" id="report_time" name="report_time">
            </div>
            <button type="submit" class="btn btn-primary">Đặt ngày giờ báo cáo</button>
        </form>

        @if (isset($reportDates))
            <div class="show-date">
                <div class="date">Ngày báo cáo đã đặt:</div>
                <input style="width: 15%; padding: 4px;" type="date" class="newest-date" value="{{ $reportDates->report_date }}" disabled>
            </div>
            <div class="show-hour">
                <div class="date">Giờ báo cáo đã đặt:</div>
                <input style="width: 15%; padding: 4px;" type="time" class="newest-date" value="{{ $reportDates->report_time }}" disabled>
            </div>
        @endif
    </div>
</x-app-layout>
