@extends('admin::layouts.dashboard')

@section('content')
    <div class="progress-bar-container">
        {{-- <div class="progress-bar">
            <div class="progress"></div>
            <div class="bar">0%</div>
        </div> --}}
        <div class="container">
            <div class="table-responsive">
                <table class="table table-striped table-bordered custom-table" id="importing-table">

                    <thead class="table-primary">
                        <tr></tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- BEGIN: Content-->
    <div class="container-fluid flex-grow-1 container-p-y">

        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-start mb-2"> {{ $title }}</h2>

                    </div>
                </div>
            </div>
            <div class="content-header-right text-md-end col-md-3 col-12 d-md-block d-none">

            </div>
        </div>
        <div class="content-body">
            <!-- Basic Tables start -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <a href="{{ $backUrl }}" class="btn btn-secondary w-10 mb-2"><i class="fas fa-arrow-left"></i>{{ trans('back') }}</a>
                            <a href="{{ $exportUrl }}" class="btn btn-success w-25 mb-2">{{ trans('export template') }}</a>
                            <div class="w3-col w3-half">
                                <div class="form-group">
                                    <input class="form-control" id="fileupload" type=file name="files[]">
                                </div>
                            </div>
                            <div class="w3-responsive">

                                <div class="emp-template" style="display:none;">
                                    <div class="import-item card text-left">
                                        <img class="card-img-top" src="holder.js/100px180/" alt="">
                                        <div class="card-body">
                                            <div class=" card-body px-2">
                                                <div class="row">
                                                    <div class="file-item col-md-6">
                                                        <h3 class="title"></h3>
                                                        <p class="example"></p>
                                                    </div>
                                                    <div class="backend-field col-md-6">
                                                        <select class="form-control" name="">
                                                            <option value="selectOne" >selectOne</option>
                                                            @foreach ($cols as $key => $value)
                                                                <option value="{{ $key }}">{{ $value }}
                                                                </option>
                                                            @endforeach


                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>


                                </div>
                                <div id="import-data-co"></div>
                            </div>
                            <div class="mt-2">
                                <button class="check-btn btn btn-primary">{{ trans('Check') }}</button>
                                <button class="submit-btn btn btn-success">{{ trans('save') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Basic Tables end -->

        </div>

    </div>
    <!-- END: Content-->


    <div class="modal fade" id="responseMessageModal" tabindex="-1" aria-labelledby="responseMessageModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <script src="https://cdn.jsdelivr.net/gh/linways/table-to-excel@v1.0.4/dist/tableToExcel.js"></script>
                    <h5 class="modal-title" id="responseMessageModalLabel">{{ trans('Modal title') }}</h5>
                </div>
                <button id="btnExport" class="btn btn-primary m-1" onclick="exportReportToExcel(this)"><i
                        class="fa-solid fa-file-export"></i> {{ trans('Export') }}</button>
                <div class="modal-body">
                    ...
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ trans('Close') }}</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('css')
    <style>
        .import-item.card .card-body {
            padding: 5px !important;
        }

        .import-item.card .card-body h3 {
            font-size: 20px
        }

      

        @-webkit-keyframes importing {
            0% {
                left: 0;
                height: 30px;
                width: 15px;
            }

            50% {
                height: 8px;
                width: 40px;
            }

            100% {
                left: 235px;
                height: 30px;
                width: 15px;
            }
        }

        @keyframes importing {
            0% {
                left: 0;
                height: 30px;
                width: 15px;
            }

            50% {
                height: 8px;
                width: 40px;
            }

            100% {
                left: 235px;
                height: 30px;
                width: 15px;
            }
        }

        .progress-bar-container {
            position: fixed;
            top: 0;
            left: 0;
            background: #fff;
            display: none;
            width: 100%;
            height: 100%;
            z-index: 9999;
        }

        .progress-bar-container .progress-bar {
            position: relative;
            top: 50%;
            left: 50%;
            transform: translate(-50%);
            width: 30%;
            height: 30px;
            border-radius: 100px;
            padding: 5px 15px;
            overflow: initial;
            background-color: #f2f2f2;
            transition: ease-in-out .2s;
            box-shadow: 0 0 10px #ddd;
        }

        .progress-bar-container .progress-bar .bar {
            position: absolute;
            width: 5%;
            height: 20px;
            top: 50%;
            left: 0;
            transform: translateY(-50%);
            border-radius: 100px;
            margin: 0 7px;
            transition: ease-in-out .2s;
            background-image: linear-gradient(45deg, #0070ff, #00e7ff);
        }

        .progress-bar-container .progress-bar .progress {
            top: -25px;
            color: #000;
            z-index: 9999999999999999999;
            left: 50%;
            transform: translate(-50%);
            position: absolute;
            padding: 15px;
            font-size: 14px;
        }

        table.errors {
            width: 100%;
        }

        table.errors td,
        table.errors th {
            border: solid #ddd 1px;
            padding: 2px 10px;
        }

        /* Make the table responsive */
        .table-responsive {
            max-width: 100%;
            overflow-x: auto;
        }

        /* Table custom styling */
        .custom-table th,
        .custom-table td {
            max-width: 150px;
            /* Set maximum width for cells */
            white-space: nowrap;
            /* Prevent line breaks */
            overflow: hidden;
            text-overflow: ellipsis;
            /* Add ellipsis for overflowed text */
        }

        /* Sticky header */
        .custom-table thead th {
            position: sticky;
            top: 0;
            background-color: #f8f9fa;
            /* Match your Bootstrap theme */
            z-index: 1;
            /* Keep header above other content */
        }

        /* Optional: Add padding to improve readability */
        .custom-table th,
        .custom-table td {
            padding: 8px 12px;
            vertical-align: middle;
        }
    </style>

    <style>
        .removable-item {
            opacity: 0.3;
        }
    </style>
@endpush

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.0/dist/xlsx.full.min.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/linways/table-to-excel@v1.0.4/dist/tableToExcel.js"></script>
    <script>
        $(".submit-btn, .check-btn").addClass('d-none');
       
        Array.prototype.unique = function() {
            var arr = this.concat();

            for (var i = 0; i < arr.length; ++i) {
                for (var j = i + 1; j < arr.length; ++j) {
                    if (arr[i] === arr[j])
                        arr.splice(j--, 1);
                }
            }

            return arr;
        }; // end of unique

        String.prototype.trimEllip = function(maxLength, trimToWord) {
            var tryStr, me = this;
            if (me.length > maxLength) {
                me = me.substring(0, maxLength).trim();
                if (trimToWord) {
                    var i = me.lastIndexOf(" "),
                        j = me.lastIndexOf("\n");
                    if (i < j) i = j;
                    if ((i > 0) && (tryStr = me.substring(0, i).trim()))
                        me = tryStr; // only if tryStr is not empty
                }
                me += "…"; // 1 symbol. Originally was "&hellip;";, but sometimes we need console output.
            }
            return me;
        }

        function getAllAvailableRowsIndex(arr) {
            let allKeys = [];

            for (let i = 0; i < arr.length; i++) {
                allKeys = allKeys.concat(Object.keys(arr[i]));
            }

            allKeys = allKeys.unique()

            for (i = 0; i < allKeys.length; i++) {
                if (!arr[0][allKeys[i]]) {
                    arr[0][allKeys[i]] = null;
                }
            }

            return arr;
        } // end of getAllAvailableRowsIndex

        var tFieldsList;
        var ExcelToJSON = function() {
            this.parseExcel = function(file) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    var data = e.target.result;
                    var workbook = XLSX.read(data, {
                        type: 'binary'
                    });

                    workbook.SheetNames.forEach(function(sheetName) {

                        var XL_row_object = XLSX.utils.sheet_to_json(workbook.Sheets[sheetName], {
                            defval: "",
                            raw: false
                        });
                        tFieldsList = JSON.parse(JSON.stringify(XL_row_object));
                        $("#import-data-co").html(" ");
                        $(".check-btn").removeClass('d-none');
                        //prepare backend items list
                        var backendItems = [];
                        $(".emp-template").find(".backend-field select option").each(function() {
                            var v = $(this).attr("value");
                            var t = $(this).text();
                            backendItems.push([v, t]);
                        });
                        //Create items from excel file columns
                        if (tFieldsList && tFieldsList[0]) {
                            var finalData = "";
                            //Loop at the top of the table
                            tFieldsList = getAllAvailableRowsIndex(tFieldsList)

                            $.each(tFieldsList[0], function(colKey, firstRowV) {
                                //Copy template
                                var temp = $(".emp-template").clone();
                                //add index to template copy
                                $(temp).find(".import-item").attr("data-index", colKey);
                                //Add title to template copy
                                $(temp).find(".file-item .title").text(colKey);
                                //Add examples to template copy after title
                                var example = "";
                                example1 = (tFieldsList[0] && tFieldsList[0][colKey]) ?
                                    example + tFieldsList[0][colKey] + ", " : "";
                                example1 = example1.trimEllip(20, true);
                                example2 = (tFieldsList[1] && tFieldsList[1][colKey]) ?
                                    example + tFieldsList[1][colKey] : "";
                                example2 = example2.trimEllip(20, true);
                                example = example1 + example2;
                                $(temp).find(".file-item .example").text(example);
                                //If table name == backend item key | value
                                $.each(backendItems, function(kl, vl) {
                                    $(temp).find('select option').each(function() {
                                        let text    = $(this).text().trim();
                                        let val     = $(this).val().trim();
                                        let matches =  colKey.includes(text) || colKey.includes(val);
                               
                                        if (matches) {
                                            $(this).prop('selected', true).attr(
                                                'selected', 'selected');
                                            return false; // Exit the loop once the option is found
                                        }
                                    });
                                    return;
                                });
                                finalData = finalData + $(temp).html();
                            });
                            //Add items templates to page
                            $("#import-data-co").html(finalData);
                        }
                    });
                };
                reader.onerror = function(ex) {
                };
                reader.readAsBinaryString(file);
            };
        };

        function handleFileSelect(evt) {
            $(".submit-btn, .check-btn").addClass('d-none');
            var files = evt.target.files; // FileList object
            var xl2json = new ExcelToJSON();
            xl2json.parseExcel(files[0]);
        }

        function htmlToElement(html) {
            var template = document.createElement('template');
            html = html.trim(); // Never return a text node of whitespace as the result
            template.innerHTML = html;
            return template.content.firstChild;
        }

        function isSimpleValue(value) {
            // Check if the value is a simple type (not an object or array)
            return (
                typeof value === 'number' ||
                value === null
            );
        }

        function isSimpleArray(jsonString) {
            try {
                // Parse the JSON string
                const parsed = JSON.parse(jsonString);

                // Check if the parsed result is an array
                if (!Array.isArray(parsed)) {
                    return false;
                }

                // Validate that all elements are simple values
                for (const item of parsed) {
                    if (!isSimpleValue(item)) {
                        return false;
                    }
                }

                // If validation passes, return true
                return true;
            } catch (error) {
                // If JSON parsing fails, it's not a valid JSON string
                return false;
            }
        }

        function upgradeArrayOfObjects(arrayOfObjects) {
            // Loop through each object in the array
            for (const obj of arrayOfObjects) {
                // Loop through each key-value pair in the object
                for (const key in obj) {
                    if (obj.hasOwnProperty(key)) {
                        const value = obj[key];

                        // Check if the value is a string and is a JSON array of simple values
                        if (typeof value === 'string' && isSimpleArray(value)) {
                            // Convert the JSON string to an array
                            obj[key] = JSON.parse(value);
                        }
                    }
                }
            }

            return arrayOfObjects;
        }
        function formatObject(input) {
            const result = {};

            for (const key in input) {
                if (input.hasOwnProperty(key)) {
                    // Split the key by dots to handle nested structure
                    const keys = key.split('.');
                    let current = result;

                    // Traverse the keys to build the nested structure
                    for (let i = 0; i < keys.length; i++) {
                        const part = keys[i];

                        // If this is the last part, assign the value
                        if (i === keys.length - 1) {
                            current[part] = input[key];
                        } else {
                            // Otherwise, create nested objects if they don't exist
                            if (!current[part]) {
                                current[part] = {};
                            }
                            current = current[part];
                        }
                    }
                }
            }

            return result;
        }
        function formatArrayOfObjects(inputArray) {
            // Map over each object in the array and format it
            return inputArray.map(obj => formatObject(obj));
        }
        function importing(data, index = 0, increase = 10) {
            var rows = Object.values(data);
            rows = upgradeArrayOfObjects(rows);
            rows = formatArrayOfObjects(rows);

            drawUploadinTable(rows)
            let requestData = {};
            requestData.data = rows;

            $.ajax({
                type: "POST",
                url: "{{ $url }}",
                data: requestData,
                dataType: "json",
                headers: {
                    "Accept": "application/json"
                },
                success: function(response) {
                    toastr.success(response.message)
                },
                error: function(xhr, status, error) {
                    toastr.error(error);
                    var responseJson = JSON.parse(xhr.responseText);
                    var errors = responseJson.errors;
                    var message = responseJson.message;
                    toastr.error(message);
                    $.each(errors, function(index, record) {
                        let number = index.match(/\.(\d+)\./)[1];
                        number = parseInt(number);
                        $.each(record, function(key, value) {
                            setTimeout(() => {
                                index = "row " + (number + 1) + " " + index.replace(
                                    'data.' + number + '.', '');
                                index = index.replace('data.' + number + '.', '');
                                toastr.error(value, index);
                            }, number * 400);
                        });
                    });



                }

            });

        } // end of importing


        function drawUploadinTable(data) {
            $('#importing-table tbody').html("");

            $.each(data, function(index, record) {
                let tr = $('<tr>');
                dataItem = {};
                $.each(finalDataToSend, function(key, match) {
                    tr.append(`<td>${record[match]}</td>`)
                });
                $('#importing-table tbody').append(tr);

            });

        }

        document.getElementById('fileupload').addEventListener('change', handleFileSelect, false);
        var finalDataToSend = {};
        $(".check-btn").on("click", function() {
            finalDataToSend = {};
            $("#import-data-co").find(".import-item").removeClass("removable-item");
            //$(".check-btn").addClass('d-none');
            $("#import-data-co").find(".import-item").each(function() {
                var index = $(this).attr("data-index");
                var se = $(this).find("select").val();
                if (!se || finalDataToSend[se]) {
                    $(this).addClass("removable-item")
                    return;
                }
                finalDataToSend[se] = index;
                $(".check-btn").addClass('d-none');
                $(".submit-btn").removeClass('d-none');
            });
        });

        $(".submit-btn").on("click", function() {
            if (tFieldsList.length) {
                let firstLoop = true;
                $('#importing-table thead tr').html("");
                $('#importing-table tbody').html("");
                let dataFillterd = {};
                let itemFillterd = {};

                $.each(tFieldsList, function(index, record) {
                    itemFillterd = {};
                    $.each(finalDataToSend, function(key, match) {
                        if (firstLoop && key != "__EMPTY") {
                            $('#importing-table thead tr').append(`<th> ${key} </th>`);
                        }
                        itemFillterd[key] = record[match];

                    });
                    firstLoop = false;
                    dataFillterd[index] = itemFillterd;
                });

                localStorage.setItem("importErrors", "");
                localStorage.setItem("importMessage", "");
                importing(dataFillterd);

            }
        });

        $("#import-data-co").on("change", ".import-item select", function() {
            $(".submit-btn").addClass('d-none');
            $(".check-btn").removeClass('d-none');
        });

        window.exportReportToExcel = function() {

            let table = $(
                "table.errors"
            ); // you can use document.getElementById('tableId') as well by providing id to the table tag

            TableToExcel.convert(table[
                0], { // html code may contain multiple tables so here we are refering to 1st table tag
                name: `errors.xlsx`, // fileName you could use any name
                sheet: {
                    name: 'Sheet 1' // sheetName
                }
            });

        }
    </script>
@endpush
