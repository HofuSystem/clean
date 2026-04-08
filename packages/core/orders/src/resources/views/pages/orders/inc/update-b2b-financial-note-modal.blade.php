<div class="modal fade" id="updateB2bFinancialNoteModal" aria-hidden="true"
    aria-labelledby="updateB2bFinancialNoteModalLabel">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h1 class="modal-title fs-5 text-white" id="updateB2bFinancialNoteModalLabel">
                    {{ trans('Update B2B Financial Note') }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="modal-form"
                    action="{{ route('dashboard.orders.update-b2b-financial-note') }}">
                    <div class="row">
                        
                        <div class="form-group mb-3 col-md-12">
                            <label class="required" for="b2b_financial_note">{{ trans('B2B Financial Note') }}</label>
                            <textarea name="b2b_financial_note" class="form-control" id="b2b_financial_note" rows="5" placeholder="{{ trans('B2B Financial Note') }}">{{ $order->b2b_financial_note }}</textarea>
                        </div>

                        <input id="for_note" type="text" name="for" hidden value="{{ json_encode([$order->id]) }}">

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
            $('#updateB2bFinancialNoteModal form').submit(function(e) {
                e.preventDefault();
                let form        = $(this);
                let url         = form.attr("action");
                let formData    = form.serialize();
                $.ajax({
                    type: "POST",
                    url: url,
                    data: formData,
                    dataType: "json",
                    success: function(response) {
                        $('#updateB2bFinancialNoteModal').modal('hide')
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
