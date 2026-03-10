<!-- Footer -->
<footer class="content-footer footer bg-footer-theme">
  <div class="container-fluid mx-auto">
    <div
      class="footer-container d-flex align-items-center justify-content-between py-2 flex-md-row flex-column">
      <div>
        ©
        <script>
          document.write(new Date().getFullYear());
        </script>
        , {{ trans("Made by") }} <a target="_blank" href="https://cleanstation.app/">{{ config('app.name')}}</a>
      </div>

    </div>
  </div>
</footer>
<!-- / Footer -->

<!-- Modal -->
<div class="modal fade" id="messageModal" tabindex="-1" role="dialog" aria-labelledby="messageModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="messageModalLabel">{{ trans("System Message") }}</h5>
          <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            {{ session('message') }}

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ trans("Close") }}</button>
        </div>
      </div>
    </div>
  </div>
    <!-- END: Footer-->

@push('js')
 
<script>
    @if(session('message') !== null)
      $('#messageModal').modal('toggle')
    @endif
</script>
@endpush
