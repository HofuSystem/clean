<div class="modal fade" id="updateDeliveryModal" aria-hidden="true"
    aria-labelledby="updateDeliveryModalLabel">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="updateDeliveryModalLabel">
                    {{ trans('update delivery price') }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="modal-form assign-operators-form"
                    action="{{ route('dashboard.orders.update-delivery-price') }}">
                    <div class="row">
                        
                        <div class="form-group mb-3 col-md-12">
                            <label class="required" for="price">{{ trans('price') }}</label>
                            <input type="number" name="price" class="form-control" id="price" placeholder="{{ trans('price') }}">

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
            $('#updateDeliveryModal form').submit(function(e) {
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
                        $('#updateDeliveryModal').modal('hide')
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
