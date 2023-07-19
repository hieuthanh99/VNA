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
        .heading-style{
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
        display: flex;
        align-items: center;
        margin-bottom: 0.5rem;
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
                align-items: center;

            }
            .custom-button:hover {
            background-color: #45a049;
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
                    @php
                    $rowCount = 1;
                    @endphp
                
                    <form action="{{ route('reports.store') }}" method="POST" class="mt-4" id="report-form">
                        @csrf
                        <div class="mb-4">
                            <h1 class="mb-2 heading-style">I. Công việc đã thực hiện</h1>
                            <div id="cong-viec-da-lam-container">
                                @if($expectedWorkValues)
                                    @forEach($expectedWorkValues as $key => $value)
                                        <div class="form-group cong-viec-da-lam-row">
                                            <span class="cong-viec-stt">{{$rowCount++}}</span>
                                            <input style="flex: 4" value="{{$value}}" type="text" name="cong_viec_da_lam[]" placeholder="Tiêu đề công việc" class="form-control" required>
                                            <div class="form-check" style="margin-top: 0; flex: 2;">
                                            <input type="checkbox" checked name="cong_viec_da_lam_completed[]" class="form-check-input" onchange="handleCongViecDaLamChange(this)">
                                            <input type="hidden" id="hiddenInput" name="cong_viec_da_lam_values[]" value="1">
                                            <label class="form-check-label">Đã hoàn thành</label>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            <button type="button" class="btn btn-primary" onclick="addNewRow('cong-viec-da-lam-container')">Thêm</button>
                        </div>
                        <hr>
                        <div class="mb-4">
                            <h1 class="mb-2 heading-style">II. Công việc dự kiến</h1>
                            <div id="cong-viec-tuan-toi-container"></div>
                            <button type="button"  style="margin-top: 5px;" class="btn btn-primary" onclick="validateAndAddCongViecTuanToi()">Thêm</button>
                        </div>
                        <hr>
                        <div class="mb-4">
                            <h1 class="mb-2 heading-style">III. Kiến nghị</h1>
                            <div>
                                <textarea style="width: 70%; height: 200px;" name="kien_nghi" placeholder="Nhập ý kiến" class="form-control" style="margin-bottom: 10px;"></textarea>
                            </div>
                           
                        </div>
                        <hr>
                        <button @if(Session::get('cronJobCompleted')) disabled @endif type="submit" style="margin: 20px 0;" class="custom-button">Gửi báo cáo</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
    var rowCount = 1;
    function addNewRow(containerId) {

        var container = document.getElementById(containerId);
        var rowCount = container.getElementsByClassName('cong-viec-da-lam-row').length + 1;

        var newRow = document.createElement('div');
        newRow.className = 'form-group cong-viec-da-lam-row';
        newRow.innerHTML = `
            <span class="cong-viec-stt">${rowCount}.</span>
            <input style="flex: 4" type="text" name="cong_viec_da_lam[]" placeholder="Tiêu đề công việc" class="form-control" required>
            <div class="form-check" style="margin-top: 0; flex: 2;">
            <input type="checkbox" name="cong_viec_da_lam_completed[]" class="form-check-input" onchange="handleCongViecDaLamChange(this)">
            <input type="hidden" id="hiddenInput" name="cong_viec_da_lam_values[]">
            <label class="form-check-label">Đã hoàn thành</label>
            </div>
        `;
      
        container.appendChild(newRow);

        // Kiểm tra nếu checkbox không được chọn thì thêm dòng tương ứng vào công việc tuần tới
        var checkbox = newRow.querySelector('input[type="checkbox"]');
        var hiddenInput = newRow.querySelector('input[name="cong_viec_da_lam_values[]"]');
        checkbox.checked = true;
        if  (checkbox.checked)  hiddenInput.value = 1;
        if (!checkbox.checked) {
            hiddenInput.value = 0;
            var congViecTuanToiContainer = document.getElementById('cong-viec-tuan-toi-container');
            var newCongViecTuanToiRow = document.createElement('div');
            newCongViecTuanToiRow.className = 'form-group cong-viec-tuan-toi-row';
            newCongViecTuanToiRow.innerHTML = `
            <span class="cong-viec-stt">${rowCount}.</span>
            <input type="text" name="cong_viec_tuan_toi[]" style="flex: 4" value="${newRow.querySelector('input').value}" class="form-control custom-input" readonly>
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
        <span class="cong-viec-stt">${rowCount}.</span>
        <input style="flex: 4" type="text" name="cong_viec_tuan_toi[]" class="form-control" required>  <div style="margin-top: 0; flex: 2;">
        <button style="margin-left: 20px; type="button" class="btn-delete" onclick="deleteCongViecTuanToi(this)">Xóa</button>
        `;

    container.appendChild(newRow);
}
function deleteCongViecTuanToi(button) {
    var row = button.closest('.cong-viec-tuan-toi-row');
    row.remove();
    updateSTT();
}
function handleCongViecDaLamChange(checkbox) {
    var row = checkbox.parentNode.parentNode;
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
            <span class="cong-viec-stt">${rowCount}.</span>
            <input type="text" name="cong_viec_tuan_toi[]" style="flex:4" value="${row.querySelector('input').value}" class="form-control custom-input" readonly> <span style="flex:2"></span>
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
