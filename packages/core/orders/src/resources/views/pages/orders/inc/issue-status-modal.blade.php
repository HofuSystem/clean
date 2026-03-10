<div class="modal fade" id="issueStatusModal" aria-hidden="true" aria-labelledby="issueStatusModalLabel">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="issueStatusModalLabel">
                    {{ trans('issue status') }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <h6>{{ trans('A problem has occurred? why?!') }}</h6>

                <form class="update-status-form" action="{{ route('dashboard.orders.change-status', $order->id) }}"
                    method="post" class="row">
                    @csrf
                    <input type="hidden" name="status" value="issue">
                    <div class="form-group my-2">
                      <label for="notes">{{ trans('notes') }}</label>
                      <input type="text" name="notes" id="notes" class="form-control" placeholder="" aria-describedby="helpId">
                    </div>
                    <button class="btn btn-danger" type="submit">{{ trans('issue Status order') }}</button>
                </form>


            </div>

        </div>
    </div>
</div>
@push('js')
    <script>
        $(document).ready(function() {

            $('#issueStatusModal form').submit(function(e) {
                e.preventDefault();
                let form = $(this);
                let url = form.attr("action"); // Get the form action URL
                let formData = form.serialize(); // Serialize form data
                $.ajax({
                    type: "POST",
                    url: url,
                    data: formData,
                    dataType: "json",
                    success: function(response) {
                        $('#issueStatusModal').modal('hide')
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
