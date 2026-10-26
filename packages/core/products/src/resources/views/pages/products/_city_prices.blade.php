@php
    $cityPriceRows = old('prices', isset($item) ? $item->prices->toArray() : []);
    $cityPriceOptions = collect($cities ?? [])->map(function ($city) {
        return ['id' => $city->id, 'name' => $city->name];
    })->values();
@endphp

<div class="form-group col-12 mt-4" id="city-prices-section">
    <input type="hidden" name="prices_present" value="1">
    <div class="card border product-form-section">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-3 py-3">
            <div>
                <h3 class="card-title mb-1">{{ trans('Prices by city') }}</h3>
                <div class="text-muted fs-7">{{ trans('Add a city only when its price or cost differs from the base product values.') }}</div>
            </div>
            <button type="button" class="btn btn-sm btn-primary" id="add-city-price">
                <i class="fas fa-plus"></i> {{ trans('Add city price') }}
            </button>
        </div>
        <div class="card-body py-3">
            <div class="table-responsive product-table-wrap">
                <table class="table table-bordered align-middle text-center mb-0 product-inner-table" id="city-prices-table">
                    <thead><tr><th>{{ trans('city') }}</th><th>{{ trans('price') }}</th><th>{{ trans('cost') }}</th><th>{{ trans('actions') }}</th></tr></thead>
                    <tbody>
                        @foreach($cityPriceRows as $index => $cityPrice)
                            <tr>
                                <td>
                                    <input type="hidden" name="prices[{{ $index }}][id]" value="{{ $cityPrice['id'] ?? '' }}">
                                    <select class="form-select" name="prices[{{ $index }}][city_id]" required>
                                        <option value="">{{ trans('select city') }}</option>
                                        @foreach($cities ?? [] as $city)
                                            <option value="{{ $city->id }}" @selected((string)($cityPrice['city_id'] ?? '') === (string)$city->id)>{{ $city->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td><input class="form-control" type="number" min="0" step="0.01" name="prices[{{ $index }}][price]" value="{{ $cityPrice['price'] ?? '' }}" required></td>
                                <td><input class="form-control" type="number" min="0" step="0.01" name="prices[{{ $index }}][cost]" value="{{ $cityPrice['cost'] ?? '' }}" required></td>
                                <td><button type="button" class="btn btn-sm btn-outline-danger remove-city-price"><i class="fas fa-trash"></i></button></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
    $(document).ready(function () {
        let cityPriceIndex = {{ count($cityPriceRows) }};
        const cityOptions = @json($cityPriceOptions);
        $('#add-city-price').on('click', function () {
            const index = cityPriceIndex++;
            const options = cityOptions.map(city => `<option value="${city.id}">${$('<div>').text(city.name).html()}</option>`).join('');
            $('#city-prices-table tbody').append(`<tr>
                <td><select class="form-select" name="prices[${index}][city_id]" required><option value="">{{ trans('select city') }}</option>${options}</select></td>
                <td><input class="form-control" type="number" min="0" step="0.01" name="prices[${index}][price]" required></td>
                <td><input class="form-control" type="number" min="0" step="0.01" name="prices[${index}][cost]" required></td>
                <td><button type="button" class="btn btn-sm btn-outline-danger remove-city-price"><i class="fas fa-trash"></i></button></td>
            </tr>`);
        });
        $(document).on('click', '.remove-city-price', function () { $(this).closest('tr').remove(); });
    });
</script>
@endpush
