<div class="modal fade" id="cancelModal" aria-hidden="true"
    aria-labelledby="cancelModalLabel">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="cancelModalLabel">
                    {{ trans('cancel order') }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @if ($order->status == 'canceled')
                <div class="alert alert-danger" role="alert">
                    {{ trans("order is canceled") }} : {{ $order->admin_cancel_reason }}
            </div>
            @else
                <hr>
                <h6>{{ trans('Do you want to cancel order? why?!') }}</h6>

                <form class="update-status-form" action="{{ route('dashboard.orders.change-status',$order->id) }}" method="post" class="row">
                    @csrf
                    <input type="hidden" name="status" value="canceled">
                    <select class="custom-select my-2  form-select advance-select" name="admin_cancel_reason">
                        <option value="">{{ trans('select reason') }}</option>
                        @foreach (\Core\Orders\Services\OrdersService::issues() as $key =>  $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                    <button class="btn btn-danger" type="submit">{{ trans('cancel order') }}</button>
                </form>

            @endif
            </div>

        </div>
    </div>
</div>
@push('js')
    <script>
        $(document).ready(function() {

            $('#cancelModal form').submit(function(e) {
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
                        $('#cancelModal').modal('hide')
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
