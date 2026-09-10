<div class="modal fade" id="mediaModal" tabindex="-1" aria-labelledby="mediaModalLabel" aria-hidden="true"
     dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}"
     data-trans-photos="{{ trans('Choose Or Drop Photos') }}"
     data-trans-files="{{ trans('Choose Or Drop Files') }}">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <div class="media-heading">
                    <span class="media-heading-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><rect x="3" y="3" width="18" height="18" rx="4"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m3 17 5-5 4 4 4-6 5 7"/></svg></span>
                    <div><h5 class="modal-title" id="mediaModalLabel">{{ trans('Media library') }}</h5><p>{{ trans('Upload new media or choose from your library') }}</p></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ trans('Close') }}"></button>
            </div>
            <div class="modal-body">
                <div class="image-container">
                    <span class="media-upload-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M7 17H6a4 4 0 0 1-.9-7.9A7 7 0 0 1 18.6 8a4.5 4.5 0 0 1-.1 9H17M12 21V11m-4 4 4-4 4 4"/></svg></span>
                    <input type="file" id="upload-button" multiple accept=".jpg,.jpeg,.png,.webp,.gif" />
                    <label id="upload-lable" for="upload-button">{{ trans('Choose Or Drop Photos') }}</label>
                    <p class="media-upload-hint">{{ trans('Uploaded media is selected automatically') }}<br>{{ trans('Up to 10 files, 5 MB each, 20 MB total. SVG is not supported.') }}</p>
                    <div id="image-display" aria-live="polite"></div>
                </div>
                <div id="error" role="alert"></div>
                <div id="media-status" role="status" aria-live="polite"></div>
                <section class="selected-gallery" aria-labelledby="media-selected-heading">
                    <div class="media-section-heading"><h6 id="media-selected-heading">{{ trans('Selected Media') }} <span class="media-count" id="media-selected-count">0</span></h6><span class="media-section-note">{{ trans('Ready to use') }}</span></div>
                    <div class="row"></div>
                    <p class="media-empty" id="media-selected-empty">{{ trans('Select media below or upload a new file') }}</p>
                </section>
                <section class="media-gallery" aria-labelledby="media-library-heading">
                    <div class="media-section-heading"><h6 id="media-library-heading">{{ trans('All system Media') }}</h6><span class="media-section-note">{{ trans('Click a thumbnail to select it') }}</span></div>
                    <div class="row"></div>
                    <p class="media-empty" id="media-library-empty" hidden>{{ trans('No media to display') }}</p>
                    <button type="button" id="load-more-media" class="btn">{{ trans('load more') }}</button>
                </section>
            </div>
            <div class="modal-footer">
                <span class="media-footer-count" aria-live="polite"></span>
                <button type="button" class="btn media-cancel" data-bs-dismiss="modal">{{ trans('Cancel') }}</button>
                <button type="button" class="btn btn-primary" id="mediaUploadBtn">{{ trans('Use selected media') }}</button>
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
