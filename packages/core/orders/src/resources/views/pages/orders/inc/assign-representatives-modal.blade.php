<div class="modal fade" id="assignRepresentativesModal" aria-hidden="true"
    aria-labelledby="assignRepresentativesModalLabel">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="assignRepresentativesModalLabel">
                    {{ trans('Order Representative') }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="modal-form assign-representatives-form"
                    action="{{ route('dashboard.orders.assign-representatives') }}">
                    <div class="row">
                        <div class="form-group mb-3 col-md-12">
                            <label class="" for="type">{{ trans('Driver Type') }}</label>
                            <select class="custom-select  form-select advance-select" name="type" id="order_id-type">

                                <option value="">{{ trans('select type') }}</option>
                                @if(isset($allowedRepresentatives))
                                    @foreach ($allowedRepresentatives as $item)
                                        <option value="{{ $item }}">
                                            {{ trans($item) }}</option>

                                    @endforeach
                                @else
                                    <option value="delivery">
                                        {{ trans('delivery') }}</option>
                                    <option value="technical">
                                        {{ trans('technical') }}</option>
                                    <option value="receiver">
                                        {{ trans('receiver') }}</option>
                                @endif

                            </select>

                        </div>
                        <div class="form-group mb-3 col-md-12">
                            <label class="required" for="representative_id">{{ trans('representative') }}</label>
                            <select class="custom-select  form-select advance-select" name="representative_id"
                                id="order_id-representative_id">

                                <option value="">{{ trans('select representative') }}</option>
                                @foreach ($users ?? [] as $sItem)
                                    <option data-roles="{{ $sItem->roles->pluck('name')->toJson() }}"
                                        value="{{ $sItem->id }}">{{ $sItem->fullname . " - " . $sItem->phone }}</option>
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
            var allUsers = [];
            $('#assignRepresentativesModal [name=representative_id] option').each(function(e) {
                let user = {};
                user.id = $(this).val();
                user.name = $(this).text();
                user.roles = $(this).data('roles');
                allUsers.push(user);

            });
            $('#assignRepresentativesModal [name=type]').change(function(e) {
                e.preventDefault();
                let type            = $(this).val();
                let selectedUser    = $('#assignRepresentativesModal [name=representative_id]').val()
                $('#assignRepresentativesModal [name=representative_id]').empty()

                $.each(allUsers, function(index, user) {
                    if (type == "delivery" && user.roles && user.roles.includes('driver')) {
                        $('#assignRepresentativesModal [name=representative_id]').append(new Option(
                            user.name, user.id));
                    } else if (type == "receiver" && user.roles && user.roles.includes('driver')) {
                        $('#assignRepresentativesModal [name=representative_id]').append(new Option(
                            user.name, user.id));
                    } else if (type == "technical" && user.roles && user.roles.includes(
                            'technical')) {
                        $('#assignRepresentativesModal [name=representative_id]').append(new Option(
                            user.name, user.id));
                    }
                });
                
                $('#assignRepresentativesModal [name=representative_id]').val(selectedUser).change()

            });
            $('#assignRepresentativesModal form').submit(function(e) {
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
                        $('#assignRepresentativesModal').modal('hide')
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
