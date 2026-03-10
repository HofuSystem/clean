<div class="modal fade" id="assignOperatorsModal" aria-hidden="true"
    aria-labelledby="assignOperatorsModalLabel">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="assignOperatorsModalLabel">
                    {{ trans('Order Operator') }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="modal-form assign-operators-form"
                    action="{{ route('dashboard.orders.assign-operators') }}">
                    <div class="row">
                        
                        <div class="form-group mb-3 col-md-12">
                            <label class="required" for="operator_id">{{ trans('operator') }}</label>
                            <select class="custom-select  form-select advance-select" name="operator_id"
                                id="order_id-operator_id">
                                
                                <option value="">{{ trans('select operator') }}</option>
                                @foreach ($operators ?? [] as $sItem)
                                        <option data-roles="{{ $sItem->roles->pluck('name')->toJson() }}"
                                            value="{{ $sItem->id }}">{{ $sItem->fullname }}</option>
                                @endforeach

                            </select>

                        </div>

                        <input id="for" type="text" name="for" hidden>

                        <div class="col-lg-9 ml-lg-auto">
                            <button type="submit"
                                class="btn btn-primary font-weight-bold mr-2">{{ trans('save') }}</button>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
@push('js')
    <script>
        $(document).ready(function() {
            $('#assignOperatorsModal form').submit(function(e) {
                e.preventDefault();
                let form        = $(this);
                let url         = form.attr("action"); // Get the form action URL
                let formData    = form.serialize(); // Serialize form data
                $.ajax({
                    type: "POST",
                    url: url,
                    data: formData,
                    dataType: "json",
                    success: function(response) {
                        $('#assignOperatorsModal').modal('hide')
                        Swal.fire({
                            text: response.message,
                            icon: "success",
                            buttonsStyling: false,
                            confirmButtonText: "Ok",
                            customClass: {
                                confirmButton: "btn fw-bold btn-success",
                            }
                        }).then(function(result) {
                            location.reload();

                        })

                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        // Code to run if the request fails
                        response = JSON.parse(jqXHR.responseText);
                        Swal.fire({
                            text: response.message,
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "Ok",
                            customClass: {
                                confirmButton: "btn fw-bold btn-success",
                            }
                        })
                        $.each(response.errors, function(key, array) {
                            $.each(array, function(index, error) {
                                toastr.error(error, key);
                            });
                        });
                    }
                });

            });
        });
    </script>
@endpush
