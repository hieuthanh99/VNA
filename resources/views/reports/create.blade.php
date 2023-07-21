<x-app-layout>
    <x-slot name="header">
        <!-- <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Báo cáo ' . $department->name) }}
        </h2> -->
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
            padding-top: 25px;
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
            padding-left: 25px;
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
            padding-left: 25px;
            padding-bottom: 20px;
        }

        .custom-button:hover {
            background-color: #45a049;
        }

        .content-date {
            display: flex;
            margin: 10px 0;
            padding-top:15px;
            padding-bottom: 15px;
        }

        .alert-success {
            background: #c4e8c0;
            padding: 10px;
        }

        .alert-danger {
            /* background: #f1c9c9; */
            padding: 10px;
        }

        .report-title {
            position: relative;
            background-image: linear-gradient(195deg,#006885 0%,#006885 100%);
            padding: 16px;
            color: #fff;
            border-radius: 0.5rem;
            margin: 0 19%;
            margin-bottom: -13px;
        }

        header{
            display: none;
        }

        .notification {
            padding: 0;
            border: none;
        }

        .alert-danger {
            padding: 0;
            position: relative;
            border-radius: 0.2rem;
            margin-bottom: -45px;
            margin-top: 17px;
            margin-left: 2%;
            margin-right: 2%;
            padding-left: 15px;
        }

        #report-form {
            margin-top: 0;
            background-color: rgb(243 244 246 / var(--tw-bg-opacity));
        }

        .mb-4 {
            background: #fff none repeat scroll 0 0;
            border: 1px solid #ede9e9;
            border-radius: 15px;
            display: inline-block;
            width: 100%;
            margin-bottom: 20px;
            padding: 20px;
        }

        .item-job {
            font-weight: 500;
        }
    </style>
    <div class="py-12">
        <h2 class="report-title">
            {{ __('Báo cáo ' . $department->name) }}
        </h2>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg" style="box-shadow:none;">
                <div class="notification p-6 bg-white border-b border-gray-200">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    <div id="error" class="alert alert-danger">
                    </div>
                    @php
                        $rowCount = 1;
                    @endphp

                    <form action="{{ route('reports.store') }}" method="POST" class="mt-4" id="report-form">
                        @csrf
                        <div class="mb-4">
                            <h1 class="mb-2 heading-style">I. Công việc đã thực hiện</h1>
                            <div id="cong-viec-da-lam-container">
                                @if ($expectedWorkValues)
                                    @foreach ($expectedWorkValues as $key => $value)
                                        <div class="form-group cong-viec-da-lam-row">
                                            <div class="header-report form-group">
                                                <span class="cong-viec-stt">{{$rowCount++}}. </span>
                                            <label class="item-job"  style="padding-left: 15px;" for="cong_viec_da_lam">Tiêu đề:</label> 
                                            <input  value="{{$value->next_work}}" style="flex: 4;margin-left: 46px" type="text" name="cong_viec_da_lam[]" placeholder="Tiêu đề công việc" class="form-control" required>
                                            <div class="form-check" style="margin-top: 0; flex: 2;">
                                            <input type="checkbox" checked name="cong_viec_da_lam_completed[]" class="form-check-input" onchange="handleCongViecDaLamChange(this)">
                                            <input type="hidden" id="hiddenInput" name="cong_viec_da_lam_values[]" value="1">
                                            <label class="form-check-label">Đã hoàn thành</label>
                                            </div>
                                            </div>
                                            <div class="content-report form-group" >
                                                <label class="item-job" style="vertical-align: top;"  for="noi_dung_cong_viec">Nội dung:</label> 
                                                <textarea required style="width: 62%; height: 200px; margin-left:30px;" name="noi_dung_cong_viec[]" placeholder="Nhập nội dung tiêu đề" class="form-control" style="margin-bottom: 10px;">{{$value->next_description}}</textarea>
                                                <div class="content-date">
                                                    <div >
                                                        <label class="item-job" for="ngay_sinh">Ngày bắt đầu:</label>
                                                        <input required type="date" name="start_date[]" id="start_date" class="form-control" value="{{$value->next_start_date}}">
                                    
                                                    </div>
                                                    <div style="margin-left: 50px;">
                                                        <label class="item-job" for="ngay_sinh">Kết thúc:</label>
                                                        <input required type="date" name="end_date[]" id="end_date" class="form-control" value="{{$value->next_end_date}}">
                                    
                                                    </div>
                                                </div>
                                              
                                            </div>
                                            <div  class="form-group style-note">
                                                    <label class="item-job" for="trangthai_congviec">Tiến độ:</label> 
                                                    <input value="{{$value->next_status_work}}" style="flex:4; margin-left: 46px" type="text"  name="trangthai_congviec[]" placeholder="Tiêu đề công việc" class="form-control" required>
                                                    <div class="form-check" style="margin-top: 0; flex: 2;">
                                                </div>
                                        </div>
                                    </div>
                                    @endforeach
                                @endif
                            </div>
                            <button type="button" class="btn btn-primary"
                                onclick="addNewRow('cong-viec-da-lam-container')">Thêm</button>
                        </div>
                        <hr style="border: none;">
                        <div class="mb-4">
                            <h1 class="mb-2 heading-style">II. Công việc dự kiến</h1>
                            <div id="cong-viec-tuan-toi-container"></div>
                            <button type="button" style="margin-top: 5px;" class="btn btn-primary"
                                onclick="validateAndAddCongViecTuanToi()">Thêm</button>
                        </div>
                        <hr style="border: none;">
                        <div class="mb-4">
                            <h1 class="mb-2 heading-style">III. Kiến nghị</h1>
                            <div>
                                <textarea style="width: 70%; height: 200px; margin-left:30px;" name="kien_nghi" placeholder="Nhập ý kiến" class="form-control"
                                    style="margin-bottom: 10px;"></textarea>
                            </div>

                        </div>
                        <hr style="border: none;">
                        <button @if (Session::get('cronJobCompleted')) disabled @endif type="submit" style="margin: 20px 0;"
                            class="custom-button">Gửi báo cáo</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
       
        var rowCount = 1;
        const errorSpan = document.getElementById('error');

        function hideErrorMessage() {
            // Xóa nội dung thông báo và đặt màu nền thành mặc định
            console.log(errorSpan);
            const error = errorSpan.querySelector('span');
            error.remove();
            errorSpan.style.backgroundColor = 'transparent';
        }

        function addNewRow(containerId) {

            var container = document.getElementById(containerId);
            var rowCount = container.getElementsByClassName('cong-viec-da-lam-row').length + 1;

            var newRow = document.createElement('div');
            newRow.className = 'form-group cong-viec-da-lam-row';
            newRow.innerHTML = `
            <div class="header-report form-group">
                <span class="item-job" class="cong-viec-stt">${rowCount}.</span>
            <label class="item-job" style="padding-left: 15px;" for="cong_viec_da_lam">Tiêu đề:</label> 
            <input style="flex: 4; margin-left: 46px" type="text" name="cong_viec_da_lam[]" placeholder="Tiêu đề công việc" class="form-control" required>
            <div class="form-check" style="margin-top: 0; flex: 2;">
            <input type="checkbox" name="cong_viec_da_lam_completed[]" class="form-check-input" value="1" onchange="handleCongViecDaLamChange(this)">
            <input  type="hidden" id="hiddenInput" name="cong_viec_da_lam_values[]" value="1">
            <label class="form-check-label">Đã hoàn thành</label>
            <button style="margin-left: 20px; flex: 5;" type="button" class="btn-delete" onclick="deleteCongViecDaLam(this)">Xóa</button>
            </div>
            </div>
            <div class="content-report form-group" >
                <label class="item-job" style="vertical-align: top;"  for="noi_dung_cong_viec">Nội dung:</label> 
                <textarea required style="width: 62%; height: 200px; margin-left:30px;" name="noi_dung_cong_viec[]" placeholder="Nhập nội dung tiêu đề" class="form-control" style="margin-bottom: 10px;"></textarea>
                <div class="content-date">
                    <div >
                        <label class="item-job" for="ngay_sinh">Ngày bắt đầu:</label>
                        <input required type="date" name="start_date[]" id="start_date" class="form-control" value="{{ old('start_date[]') }}">
    
                    </div>
                    <div style="margin-left: 50px;">
                        <label class="item-job" for="ngay_sinh">Kết thúc:</label>
                        <input required type="date" name="end_date[]" id="end_date" class="form-control" value="{{ old('end_date[]') }}">
    
                    </div>
                </div>
              
            </div>
            <div  class="form-group style-note">
                    <label class="item-job" for="trangthai_congviec">Tiến độ:</label> 
                    <input required style="flex:4; margin-left: 46px" type="text"  name="trangthai_congviec[]" placeholder="Tiêu đề công việc" class="form-control" required>
                    <div class="form-check" style="margin-top: 0; flex: 2;">
                </div>
        `;

            container.appendChild(newRow);

            // Kiểm tra nếu checkbox không được chọn thì thêm dòng tương ứng vào công việc tuần tới
            var checkbox = newRow.querySelector('input[type="checkbox"]');
            var hiddenInput = newRow.querySelector('input[name="cong_viec_da_lam_values[]"]');
            checkbox.checked = true;
            if (checkbox.checked) hiddenInput.value = 1;
            if (!checkbox.checked) {
                hiddenInput.value = 0;
                var congViecTuanToiContainer = document.getElementById('cong-viec-tuan-toi-container');
                var newCongViecTuanToiRow = document.createElement('div');
                newCongViecTuanToiRow.className = 'form-group cong-viec-tuan-toi-row';
                newCongViecTuanToiRow.innerHTML = `
                <div class="header-report form-group">
                <span class="item-job" class="cong-viec-stt">${rowCount}.</span>
                <label class="item-job" style="padding-left: 15px;" for="cong_viec_tuan_toi">Tiêu đề:</label> 
                <input readonly type="text" name="cong_viec_tuan_toi[]" style="flex:4" value="${row.querySelector('input[name="cong_viec_da_lam[]"]').value}" class="form-control custom-input" readonly> <span style="flex:2"></span>
            </div>
            <div class="content-report form-group" >
                <label class="item-job" style="vertical-align: top;"  for="noi_dung_cong_viec">Nội dung:</label> 
                <textarea readonly style="width: 55%; height: 200px; margin-left:30px;" name="noi_dung_cong_viec_tuan_toi[]" placeholder="Nhập nội dung" class="form-control" style="margin-bottom: 10px;">${row.querySelector('textarea').value}</textarea>
                <div  class="content-date">
                    <div >
                        <label class="item-job" for="start_date_tuan_toi">Ngày bắt đầu:</label>
                        <input readonly type="date" name="start_date_tuan_toi" id="start_date_tuan_toi[]" value="${row.querySelector('input[name="start_date[]"]').value}" class="form-control">
    
                    </div>
                    <div style="margin-left: 50px;">
                        <label class="item-job" for="end_date_tuan_toi">Kết thúc:</label>
                        <input readonly type="date" name="end_date_tuan_toi" value="${row.querySelector('input[name="end_date[]"]').value}" id="end_date_tuan_toi[]" class="form-control">
    
                    </div>
                </div>
            
           
            </div>
            <div class="form-group style-note">
                    <label class="item-job" for="trangthai_congviec">Tiến độ:</label> 
                    <input readonly style="flex:4; margin-left: 46px" type="text" value="${row.querySelector('input[name="trangthai_congviec[]"]').value}"   name="trangthai_congviec_tuan_toi[]" placeholder="Tiêu đề công việc" class="form-control" required>
                    <div class="form-check" style="margin-top: 0; flex: 2;">
                </div>
            `;
                newCongViecTuanToiRow.setAttribute('data-row-id', rowCount);
                congViecTuanToiContainer.appendChild(newCongViecTuanToiRow);
            }
        }

        function validateAndAddCongViecTuanToi() {
            var input = document.querySelector('#cong-viec-tuan-toi-container input[name="cong_viec_tuan_toi[]"]');
            var container = document.getElementById('cong-viec-tuan-toi-container');
            var rowCount = container.querySelectorAll('.cong-viec-tuan-toi-row').length;

            if (rowCount === 0 || (rowCount > 0 && input.value.trim() !== '')) {
                addCongViecTuanToi();
            } else {
                alert('Vui lòng nhập giá trị trước khi thêm công việc tuần tới.');
            }
        }

        function addCongViecTuanToi() {
            var container = document.getElementById('cong-viec-tuan-toi-container');
            var rowCount = container.getElementsByClassName('cong-viec-tuan-toi-row').length + 1;

            var newRow = document.createElement('div');
            newRow.className = 'form-group  cong-viec-tuan-toi-row';
            newRow.innerHTML = `
        <div class="header-report form-group">
            <span class="item-job" class="cong-viec-stt">${rowCount}.</span>
            <label class="item-job" style="padding-left: 15px;" for="cong_viec_tuan_toi">Tiêu đề:</label> 
            <input style="flex: 4; margin-left: 46px" type="text" name="cong_viec_tuan_toi[]" placeholder="Tiêu đề công việc" class="form-control" required> 
            <button style="margin-left: 20px; flex: 5;" type="button" class="btn-delete" onclick="deleteCongViecTuanToi(this)">Xóa</button>
        </div>
        <div class="content-report form-group" >
            <label class="item-job" style="vertical-align: top;"  for="noi_dung_cong_viec">Nội dung:</label> 
                <textarea required style="width: 55%; height: 200px; margin-left:30px;" name="noi_dung_cong_viec_tuan_toi[]" placeholder="Nhập nội dung" class="form-control" style="margin-bottom: 10px;"></textarea>
                <div class="content-date">
                    <div >
                        <label class="item-job" for="ngay_sinh">Ngày bắt đầu:</label>
                        <input required type="date" name="start_date_tuan_toi[]" id="start_date_tuan_toi[]" class="form-control" value="{{ old('start_date[]') }}">
    
                    </div>
                    <div style="margin-left: 50px;">
                        <label class="item-job" for="ngay_sinh">Kết thúc:</label>
                        <input required type="date" name="end_date_tuan_toi[]" id="end_date_tuan_toi[]" class="form-control" value="{{ old('end_date[]') }}">
    
                    </div>
                </div>
            </div>
         
        </div>
        <div class="form-group style-note">
                    <label class="item-job" for="trangthai_congviec">Tiến độ:</label> 
                    <input required style="flex:4; margin-left: 46px" type="text"  name="trangthai_congviec_tuan_toi[]" placeholder="Tiêu đề công việc" class="form-control" required>
                    <div class="form-check" style="margin-top: 0; flex: 2;">
                </div>
        `;

            container.appendChild(newRow);
        }

        function deleteCongViecTuanToi(button) {
            var row = button.closest('.cong-viec-tuan-toi-row');
            row.remove();
            updateSTT();
        }
        function deleteCongViecDaLam(button) {
            var row = button.closest('.cong-viec-da-lam-row');
            var checkbox = row.querySelector('input[name="cong_viec_da_lam_completed[]"]');
            checkbox.click();
            // console.log(checkbox);
            // handleCongViecDaLamChange(checkbox);
            row.remove();
            updateSTTDaLam();
        }

        function handleCongViecDaLamChange(checkbox) {
            var row = checkbox.parentNode.parentNode.parentNode;
            
            //var row2 = checkbox.parentNode.parentNode.parentNode;
            var workDone = row.querySelector('input[name="cong_viec_da_lam[]"]');
            var startDate = row.querySelector('input[name="start_date[]"]');
            var endDate = row.querySelector('input[name="end_date[]"]');
            var workStatus = row.querySelector('input[name="trangthai_congviec[]"]');
            var descriptionWork = row.querySelector('textarea');
            // var workDone = row.querySelector('input[name="cong_viec_da_lam[]"]');

            // Check if the workDone has text
            var startDateValue = startDate.value.trim() !== '';
            var endDateValue = endDate.value.trim() !== '';
            var descriptionWorkValue = descriptionWork.value.trim() !== '';
            var workStatusValue = workStatus.value.trim() !== '';
            var hasText = workDone.value.trim() !== '';
            const errorSpan = document.getElementById('error');
            if (!hasText || !startDateValue || !descriptionWorkValue || !endDateValue || !workStatusValue) {
                const errorMessage = 'Vui lòng nhập dữ liệu.';
                errorSpan.style.backgroundColor = '#f1c9c9';
                errorSpan.innerHTML = '<span>' + errorMessage + '</span>';
                checkbox.checked = true;
                const delayTime = 800;
                setTimeout(hideErrorMessage, delayTime);
                return;
            }
            //  console.log("hasText");
            var congViecTuanToiContainer = document.getElementById('cong-viec-tuan-toi-container');
            var congViecTuanToiRows = congViecTuanToiContainer.getElementsByClassName('cong-viec-tuan-toi-row');
            var hiddenInput = row.querySelector('input[name="cong_viec_da_lam_values[]"]');
            if (!checkbox.checked) {
                var rowCount = congViecTuanToiRows.length + 1;
                checkbox.checked = false;
                hiddenInput.value = 0;
                var newCongViecTuanToiRow = document.createElement('div');
                newCongViecTuanToiRow.className = 'form-group cong-viec-tuan-toi-row';

                newCongViecTuanToiRow.innerHTML = `
            <div class="header-report form-group">
                <span class="item-job" class="cong-viec-stt">${rowCount}.</span>
                <label class="item-job" style="padding-left: 15px;" for="cong_viec_tuan_toi">Tiêu đề:</label> 
                <input type="text" name="cong_viec_tuan_toi[]" style="flex:4;" value="${row.querySelector('input[name="cong_viec_da_lam[]"]').value}" class="form-control custom-input"> <span style="flex:2"></span>
            </div>
            <div class="content-report form-group" >
                <label class="item-job" style="vertical-align: top;"  for="noi_dung_cong_viec">Nội dung:</label> 
                <textarea style="width: 55%; height: 200px; margin-left:30px;" name="noi_dung_cong_viec_tuan_toi[]" placeholder="Nhập nội dung" class="form-control" style="margin-bottom: 10px;">${row.querySelector('textarea').value}</textarea>
                <div  class="content-date">
                    <div >
                        <label class="item-job" for="ngay_sinh">Ngày bắt đầu:</label>
                        <input type="date" name="start_date_tuan_toi[]" id="start_date_tuan_toi[]" value="${row.querySelector('input[name="start_date[]"]').value}" class="form-control">
                    </div>
                    <div style="margin-left: 50px;">
                        <label class="item-job" for="ngay_sinh">Kết thúc:</label>
                        <input type="date" name="end_date_tuan_toi[]" value="${row.querySelector('input[name="end_date[]"]').value}" id="end_date[]" class="form-control" >
    
                    </div>
                </div>
            </div>
            <div class="form-group style-note">
                    <label class="item-job" for="trangthai_congviec">Tiến độ:</label> 
                    <input style="flex:4; margin-left: 46px" type="text" value="${row.querySelector('input[name="trangthai_congviec[]"]').value}"   name="trangthai_congviec_tuan_toi[]" placeholder="Tiêu đề công việc" class="form-control" required>
                    <div class="form-check" style="margin-top: 0; flex: 2;">
                </div>
            `;
                newCongViecTuanToiRow.setAttribute('data-row-id', row.getAttribute('data-row-id'));
                congViecTuanToiContainer.appendChild(newCongViecTuanToiRow);
                // if (workDone && startDate && endDate && workStatus && descriptionWork) {
                //     // Disabled các trường input và textarea
                //     workDone.disabled = true;
                //     startDate.disabled = true;
                //     endDate.disabled = true;
                //     workStatus.disabled = true;
                //     descriptionWork.disabled = true;
                // } else {
                //     console.error('Không tìm thấy các trường input và textarea.');
                // }
                // Cập nhật lại số thứ tự của các dòng còn lại
                for (var i = 0; i < congViecTuanToiRows.length; i++) {
                    var sttSpan = congViecTuanToiRows[i].querySelector('.cong-viec-stt');
                    sttSpan.innerText = i + 1 + '.';
                }
            } else {
                checkbox.checked = true;
                hiddenInput.value = 1;
                var rowId = row.getAttribute('data-row-id');
                var congViecTuanToiRow = congViecTuanToiContainer.querySelector(`[data-row-id="${rowId}"]`);
                if (congViecTuanToiRow) {
                    congViecTuanToiContainer.removeChild(congViecTuanToiRow);

                    // Cập nhật lại số thứ tự của các dòng còn lại
                    for (var i = 0; i < congViecTuanToiRows.length; i++) {
                        var sttSpan = congViecTuanToiRows[i].querySelector('.cong-viec-stt');
                        sttSpan.innerText = i + 1 + '.';
                    }
                }
            }
        }

        function updateSTT() {
            var rows = document.querySelectorAll('.cong-viec-tuan-toi-row');
            rows.forEach(function(row, index) {
                var sttElement = row.querySelector('.cong-viec-stt');
                sttElement.textContent = (index + 1) + '.';
            });
        }
        function updateSTTDaLam() {
            var rows = document.querySelectorAll('.cong-viec-da-lam-row');
            rows.forEach(function(row, index) {
                var sttElement = row.querySelector('.cong-viec-stt');
                sttElement.textContent = (index + 1) + '.';
            });
        }
        
    </script>
</x-app-layout>
