@extends('layouts.client')

@section('content')
    <!-- Main Content -->
    <main class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="page-title">{{ trans('client.customer_overprices') }}</h1>
                    <p class="page-subtitle">
                        {{ trans('client.manage_customer_prices_for') }} {{ $contract->title }}
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPriceModal">
                        <i class="fas fa-plus"></i> {{ trans('client.add_new_overprice') }}
                    </button>
                    <a href="{{ route('client.contracts.contract') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> {{ trans('client.back_to_contracts') }}
                    </a>
                </div>
            </div>
        </div>

        <!-- Add Price Modal -->
        <div class="modal fade" id="addPriceModal" tabindex="-1" aria-labelledby="addPriceModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="addPriceModalLabel">
                            <i class="fas fa-plus-circle me-2"></i>{{ trans('client.add_new_overprice') }}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <!-- Filters -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label small text-muted">{{ trans('client.product_name') }}</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
                                    <input type="text" id="modal_product_search" class="form-control border-start-0 bg-light" placeholder="{{ trans('client.search_placeholder') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small text-muted">{{ trans('client.category') }}</label>
                                <select id="modal_category_filter" class="form-select form-select-sm bg-light">
                                    <option value="">{{ trans('client.all_categories') }}</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small text-muted">{{ trans('client.sub_category') }}</label>
                                <select id="modal_subcategory_filter" class="form-select form-select-sm bg-light">
                                    <option value="" data-parent-id="">{{ trans('client.all_sub_categories') }}</option>
                                    @foreach($subCategories as $subCategory)
                                        <option value="{{ $subCategory->id }}" data-parent-id="{{ $subCategory->parent_id }}">{{ $subCategory->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Results List -->
                        <div class="modal-results-container" style="max-height: 400px; overflow-y: auto;">
                            <div id="modal-search-results" class="list-group list-group-flush">
                                <div class="text-center py-5 text-muted">
                                    <i class="fas fa-box-open fa-3x mb-3 opacity-25"></i>
                                    <p>{{ trans('client.modal_results_help') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Price Management Table -->
        <div class="card">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <h3 class="card-title mb-0">{{ trans('client.manage_prices') }}</h3>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="prices-table">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">{{ trans('client.product_name') }}</th>
                                <th class="text-center">{{ trans('client.clean_station_price') }}</th>
                                <th class="text-center">{{ trans('client.over_price') }}</th>
                                <th class="text-center">{{ trans('client.final_price') }}</th>
                                <th class="text-end pe-4">{{ trans('client.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody id="prices-tbody">
                            @foreach($contract->contractCustomerPrices as $customerPrice)
                                @php
                                    $contractPrice = $contract->contractPrices->where('product_id', $customerPrice->product_id)->first();
                                    $basePrice = $contractPrice ? $contractPrice->price : 0;
                                @endphp
                                <tr id="price-row-{{ $customerPrice->id }}" data-product-id="{{ $customerPrice->product_id }}">
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="product-icon me-3 bg-light rounded p-2">
                                                <i class="fas fa-box text-primary"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $customerPrice->product->name }}</div>
                                                <small class="text-muted">
                                                    {{ $customerPrice->product->category?->name }} >
                                                    {{ $customerPrice->product->subCategory?->name }}
                                                </small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="base-price fw-medium">{{ $basePrice }}</span>
                                        <small class="text-muted">{{ trans('client.SAR') }}</small>
                                    </td>
                                    <td class="text-center">
                                        <div class="input-group input-group-sm mx-auto" style="width: 130px;">
                                            <input type="number" step="0.01"
                                                class="form-control over-price-input text-center"
                                                value="{{ $customerPrice->over_price }}"
                                                data-id="{{ $customerPrice->id }}" data-base="{{ $basePrice }}"
                                                data-url="{{ route('client.contracts.customer-prices.update', $customerPrice->id) }}">
                                            <span class="input-group-text bg-light border-start-0 small" style="font-size: 0.7rem;">{{ trans('client.SAR') }}</span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span
                                            class="final-price fw-bold text-primary">{{ $basePrice + $customerPrice->over_price }}</span>
                                        <small class="text-primary fw-bold">{{ trans('client.SAR') }}</small>
                                    </td>
                                    <td class="text-end pe-4">
                                        <button type="button" class="btn btn-outline-danger btn-sm rounded-circle delete-btn"
                                            data-id="{{ $customerPrice->id }}"
                                            data-url="{{ route('client.contracts.customer-prices.delete', $customerPrice->id) }}">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                            @if($contract->contractCustomerPrices->isEmpty())
                                <tr id="no-prices-row">
                                    <td colspan="5" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="fas fa-search fa-3x mb-3 opacity-25"></i>
                                            <p>{{ __('Search and add products to manage their overprices') }}</p>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <style>
        .modal-results-container::-webkit-scrollbar {
            width: 6px;
        }
        .modal-results-container::-webkit-scrollbar-thumb {
            background-color: #e0e0e0;
            border-radius: 10px;
        }
        .search-result-modal-item {
            padding: 15px;
            border-left: 4px solid transparent;
            transition: all 0.2s;
        }
        .search-result-modal-item:hover {
            background-color: #f8fbff;
            border-left-color: #4876b2;
        }
        .over-price-input:focus {
            border-color: #4876b2;
            box-shadow: 0 0 0 0.2rem rgba(72, 118, 178, 0.25);
        }
        .table thead th {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #6c757d;
        }
        .product-icon {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .btn-xs {
            padding: 0.2rem 0.4rem;
            font-size: 0.75rem;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modalSearchInput = document.getElementById('modal_product_search');
            const modalCategoryFilter = document.getElementById('modal_category_filter');
            const modalSubcategoryFilter = document.getElementById('modal_subcategory_filter');
            const modalResults = document.getElementById('modal-search-results');
            const tbody = document.getElementById('prices-tbody');
            const noPricesRow = document.getElementById('no-prices-row');

            // Handle Modal Search and Filters
            let debounceTimer;
            const handleFilterChange = (isCategoryChange = false) => {
                const query = modalSearchInput.value.trim();
                const categoryId = modalCategoryFilter.value;
                const subCategoryId = modalSubcategoryFilter.value;

                // Filter sub-category dropdown if category changed
                if (isCategoryChange) {
                    Array.from(modalSubcategoryFilter.options).forEach(option => {
                        const parentId = option.dataset.parentId;
                        if (!categoryId || !parentId || parentId == categoryId) {
                            option.hidden = false;
                        } else {
                            option.hidden = true;
                        }
                    });
                    
                    // Reset sub-category if current selection is now hidden
                    const selectedOption = modalSubcategoryFilter.options[modalSubcategoryFilter.selectedIndex];
                    if (selectedOption && selectedOption.hidden) {
                        modalSubcategoryFilter.value = '';
                    }
                }

                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    fetch(`{{ route('client.contracts.customer-prices.search') }}?q=${query}&category_id=${categoryId}&sub_category_id=${modalSubcategoryFilter.value}`)
                        .then(response => response.json())
                        .then(data => {
                            displayModalResults(data);
                        });
                }, 300);
            };

            modalSearchInput.addEventListener('input', () => handleFilterChange());
            modalCategoryFilter.addEventListener('change', () => handleFilterChange(true));
            modalSubcategoryFilter.addEventListener('change', () => handleFilterChange());

            function displayModalResults(products) {
                modalResults.innerHTML = '';
                if (products.length === 0) {
                    modalResults.innerHTML = `<div class="text-center py-5 text-muted"><p>${trans('no_products_found')}</p></div>`;
                } else {
                    products.forEach(product => {
                        const isAdded = !!document.querySelector(`tr[data-product-id="${product.id}"]`);
                        
                        const item = document.createElement('div');
                        item.className = 'list-group-item search-result-modal-item mb-2 rounded border shadow-sm';
                        item.innerHTML = `
                            <div class="row align-items-center">
                                <div class="col-md-5">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-light rounded p-2 me-3">
                                            <i class="fas fa-box text-primary"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">${product.name}</div>
                                            <small class="text-muted d-block">${product.category} > ${product.sub_category}</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 text-center">
                                    <div class="small text-muted mb-1">${trans('base_price')}</div>
                                    <div class="fw-bold text-primary">${product.clean_station_price} ${trans('SAR')}</div>
                                </div>
                                <div class="col-md-4">
                                    ${isAdded ? 
                                        `<div class="text-end text-success small fw-bold">
                                            <i class="fas fa-check-circle me-1"></i>${trans('already_in_list')}
                                        </div>` : 
                                        `<div class="d-flex gap-2">
                                            <div class="input-group input-group-sm">
                                                <input type="number" step="0.01" class="form-control modal-over-price-input" 
                                                       placeholder="${trans('over_price')}" value="0">
                                            </div>
                                            <button class="btn btn-sm btn-primary add-to-table-btn whitespace-nowrap" style="min-width: 80px;">
                                                <i class="fas fa-plus me-1"></i>${trans('add')}
                                            </button>
                                        </div>`
                                    }
                                </div>
                            </div>
                        `;
                        
                        if (!isAdded) {
                            const addBtn = item.querySelector('.add-to-table-btn');
                            const overPriceInput = item.querySelector('.modal-over-price-input');
                            addBtn.addEventListener('click', () => {
                                const overPrice = parseFloat(overPriceInput.value) || 0;
                                addProductToTable(product, overPrice, item);
                            });
                        }
                        
                        modalResults.appendChild(item);
                    });
                }
            }

            function addProductToTable(product, overPrice, modalItem) {
                saveNewCustomerPrice(product.id, overPrice, (newId) => {
                    product.customer_price_id = newId;
                    product.over_price = overPrice;
                    renderRow(product);
                    
                    // Update modal item state
                    const actionContainer = modalItem.querySelector('.col-md-4');
                    actionContainer.innerHTML = `
                        <div class="text-end text-success small fw-bold slide-in">
                            <i class="fas fa-check-circle me-1"></i>${trans('added')}
                        </div>
                    `;
                });
            }

            function renderRow(product) {
                if (noPricesRow && noPricesRow.parentNode) noPricesRow.remove();

                const rowHtml = `
                    <tr id="price-row-${product.customer_price_id}" data-product-id="${product.id}" class="table-success">
                        <td class="ps-4">
                            <div class="d-flex align-items-center">
                                <div class="product-icon me-3 bg-light rounded p-2">
                                    <i class="fas fa-box text-primary"></i>
                                </div>
                                <div>
                                    <div class="fw-bold">${product.name}</div>
                                    <small class="text-muted">${product.category} > ${product.sub_category}</small>
                                </div>
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="base-price fw-medium">${product.clean_station_price}</span> 
                            <small class="text-muted">{{ trans('client.SAR') }}</small>
                        </td>
                        <td class="text-center">
                            <div class="input-group input-group-sm mx-auto" style="width: 130px;">
                                <input type="number" step="0.01" class="form-control over-price-input text-center" 
                                       value="${product.over_price}" 
                                       data-id="${product.customer_price_id}" 
                                       data-base="${product.clean_station_price}"
                                       data-url="${"{{ route('client.contracts.customer-prices.update', ['priceId' => ':id']) }}".replace(':id', product.customer_price_id)}">
                                <span class="input-group-text bg-light border-start-0 small" style="font-size: 0.7rem;">{{ trans('client.SAR') }}</span>
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="final-price fw-bold text-primary">${(parseFloat(product.clean_station_price) + parseFloat(product.over_price)).toFixed(2)}</span> 
                            <small class="text-primary fw-bold">{{ trans('client.SAR') }}</small>
                        </td>
                        <td class="text-end pe-4">
                            <button type="button" class="btn btn-outline-danger btn-sm rounded-circle delete-btn" 
                                    data-id="${product.customer_price_id}"
                                    data-url="${"{{ route('client.contracts.customer-prices.delete', ['priceId' => ':id']) }}".replace(':id', product.customer_price_id)}">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </td>
                    </tr>
                `;
                tbody.insertAdjacentHTML('afterbegin', rowHtml);
                const newRow = tbody.firstElementChild;
                setTimeout(() => newRow.classList.remove('table-success'), 2000);
            }

            function saveNewCustomerPrice(productId, overPrice, callback) {
                fetch(`{{ route('client.contracts.customer-prices.store') }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ product_id: productId, over_price: overPrice })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status) {
                            callback(data.data.id);
                            toastr.success(data.message);
                        } else {
                            toastr.error(data.message);
                        }
                    })
                    .catch(error => {
                        toastr.error('{{ trans('client.customer_price_add_failed') }}');
                    });
            }

            // Handle overprice changes
            tbody.addEventListener('input', function (e) {
                if (e.target.classList.contains('over-price-input')) {
                    const input = e.target;
                    const row = input.closest('tr');
                    const basePrice = parseFloat(input.dataset.base);
                    const overPrice = parseFloat(input.value) || 0;
                    row.querySelector('.final-price').textContent = (basePrice + overPrice).toFixed(2);

                    // Auto-save with debounce
                    clearTimeout(input.saveTimer);
                    input.saveTimer = setTimeout(() => {
                        saveUpdate(input.dataset.url, overPrice, input.dataset.id);
                    }, 1000);
                }
            });

            function saveUpdate(url, overPrice, id) {
                fetch(url, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ over_price: overPrice })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status) {
                            toastr.success(data.message);
                        } else {
                            toastr.error(data.message);
                        }
                    });
            }

            // Handle delete
            tbody.addEventListener('click', function (e) {
                const btn = e.target.closest('.delete-btn');
                if (btn) {
                    if (confirm('{{ trans('client.confirm_delete') }}')) {
                        fetch(btn.dataset.url, {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.status) {
                                    document.getElementById(`price-row-${btn.dataset.id}`).remove();
                                    toastr.success(data.message);
                                } else {
                                    toastr.error(data.message);
                                }
                            });
                    }
                }
            });

            function trans(key) {
                const translations = {
                    'SAR': '{{ trans('client.SAR') }}',
                    'base_price': '{{ trans('client.base_price') }}',
                    'already_in_list': '{{ trans('client.already_in_list') }}',
                    'over_price': '{{ trans('client.over_price') }}',
                    'add': '{{ trans('client.add') }}',
                    'added': '{{ trans('client.added') }}',
                    'no_products_found': '{{ trans('client.no_products_found') }}'
                };
                return translations[key] || key;
            }
        });
    </script>
@endsection