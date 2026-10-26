@php
    $additionalSubCategoryOptions = collect($subCategories ?? [])->map(function ($category) {
        return [
            'id' => $category->id,
            'parent_id' => $category->parent_id,
            'name' => $category->name,
        ];
    })->values();
@endphp

<div class="form-group col-12 mt-4" id="additional-products-section" style="display:none;">
    <div class="card border product-form-section">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-3 py-3">
            <div>
                <h3 class="card-title mb-1">{{ trans('Washing product prices') }}</h3>
                <div class="text-muted fs-7">{{ trans('Add every subcategory in which this item is offered, with its SKU, price, points, and cost.') }}</div>
            </div>
            <button type="button" class="btn btn-sm btn-primary" id="add-additional-product">
                <i class="fas fa-plus"></i> {{ trans('Add product row') }}
            </button>
        </div>
        <div class="card-body py-3">
            <div class="table-responsive product-table-wrap">
                <table class="table table-bordered align-middle text-center mb-0 product-inner-table" id="additional-products-table">
                    <thead><tr>
                        <th>{{ trans('sub category') }}</th><th>{{ trans('sku') }}</th><th>{{ trans('price') }}</th>
                        <th>{{ trans('points') }}</th><th>{{ trans('cost') }}</th><th>{{ trans('actions') }}</th>
                    </tr></thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
    let additionalProductIndex = 0;
    const additionalSubCategories = @json($additionalSubCategoryOptions);

    window.addAdditionalProductRow = function () {
            const categoryId = String($('#category_id').val() || '');
            const usedSubCategoryIds = [String($('#sub_category_id').val() || '')]
                .concat($('#additional-products-table select').map(function () { return String($(this).val() || ''); }).get());
            const options = additionalSubCategories
                .filter(category => String(category.parent_id) === categoryId && !usedSubCategoryIds.includes(String(category.id)))
                .map(category => `<option value="${category.id}">${$('<div>').text(category.name).html()}</option>`)
                .join('');

            if (!categoryId) {
                toastr.error("{{ trans('Select a category that has subcategories first.') }}");
                return false;
            }
            if (!options) {
                toastr.error("{{ trans('No additional subcategories are available.') }}");
                return false;
            }

            const index = additionalProductIndex++;
            $('#additional-products-table tbody').append(`<tr>
                <td><select class="form-select" name="additional_products[${index}][sub_category_id]" required><option value="">{{ trans('select sub category') }}</option>${options}</select></td>
                <td><input class="form-control" type="text" name="additional_products[${index}][sku]" required></td>
                <td><input class="form-control additional-price" type="number" min="0" step="0.01" name="additional_products[${index}][price]" required></td>
                <td><input class="form-control additional-points" type="number" min="0" step="0.01" name="additional_products[${index}][points]" required></td>
                <td><input class="form-control" type="number" min="0" step="0.01" name="additional_products[${index}][cost]" required></td>
                <td><button type="button" class="btn btn-sm btn-outline-danger remove-additional-product"><i class="fas fa-trash"></i></button></td>
            </tr>`);
            return true;
    };

    $(document).ready(function () {
        $('#add-additional-product').on('click', window.addAdditionalProductRow);
        $(document).on('input', '.additional-price', function () {
            $(this).closest('tr').find('.additional-points').val($(this).val());
        });
        $(document).on('click', '.remove-additional-product', function () { $(this).closest('tr').remove(); });
    });
</script>
@endpush
