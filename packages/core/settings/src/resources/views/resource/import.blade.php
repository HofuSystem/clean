
@extends('admin::layouts.dashboard')

@section('content')
    <!-- BEGIN: Content-->
    <div class="app-content content ">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper container-fluidp-0">
            <div class="content-header row">
                <div class="content-header-left col-md-9 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <h2 class="content-header-title float-start mb-2">{{$title}}</h2>
                            <div class="breadcrumb-wrapper">

                            </div>
                        </div>
                    </div>
                </div>
                <div class="content-header-right text-md-end col-md-3 col-12 d-md-block d-none">

                </div>
            </div>
            <div class="content-body">
                <!-- Basic Tables start -->
                <div class="row" >
                    <div class="col-12">
                        <div class="card">

                            <div class="card-body">
                                <div class="w3-col w3-half">
                                    <div class="form-group">
                                        <input  class="form-control" id="fileupload" type=file name="files[]">
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
                                                            <option value=""></option>
                                                            <option value="id">ID</option>
                                                            @foreach ($fields as $field)
                                                                <option value="{{ $field->key }}">{{ "$field->label ($field->key)" }}</option>
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

                                <button class="check-btn btn btn-primary">Check</button>
                                <button class="submit-btn btn btn-success">Submit</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Basic Tables end -->

            </div>
        </div>
    </div>
    <!-- END: Content-->

    <div class="sidenav-overlay"></div>
    <div class="drag-target"></div>


    <div class="modal fade" id="responseMessageModal" tabindex="-1" aria-labelledby="responseMessageModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="responseMessageModalLabel">Modal title</h5>
            </div>
            <div class="modal-body">
              ...
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
      </div>
@endsection
@push('css')
<style>
    	.removable-item {
		opacity: 0.3;
	}
</style>
@endpush

@push('js')
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.0/dist/xlsx.full.min.js"></script>

<script >

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
                    var XL_row_object = XLSX.utils.sheet_to_row_object_array(workbook.Sheets[sheetName]);
                    tFieldsList = JSON.parse(JSON.stringify(XL_row_object));
                    $("#import-data-co").html(" ");
                    $(".check-btn").show();
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
                        $.each(tFieldsList[0], function(colKey, firstRowV) {
                            //Copy template
                            var temp = $(".emp-template").clone();
                            //add index to template copy
                            $(temp).find(".import-item").attr("data-index", colKey);
                            //Add title to template copy
                            $(temp).find(".file-item .title").text(colKey);
                            //Add examples to template copy after title
                            var example = "";
                            example1 = (tFieldsList[0] && tFieldsList[0][colKey]) ? example + tFieldsList[0][colKey] + ", " : "";
                            example1 = example1.trimEllip(20, true);
                            example2 = (tFieldsList[1] && tFieldsList[1][colKey]) ? example + tFieldsList[1][colKey] : "";
                            example2 = example2.trimEllip(20, true);
                            example = example1 + example2;
                            $(temp).find(".file-item .example").text(example);
                            //If table name == backend item key | value
                            $.each(backendItems, function(kl, vl) {
                                if (vl.includes(firstRowV) || vl.includes(colKey) || colKey.includes('(' + vl[0] + ')')) {
                                    $(temp).find('select option').eq(kl).prop('selected', true).attr('selected', '1');
                                    return;
                                }
                            });
                            finalData = finalData + $(temp).html();
                        });
                        //Add items templates to page
                        $(finalData).appendTo("#import-data-co");
                    }
                });
            };
            reader.onerror = function(ex) {
            };
            reader.readAsBinaryString(file);
        };
    };
    $(".submit-btn, .check-btn").hide();

    function handleFileSelect(evt) {
        $(".submit-btn, .check-btn").hide();
        var files = evt.target.files; // FileList object
        var xl2json = new ExcelToJSON();
        xl2json.parseExcel(files[0]);
    }
    document.getElementById('fileupload').addEventListener('change', handleFileSelect, false);
    var finalDataToSend = {};
    $(".check-btn").on("click", function() {
        finalDataToSend = {};
        $("#import-data-co").find(".import-item").removeClass("removable-item");
        //$(".check-btn").hide();
        $("#import-data-co").find(".import-item").each(function() {
            var index = $(this).attr("data-index");
            var se = $(this).find("select").val();
            if (!se || finalDataToSend[se]) {
                $(this).addClass("removable-item")
                return;
            }
            finalDataToSend[se] = index;
            $(".check-btn").hide();
            $(".submit-btn").show();
        });
    });
    $(".submit-btn").on("click", function() {
        dataFillterd = {};
		$.each(tFieldsList, function (index, record) {
			dataItem = {};
			$.each(finalDataToSend, function (key, match) {
				dataItem[key] = record[match];
			});
			dataFillterd[index]= dataItem;
		});
        $.ajax({
        type: "POST",
        url: "{{ $url }}",
        data: dataFillterd,
        dataType: "json",
        success: function (response) {
            if(response.status){
                $('#responseMessageModal .modal-header').addClass('bg-success');
                $('#responseMessageModal .modal-title').addClass('text-white').text('Sucess');
                $('#responseMessageModal .modal-body').text(response.message);
                $('#responseMessageModal').modal('show')

            }else{
                $('#responseMessageModal .modal-header').addClass('bg-danger');
                $('#responseMessageModal .modal-title').addClass('text-white').text('Error');
                $('#responseMessageModal .modal-body').text(response.message);
                $('#responseMessageModal').modal('show');
                $($('[type=submit]')).addClass('btn-danger');
                $.each(response.errors, function (key, value) {
                    toastr.error(value,key,1000)

                });
            }
        },
        error: function(xhr, status, error){
            toastr.error(error);
            $($('[type=submit]')).addClass('btn-danger');
        }
    });

});
    $("#import-data-co").one("change", ".import-item select", function() {
        $(".submit-btn").hide();
        $(".check-btn").show();
    });
</script>

@endpush
