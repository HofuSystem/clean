<div class="modal fade" id="updateTotalProviderInvoiceModal" aria-hidden="true"
    aria-labelledby="updateTotalProviderInvoiceModalLabel">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="updateTotalProviderInvoiceModalLabel">
                    {{ trans('update total provider invoice') }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="modal-form update-total-provider-invoice-form"
                    action="{{ route('dashboard.orders.update-total-provider-invoice') }}">
                    <div class="row">
                        
                        <div class="form-group mb-3 col-md-12">
                            <label class="required" for="total_provider_invoice">{{ trans('total provider invoice') }}</label>
                            <input type="number" step="0.01" name="total_provider_invoice" class="form-control" id="total_provider_invoice" placeholder="{{ trans('total provider invoice') }}">

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
            $('#updateTotalProviderInvoiceModal form').submit(function(e) {
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
                        $('#updateTotalProviderInvoiceModal').modal('hide')
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

