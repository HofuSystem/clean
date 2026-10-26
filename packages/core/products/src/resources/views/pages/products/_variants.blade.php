@php
    $savedVariantRows = isset($item) ? $item->variants->toArray() : [];
    $isLegacyPackageVariant = isset($item) && $item->is_package && count($savedVariantRows) === 0;

    // Packages created before product variants existed stored their first option
    // directly on the product. Present that data as an editable draft option;
    // saving it creates the variant while retaining the product-level values for
    // older app versions.
    if ($isLegacyPackageVariant) {
        $legacyText = collect(['ar', 'en'])
            ->flatMap(function ($locale) use ($item) {
                $translation = $item->translate($locale);
                return [$translation?->name, $translation?->desc];
            })
            ->filter()
            ->implode(' ');

        $legacyText = strtr($legacyText, [
            '٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4',
            '٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9',
        ]);
        preg_match('/(\d+)\s*(?:قطعة|قطع|pieces?)/iu', $legacyText, $piecesMatch);
        preg_match('/(?:قطعة\s+إضافية|extra\s+piece)[^\d]*(\d+(?:[.,]\d+)?)/iu', $legacyText, $extraPriceMatch);

        $savedVariantRows = [[
            'pieces_count' => $piecesMatch[1] ?? null,
            'price' => $item->price,
            'points' => $item->points ?? $item->price,
            'cost' => $item->cost ?? 0,
            'extra_piece_price' => isset($extraPriceMatch[1]) ? str_replace(',', '.', $extraPriceMatch[1]) : null,
            'status' => $item->status ?? 'active',
        ]];
    }

    $variantRows = old('variants', $savedVariantRows);
@endphp

<div class="col-12 mt-4" id="product-variants-section"
    style="{{ isset($item) && ($item->is_package || count($variantRows) > 0) ? '' : 'display: none;' }}">
    <input type="hidden" name="variants_present" value="1">
    <div class="card border mb-4 product-form-section">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-3 py-3">
            <div>
                <h3 class="card-title mb-1">{{ trans('Product options') }}</h3>
                <div class="text-muted fs-7">{{ trans('Add at least one option. Each pieces count has its own price, points, and cost.') }}</div>
                @if($isLegacyPackageVariant && !old('variants'))
                    <div class="text-warning fs-7 mt-2">{{ trans('This legacy package was prepared as its first product option. Review the pieces count, then save the product.') }}</div>
                @endif
            </div>
            <button type="button" class="btn btn-sm btn-primary" id="add-product-variant">
                <i class="fas fa-plus"></i> {{ trans('Add option') }}
            </button>
        </div>
        <div class="card-body py-3">
            <div class="table-responsive product-table-wrap">
                <table class="table table-bordered align-middle text-center mb-0 product-inner-table" id="product-variants-table">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th>{{ trans('Pieces count') }}</th>
                            <th>@lang('price')</th>
                            <th>@lang('points')</th>
                            <th>@lang('cost')</th>
                            <th>{{ trans('Extra piece price') }}</th>
                            <th>@lang('status')</th>
                            <th>{{ trans('actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($variantRows as $index => $variant)
                            <tr>
                                <td><input type="hidden" name="variants[{{ $index }}][id]" value="{{ $variant['id'] ?? '' }}"><input class="form-control" type="number" min="1" name="variants[{{ $index }}][pieces_count]" value="{{ $variant['pieces_count'] ?? '' }}"></td>
                                <td><input class="form-control" type="number" min="0" step="0.01" name="variants[{{ $index }}][price]" value="{{ $variant['price'] ?? '' }}" required></td>
                                <td><input class="form-control" type="number" min="0" step="0.01" name="variants[{{ $index }}][points]" value="{{ $variant['points'] ?? '' }}" required></td>
                                <td><input class="form-control" type="number" min="0" step="0.01" name="variants[{{ $index }}][cost]" value="{{ $variant['cost'] ?? '' }}" required></td>
                                <td><input class="form-control" type="number" min="0" step="0.01" name="variants[{{ $index }}][extra_piece_price]" value="{{ $variant['extra_piece_price'] ?? '' }}"></td>
                                <td><select class="form-select" name="variants[{{ $index }}][status]" required><option value="active" @selected(($variant['status'] ?? 'active') === 'active')>@lang('active')</option><option value="not-active" @selected(($variant['status'] ?? '') === 'not-active')>@lang('not-active')</option></select></td>
                                <td><button type="button" class="btn btn-sm btn-outline-danger remove-product-variant"><i class="fas fa-trash"></i></button></td>
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
    let productVariantIndex = {{ count($variantRows) }};
    window.addProductVariantRow = function () {
            const index = productVariantIndex++;
            $('#product-variants-table tbody').append(`<tr>
                <td><input class="form-control" type="number" min="1" name="variants[${index}][pieces_count]"></td>
                <td><input class="form-control" type="number" min="0" step="0.01" name="variants[${index}][price]" required></td>
                <td><input class="form-control" type="number" min="0" step="0.01" name="variants[${index}][points]" required></td>
                <td><input class="form-control" type="number" min="0" step="0.01" name="variants[${index}][cost]" required></td>
                <td><input class="form-control" type="number" min="0" step="0.01" name="variants[${index}][extra_piece_price]"></td>
                <td><select class="form-select" name="variants[${index}][status]" required><option value="active">{{ trans('active') }}</option><option value="not-active">{{ trans('not-active') }}</option></select></td>
                <td><button type="button" class="btn btn-sm btn-outline-danger remove-product-variant"><i class="fas fa-trash"></i></button></td>
            </tr>`);
    };

    $(document).ready(function () {
        $('#add-product-variant').on('click', window.addProductVariantRow);
        $(document).on('click', '.remove-product-variant', function () {
            $(this).closest('tr').remove();
        });
    });
</script>
@endpush
