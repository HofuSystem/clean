   <!-- Basic modal -->
   <div class="modal fade text-left" id="notifyModal" role="dialog" aria-labelledby="myModalLabel120" aria-hidden="true">
       <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
           <div class="modal-content">
               <div class="modal-header bg-primary white p-2">
                   <h5 class="modal-title text-white" id="myModalLabel120">
                       {{ trans('Notify Modal') }}</h5>
                   <button class="btn btn-danger" type="button" class="close" data-bs-dismiss="modal"
                       aria-bs-label="Close">
                       <span aria-hidden="true">&times;</span>
                   </button>
               </div>
               <div class="card row">
                   <div class="card-body row">
                       <div class="form-group mb-3 col-md-12">
                           <label class="required" for="types">{{ trans('types') }}</label>
                           <select class="custom-select  form-select advance-select" name="types" id="types"
                               multiple>

                               <option value="">{{ trans('select types') }}</option>
                               <option value="apps">{{ trans('apps') }}</option>
                               <option value="email">{{ trans('email') }}</option>
                               <option value="sms">{{ trans('sms') }}</option>
                               <option value="whats_app">{{ trans('whatsapp') }}</option>

                           </select>

                       </div>

                       <div class="form-group mb-3 col-md-12">
                           <label class="required" for="title">{{ trans('title') }}</label>
                           <input type="text" name="title" class="form-control "
                               placeholder="{{ trans('Enter title') }} " value="{{ config('app.name') }}">

                       </div>

                       <div class="form-group mb-3 col-md-12">
                           <label class="required" for="body">{{ trans('body') }}</label>
                           <textarea type="number" name="body" class="form-control " placeholder="{{ trans('Enter body') }} "></textarea>

                       </div>

                       <div class="form-group mb-3 col-md-12">
                           <label class="" for="media">{{ trans('media') }}</label>
                           <div class="media-center-group form-control" data-max="5" data-type="gallery">
                               <input type="text" hidden="hidden" class="form-control" name="media"
                                   value="{{ old('media', $item->media ?? null) }}">
                               <button type="button" class="btn btn-secondary media-center-load"
                                   style="margin-top: 10px;"><i class="fa fa-file-upload"></i></button>
                               <div class="input-gallery"></div>
                           </div>
                       </div>

                       <input hidden type="text" name="for" class="form-control">
                       <input hidden type="text" name="for_data" class="form-control">

                   </div>
                   <div class="card-footer">
                       <div class="row">
                           <div class="col-lg-9 ml-lg-auto">
                               <button id="notifyBtn" data-url="{{ route('dashboard.notifications.create') }}"
                                   type="submit" class="btn btn-primary font-weight-bold mr-2"><i
                                       class="fas fa-paper-plane"></i> {{ trans('send') }}</button>
                           </div>
                       </div>
                   </div>
               </div>
           </div>
       </div>
   </div>
   @push('js')
       <script>
           $('#notifyBtn').click(function(e) {
               e.preventDefault();
               let url  = $(this).data('url');
               let data = {};
               $('#notifyModal input,#notifyModal select,#notifyModal textarea').each(function (index, element) {
                 let name = $(this).attr('name');
                 let val = $(this).val();
                 data[name] = val;
                
               });
               $.ajax({
                   type: "POST",
                   url: url,
                   data: data,
                   dataType: "json",
                   beforeSend: function() {
                       // Code to run before the request is sent
                       $('.loader-rm-wrapper').removeClass('hide-loader')
                   },
                   success: function(response) {
                       $('#notifyModal').modal('hide')
                       Swal.fire({
                           text: response.message,
                           icon: "success",
                           buttonsStyling: false,
                           confirmButtonText: "Ok",
                           customClass: {
                               confirmButton: "btn fw-bold btn-success",
                           }
                       });
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
                   },
                   complete: function() {
                       $('.loader-rm-wrapper').addClass('hide-loader')
                   }
               });
           });
       </script>
   @endpush
