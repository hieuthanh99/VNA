<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Báo cáo ' . $department->name) }}
        </h2>
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
            padding-left: 10px;
        }

        .custom-button:hover {
            background-color: #45a049;
        }

        .content-date {
            display: flex;
            margin: 10px 0;
        }

        .alert-success {
            background: #c4e8c0;
            padding: 10px;
        }

        .alert-danger {
            padding: 10px;
        }
    </style>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
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
                                            <span class="cong-viec-stt">{{ $rowCount++ }}</span>
                                            <input style="flex: 4" value="{{ $value }}" type="text"
                                                name="cong_viec_da_lam[]" placeholder="Tiêu đề công việc"
                                                class="form-control" required>
                                            <div class="form-check" style="margin-top: 0; flex: 2;">
                                                <input type="checkbox" checked name="cong_viec_da_lam_completed[]"
                                                    class="form-check-input" onchange="handleCongViecDaLamChange(this)">
                                                <input type="hidden" id="hiddenInput" name="cong_viec_da_lam_values[]"
                                                    value="1">
                                                <label class="form-check-label">Đã hoàn thành</label>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            <button type="button" class="btn btn-primary"
                                onclick="addNewRow('cong-viec-da-lam-container')">Thêm</button>
                        </div>
                        <hr>
                        <div class="mb-4">
                            <h1 class="mb-2 heading-style">II. Công việc dự kiến</h1>
                            <div id="cong-viec-tuan-toi-container"></div>
                            <button type="button" style="margin-top: 5px;" class="btn btn-primary"
                                onclick="validateAndAddCongViecTuanToi()">Thêm</button>
                        </div>
                        <hr>
                        <div class="mb-4">
                            <h1 class="mb-2 heading-style">III. Kiến nghị</h1>
                            <div>
                                <textarea style="width: 70%; height: 200px;" name="kien_nghi" placeholder="Nhập ý kiến" class="form-control"
                                    style="margin-bottom: 10px;"></textarea>
                            </div>

                        </div>
                        <hr>
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
                <span class="cong-viec-stt">${rowCount}.</span>
            <label  for="cong_viec_da_lam">Tiêu đề:</label> 
            <input style="flex: 4" type="text" name="cong_viec_da_lam[]" placeholder="Tiêu đề công việc" class="form-control" required>
            <div class="form-check" style="margin-top: 0; flex: 2;">
            <input type="checkbox" name="cong_viec_da_lam_completed[]" class="form-check-input" onchange="handleCongViecDaLamChange(this)">
            <input type="hidden" id="hiddenInput" name="cong_viec_da_lam_values[]">
            <label class="form-check-label">Đã hoàn thành</label>
            </div>
            </div>
            <div class="content-report form-group" >
                <label  for="noi_dung_cong_viec">Nội dung:</label> 
                <textarea required style="width: 67%; height: 200px;" name="noi_dung_cong_viec[]" placeholder="Nhập nội dung tiêu đề" class="form-control" style="margin-bottom: 10px;"></textarea>
                <div class="content-date">
                    <div >
                        <label for="ngay_sinh">Ngày bắt đầu:</label>
                        <input required type="date" name="start_date[]" id="start_date[]" class="form-control" value="{{ old('start_date[]') }}">
    
                    </div>
                    <div style="margin-left: 10px;">
                        <label for="ngay_sinh">Kết thúc:</label>
                        <input required type="date" name="end_date[]" id="end_date[]" class="form-control" value="{{ old('end_date[]') }}">
    
                    </div>
                </div>
              
            </div>
            <div  class="form-group style-note">
                    <label  for="trangthai_congviec">Tiến độ:</label> 
                    <input required style="flex:4" type="text"  name="trangthai_congviec[]" placeholder="Tiêu đề công việc" class="form-control" required>
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
                <span class="cong-viec-stt">${rowCount}.</span>
                <label  for="cong_viec_tuan_toi">Tiêu đề:</label> 
                <input readonly type="text" name="cong_viec_tuan_toi[]" style="flex:4" value="${row.querySelector('input[name="cong_viec_da_lam[]"]').value}" class="form-control custom-input" readonly> <span style="flex:2"></span>
            </div>
            <div class="content-report form-group" >
                <label  for="noi_dung_cong_viec">Nội dung:</label> 
                <textarea readonly style="width: 67%; height: 200px;" name="noi_dung_cong_viec_tuan_toi[]" placeholder="Nhập nội dung" class="form-control" style="margin-bottom: 10px;">${row.querySelector('textarea').value}</textarea>
                <div  class="content-date">
                    <div >
                        <label for="start_date_tuan_toi">Ngày bắt đầu:</label>
                        <input readonly type="date" name="start_date_tuan_toi[]" id="start_date_tuan_toi[]" value="${row.querySelector('input[name="start_date[]"]').value}" class="form-control">
    
                    </div>
                    <div style="margin-left: 10px;">
                        <label for="end_date_tuan_toi">Kết thúc:</label>
                        <input readonly type="date" name="end_date_tuan_toi[]" value="${row.querySelector('input[name="end_date[]"]').value}" id="end_date_tuan_toi[]" class="form-control">
    
                    </div>
                </div>
            
           
            </div>
            <div class="form-group style-note">
                    <label  for="trangthai_congviec">Tiến độ:</label> 
                    <input readonly style="flex:4" type="text" value="${row.querySelector('input[name="trangthai_congviec[]"]').value}"   name="trangthai_congviec_tuan_toi[]" placeholder="Tiêu đề công việc" class="form-control" required>
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
            <span class="cong-viec-stt">${rowCount}.</span>
            <label  for="cong_viec_tuan_toi">Tiêu đề:</label> 
            <input style="flex: 4" type="text" name="cong_viec_tuan_toi[]" class="form-control" required> 
            <button style="margin-left: 20px; flex: 5;" type="button" class="btn-delete" onclick="deleteCongViecTuanToi(this)">Xóa</button>
        </div>
        <div class="content-report form-group" >
            <label  for="noi_dung_cong_viec">Nội dung:</label> 
                <textarea required style="width: 67%; height: 200px;" name="noi_dung_cong_viec_tuan_toi[]" placeholder="Nhập nội dung" class="form-control" style="margin-bottom: 10px;"></textarea>
                <div class="content-date">
                    <div >
                        <label for="ngay_sinh">Ngày bắt đầu:</label>
                        <input required type="date" name="start_date_tuan_toi[]" id="start_date_tuan_toi[]" class="form-control" value="{{ old('start_date[]') }}">
    
                    </div>
                    <div style="margin-left: 10px;">
                        <label for="ngay_sinh">Kết thúc:</label>
                        <input required type="date" name="end_date_tuan_toi[]" id="end_date_tuan_toi[]" class="form-control" value="{{ old('end_date[]') }}">
    
                    </div>
                </div>
            </div>
         
        </div>
        <div class="form-group style-note">
                    <label  for="trangthai_congviec">Tiến độ:</label> 
                    <input required style="flex:4" type="text"  name="trangthai_congviec_tuan_toi[]" placeholder="Tiêu đề công việc" class="form-control" required>
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
            var startDate = startDate.value.trim() !== '';
            var endDate = endDate.value.trim() !== '';
            var descriptionWork = descriptionWork.value.trim() !== '';
            var workStatus = workStatus.value.trim() !== '';
            var hasText = workDone.value.trim() !== '';
            const errorSpan = document.getElementById('error');
            if (!hasText || !startDate || !descriptionWork || !endDate || !workStatus) {
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
                <span class="cong-viec-stt">${rowCount}.</span>
                <label  for="cong_viec_tuan_toi">Tiêu đề:</label> 
                <input readonly type="text" name="cong_viec_tuan_toi[]" style="flex:4" value="${row.querySelector('input[name="cong_viec_da_lam[]"]').value}" class="form-control custom-input" readonly> <span style="flex:2"></span>
            </div>
            <div class="content-report form-group" >
                <label  for="noi_dung_cong_viec">Nội dung:</label> 
                <textarea readonly style="width: 67%; height: 200px;" name="noi_dung_cong_viec_tuan_toi[]" placeholder="Nhập nội dung" class="form-control" style="margin-bottom: 10px;">${row.querySelector('textarea').value}</textarea>
                <div  class="content-date">
                    <div >
                        <label for="ngay_sinh">Ngày bắt đầu:</label>
                        <input readonly type="date" name="start_date_tuan_toi[]" id="start_date_tuan_toi[]" value="${row.querySelector('input[name="start_date[]"]').value}" class="form-control">
                    </div>
                    <div style="margin-left: 10px;">
                        <label for="ngay_sinh">Kết thúc:</label>
                        <input readonly type="date" name="end_date_tuan_toi[]" value="${row.querySelector('input[name="end_date[]"]').value}" id="end_date[]" class="form-control" >
    
                    </div>
                </div>
            </div>
            <div class="form-group style-note">
                    <label  for="trangthai_congviec">Tiến độ:</label> 
                    <input readonly style="flex:4" type="text" value="${row.querySelector('input[name="trangthai_congviec[]"]').value}"   name="trangthai_congviec_tuan_toi[]" placeholder="Tiêu đề công việc" class="form-control" required>
                    <div class="form-check" style="margin-top: 0; flex: 2;">
                </div>
            `;
                newCongViecTuanToiRow.setAttribute('data-row-id', row.getAttribute('data-row-id'));
                congViecTuanToiContainer.appendChild(newCongViecTuanToiRow);

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
    </script>
</x-app-layout>
