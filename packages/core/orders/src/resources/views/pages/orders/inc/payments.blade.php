
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">{{ trans('Payment Transactions') }}</h5>
            @can('dashboard.order-transactions.create')
                <button type="button" class="btn btn-sm btn-primary" onclick="openTransactionModal()">
                    <i class="fas fa-plus-circle"></i>
                    <span>{{ trans('create new') }}</span>
                </button>
            @endcan
        </div>
        <div class="table-responsive p-2">
            <table class="table table-bordered table-striped table-hover text-center" id="transactions-table">
                <thead class="table-primary">
                    <tr>
                        <th>{{ trans('id') }}</th>
                        <th>{{ trans('Payment Method') }}</th>
                        <th>{{ trans('Transaction ID') }}</th>
                        <th>{{ trans('Amount (SAR)') }}</th>
                        <th>{{ trans('Notes') }}</th>
                        <th>{{ trans('Date & Time') }}</th>
                        <th>{{ trans('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->transactions as $transaction)
                        <tr>
                            <td data-id="{{ $transaction->id }}">{{ $transaction->id }}</td>
                            <td data-payment_method="{{ $transaction->type }}" data-online_payment_method="{{ $transaction->online_payment_method }}">{{ $transaction->online_payment_method ?? trans($transaction->type) }}</td>
                            <td data-transaction_id="{{ $transaction->transaction_id }}">{{ $transaction->transaction_id }}</td>
                            <td data-amount="{{ $transaction->amount }}">{{ number_format($transaction->amount, 2) }}</td>
                            <td data-notes="{{ $transaction->notes }}"  >{{ $transaction->notes }}</td>
                            <td>{{ $transaction->created_at->format('d-m-Y h:i A') }}</td>
                            <td>
                                <div class="d-flex justify-content-center gap-1">

                                    @can('dashboard.order-transactions.edit')
                                        <button type="button"
                                           class="btn btn-sm btn-warning"
                                           title="{{ trans('edit') }}"
                                           onclick="editTransaction({{ $transaction->id }})">
                                            <i class="fa fa-edit"></i>
                                        </button>
                                    @endcan
                                    @can('dashboard.order-transactions.delete')
                                        <button type="button"
                                           class="btn btn-sm btn-danger"
                                           title="{{ trans('delete') }}"
                                           onclick="deleteTransaction({{ $transaction->id }})">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Transaction Modal -->
<div class="modal fade" id="transactionModal" tabindex="-1" aria-labelledby="transactionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="transactionModalLabel">{{ trans('create new') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="transactionForm">
                @csrf
                <input type="hidden" id="transaction_id" name="transaction_id">
                <input type="hidden" name="order_id" value="{{ $order->id }}">
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group mb-3 col-md-12">
                            <label class="required" for="type">{{ trans('type') }}</label>
                            <select class="form-control form-select" name="type" id="type" required>
                                <option value="">{{ trans('select type') }}</option>
                                <option value="cash">{{ trans('cash') }}</option>
                                <option value="card">{{ trans('card') }}</option>
                                <option value="wallet">{{ trans('wallet') }}</option>
                            </select>
                        </div>

                        <div class="form-group mb-3 col-md-12">
                            <label for="online_payment_method">{{ trans('online payment method') }}</label>
                            <input type="text" name="online_payment_method" id="online_payment_method" class="form-control"
                                placeholder="{{ trans('Enter online payment method') }}">
                        </div>

                        <div class="form-group mb-3 col-md-12">
                            <label class="required" for="amount">{{ trans('amount') }}</label>
                            <input type="number" step="0.01" name="amount" id="amount" class="form-control"
                                placeholder="{{ trans('Enter amount') }}" required>
                        </div>

                        <div class="form-group mb-3 col-md-12">
                            <label for="transaction_id_field">{{ trans('transaction id') }}</label>
                            <input type="text" name="transaction_id" id="transaction_id_field" class="form-control"
                                placeholder="{{ trans('Enter transaction id') }}">
                        </div>

                        <div class="form-group mb-3 col-md-12">
                            <label for="notes">{{ trans('notes') }}</label>
                            <textarea name="notes" id="notes" class="form-control" rows="3"
                                placeholder="{{ trans('Enter notes') }}"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ trans('close') }}</button>
                    <button type="submit" class="btn btn-primary">{{ trans('save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('js')
<script>
// Open modal for creating new transaction
function openTransactionModal() {
    document.getElementById('transactionModalLabel').textContent = '{{ trans("create new") }}';
    document.getElementById('transactionForm').reset();
    document.getElementById('transaction_id').value = '';
    document.getElementById('transactionForm').setAttribute('data-mode', 'create');

    var modal = new bootstrap.Modal(document.getElementById('transactionModal'));
    modal.show();
}

// Open modal for editing transaction
function editTransaction(id) {
    document.getElementById('transactionModalLabel').textContent = '{{ trans("edit") }}';
    document.getElementById('transactionForm').setAttribute('data-mode', 'edit');
    document.getElementById('transaction_id').value = id;

    // Get transaction data from the table row data attributes
    const row = document.querySelector(`td[data-id="${id}"]`).closest('tr');

    // Get all the data attributes from the row
    const typeCell = row.querySelector('td[data-payment_method]');
    const paymentMethod = typeCell.getAttribute('data-payment_method');
    const onlinePaymentMethod = typeCell.getAttribute('data-online_payment_method');

    const transactionIdCell = row.querySelector('td[data-transaction_id]');
    const transactionIdValue = transactionIdCell.getAttribute('data-transaction_id');

    const amountCell = row.querySelector('td[data-amount]');
    const amountValue = amountCell.getAttribute('data-amount');

    const notesCell = row.querySelector('td[data-notes]');
    const notesValue = notesCell.getAttribute('data-notes');

    // Fill form fields
    document.getElementById('type').value = paymentMethod || '';
    document.getElementById('online_payment_method').value = onlinePaymentMethod || '';
    document.getElementById('amount').value = amountValue || '';
    document.getElementById('transaction_id_field').value = transactionIdValue || '';
    document.getElementById('notes').value = notesValue || '';

    // Show modal
    var modal = new bootstrap.Modal(document.getElementById('transactionModal'));
    modal.show();
}

// Handle form submission
document.getElementById('transactionForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const mode = this.getAttribute('data-mode');
    const transactionId = document.getElementById('transaction_id').value;
    const formData = new FormData(this);

    let url, method;
    if (mode === 'edit' && transactionId) {
        url = `/admin/order-transactions/${transactionId}/edit`;
        method = 'PUT';
        formData.append('_method', 'PUT');
    } else {
        url = '/admin/order-transactions/create';
        method = 'POST';
    }

    // Show loading
    Swal.fire({
        title: '{{ trans("Please wait") }}...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    fetch(url, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status) {
            Swal.fire({
                icon: 'success',
                title: '{{ trans("Success") }}',
                text: data.message || '{{ trans("Transaction saved successfully") }}',
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                // Close modal
                var modal = bootstrap.Modal.getInstance(document.getElementById('transactionModal'));
                modal.hide();

                // Reload page
                location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: '{{ trans("Error") }}',
                text: data.message || '{{ trans("An error occurred") }}'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: '{{ trans("Error") }}',
            text: '{{ trans("An error occurred while saving") }}'
        });
    });
});

// Delete transaction with SweetAlert
function deleteTransaction(id) {
    Swal.fire({
        title: '{{ trans("Are you sure?") }}',
        text: '{{ trans("You will not be able to recover this transaction!") }}',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: '{{ trans("Yes, delete it!") }}',
        cancelButtonText: '{{ trans("Cancel") }}'
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: '{{ trans("Deleting") }}...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('_method', 'DELETE');

            fetch(`/admin/order-transactions/${id}/delete`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.status) {
                    Swal.fire({
                        icon: 'success',
                        title: '{{ trans("Deleted!") }}',
                        text: data.message || '{{ trans("Transaction has been deleted") }}',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        // Reload page
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: '{{ trans("Error") }}',
                        text: data.message || '{{ trans("Could not delete transaction") }}'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: '{{ trans("Error") }}',
                    text: '{{ trans("An error occurred while deleting") }}'
                });
            });
        }
    });
}
</script>
@endpush

