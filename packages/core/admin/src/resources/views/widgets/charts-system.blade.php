<div id="ecommerce-charts-statistics" class="charts-system charts-statistics">
</div>
<div id="recent-cutomers" class="charts-system charts-list">
</div>
<div id="recent-merchants" class="charts-system charts-list">
</div>
<div id="recent-painters" class="charts-system charts-list">
</div>
<div id="recent-orders-table" class="charts-system charts-table">
</div>
<div id="recent-stores-table" class="charts-system charts-table">
</div>
<div id="not-active-profiles" class="charts-system charts-list">
</div>
<div id="not-active-companies" class="charts-system charts-list">
</div>

<!-- Modal -->
<div class="modal fade" id="delete-recored" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="delete-recoredLabel">{{ trans('Remove recored') }}</h5>

                </button>
            </div>
            <div class="modal-body">
                <h5>{{ trans('Are you sure of deleteing this recored') }}</h5>
                <form action="" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">{{ trans('Delete') }}</button>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-default"
                    data-bs-dismiss="modal">{{ trans('Close') }}</button>
            </div>
        </div>
    </div>
</div>
@push('js')
    <!-- Page JS -->
    {{-- <script src="{{ asset('control') }}/assets/js/charts-apex.js"></script> --}}
    <script>
        $(document).ready(function() {
         
           
        });
    </script>
    </body>
@endpush
@push('css')
@endpush
