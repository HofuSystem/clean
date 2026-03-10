<div class="modal fade" id="changeStatusModal" aria-hidden="true"
    aria-labelledby="changeStatusModalLabel">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="changeStatusModalLabel">
                    {{ trans('Change status') }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="modal-form assign-representatives-form"
                    action="{{ route('dashboard.orders.change-status',$order->id) }}">
                    <div class="row">
                        <div class="col-12 mb-1">
                            <label for="status">@lang('status')</label>
                            <select class="custom-select filter-input form-select advance-select"
                                name="status" id="status">
                                <option value=""> @lang('select status')</option>
                                <option value="pending" @selected('pending' == $order->status)>{{ trans('pending') }}</option>
                                <option value="issue" @selected('issue' == $order->status)>{{ trans('issue') }}</option>
                                <option value="order_has_been_delivered_to_admin" @selected('order_has_been_delivered_to_admin' == $order->status)>{{ trans('order_has_been_delivered_to_admin') }}</option>
                                <option value="ready_to_delivered" @selected('ready_to_delivered' == $order->status)>{{ trans('ready_to_delivered') }}</option>
                                <option value="delivered" @selected('delivered' == $order->status)>{{ trans('delivered') }}</option>
                                <option value="finished" @selected('finished' == $order->status)>{{ trans('finished') }}</option>
                            </select>
                        </div>
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

            $('#changeStatusModal form').submit(function(e) {
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
                        $('#changeStatusModal').modal('hide')
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
