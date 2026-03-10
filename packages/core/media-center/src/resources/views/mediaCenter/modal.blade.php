
    <!-- Modal -->
    <div class="modal fade" id="mediaModal" tabindex="-1" aria-labelledby="mediaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="mediaModalLabel">Upload Media</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="image-container">
                        <input type="file" id="upload-button" multiple accept="image/*" />
                        <label id="upload-lable" for="upload-button"><i class="text-white fa fa-file-upload"></i> {{ trans('Choose Or Drop Photos') }}
                        </label>
                        <div id="error"></div>
                        <div id="image-display"></div>
                    </div>
                    <div class="selected-gallery">
                        <h5>{{ trans('Selected Media') }}</h5>
                        <div class="row p-3">
               
                          <!-- Add more columns for more images... -->
                        </div>
                    </div>
                    <div class="media-gallery">
                        <h5>{{ trans('All system Media') }}</h5>

                        <div class="row p-3">
               
                          <!-- Add more columns for more images... -->
                        </div>
                        <button id="load-more-media" class="btn btn-success mt-3">{{ trans('load more') }}...</button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="mediaUploadBtn">Save</button>
                </div>
            </div>
        </div>
    </div>
    
@push('css')
    <link rel="stylesheet" href="{{ asset('control') }}/core-assets/media-center/style.css">
@endpush

@push('js')
    <script>
      var media_center_links      = {};
      media_center_links.url     = "{{ url('storage') }}" 
      media_center_links.list     = "{{ route('dashboard.media-center.list') }}" 
      media_center_links.add_new  = "{{ route('dashboard.media-center.add-new') }}" 
      media_center_links.delete   = "{{ route('dashboard.media-center.delete') }}" 
    </script>
    <script src="{{ asset('control') }}/core-assets/media-center/script.js"></script>
@endpush
