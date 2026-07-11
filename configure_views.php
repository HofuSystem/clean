<?php

$paths = [
    'purchase-providers' => [
        'singular' => 'Purchase provider',
        'route' => 'dashboard.purchase-providers',
        'tableCols' => "['name'=>'name', 'commercial_registration'=>'commercial_registration', 'tax_number'=>'tax_number', 'city'=>'city', 'district'=>'district']",
        'formFields' => '
            @include("admin::components.input",["name"=>"name","type"=>"text","value"=>$item?->name,"label"=>trans("name"),"required"=>true])
            @include("admin::components.input",["name"=>"commercial_registration","type"=>"text","value"=>$item?->commercial_registration,"label"=>trans("commercial registration")])
            @include("admin::components.input",["name"=>"tax_number","type"=>"text","value"=>$item?->tax_number,"label"=>trans("tax number")])
            @include("admin::components.input",["name"=>"street_name","type"=>"text","value"=>$item?->street_name,"label"=>trans("street name")])
            @include("admin::components.input",["name"=>"building_no","type"=>"text","value"=>$item?->building_no,"label"=>trans("building no")])
            @include("admin::components.select",["name"=>"city_id","value"=>$item?->city_id,"label"=>trans("city"),"items"=>$cities])
            @include("admin::components.select",["name"=>"district_id","value"=>$item?->district_id,"label"=>trans("district"),"items"=>$districts])
            @include("admin::components.input",["name"=>"postal_code","type"=>"text","value"=>$item?->postal_code,"label"=>trans("postal code")])
        ',
        'showFields' => '
            <p><b>{{ trans("name") }}</b> : {{ $item->name }}</p>
            <p><b>{{ trans("commercial registration") }}</b> : {{ $item->commercial_registration }}</p>
            <p><b>{{ trans("tax number") }}</b> : {{ $item->tax_number }}</p>
            <p><b>{{ trans("street name") }}</b> : {{ $item->street_name }}</p>
            <p><b>{{ trans("building no") }}</b> : {{ $item->building_no }}</p>
            <p><b>{{ trans("city") }}</b> : {{ $item->city?->name }}</p>
            <p><b>{{ trans("district") }}</b> : {{ $item->district?->name }}</p>
            <p><b>{{ trans("postal code") }}</b> : {{ $item->postal_code }}</p>
        '
    ],
    'purchase-items' => [
        'singular' => 'Purchase item',
        'route' => 'dashboard.purchase-items',
        'tableCols' => "['name'=>'name']",
        'formFields' => '
            @include("admin::components.input",["name"=>"name","type"=>"text","value"=>$item?->name,"label"=>trans("name"),"required"=>true])
        ',
        'showFields' => '
            <p><b>{{ trans("name") }}</b> : {{ $item->name }}</p>
        '
    ],
    'purchases' => [
        'singular' => 'Purchase',
        'route' => 'dashboard.purchases',
        'tableCols' => "['item'=>'item', 'provider'=>'provider', 'value_before_tax'=>'value before tax', 'tax_value'=>'tax value', 'value_after_tax'=>'value after tax']",
        'formFields' => '
            @include("admin::components.select",["name"=>"item_id","value"=>$item?->item_id,"label"=>trans("item"),"items"=>$items,"required"=>true])
            @include("admin::components.select",["name"=>"provider_id","value"=>$item?->provider_id,"label"=>trans("provider"),"items"=>$providers,"required"=>true])
            @include("admin::components.input",["name"=>"value_before_tax","type"=>"number","value"=>$item?->value_before_tax,"label"=>trans("value before tax"),"required"=>true])
            @include("admin::components.input",["name"=>"tax_value","type"=>"number","value"=>$item?->tax_value,"label"=>trans("tax value"),"required"=>true])
            @include("admin::components.input",["name"=>"value_after_tax","type"=>"number","value"=>$item?->value_after_tax,"label"=>trans("value after tax"),"required"=>true])
            @include("admin::components.textarea",["name"=>"notes","value"=>$item?->notes,"label"=>trans("notes")])
        ',
        'showFields' => '
            <p><b>{{ trans("item") }}</b> : {{ $item->item?->name }}</p>
            <p><b>{{ trans("provider") }}</b> : {{ $item->provider?->name }}</p>
            <p><b>{{ trans("value before tax") }}</b> : {{ $item->value_before_tax }}</p>
            <p><b>{{ trans("tax value") }}</b> : {{ $item->tax_value }}</p>
            <p><b>{{ trans("value after tax") }}</b> : {{ $item->value_after_tax }}</p>
            <p><b>{{ trans("notes") }}</b> : {{ $item->notes }}</p>
        '
    ]
];

foreach ($paths as $dir => $config) {
    $basePath = "/var/www/html/clean/packages/core/financials/src/resources/views/pages/{$dir}/";
    
    // LIST
    $listPath = $basePath . 'list.blade.php';
    $listContent = file_get_contents($listPath);
    // Replace datatable stuff
    $listContent = preg_replace('/\@include\("admin::components.datatable",\s*\[[^\]]+\]\)/ms', 
        '@include("admin::components.datatable",["url"=>route("'.$config['route'].'.index"),"cols"=>'.$config['tableCols'].'])', $listContent);
    // Replace URL buttons
    $listContent = preg_replace('/route\("dashboard\.order-invoices/ms', 'route("'.$config['route'], $listContent);
    // Remove export/import buttons if present
    $listContent = preg_replace('/<a.*?export.*?<\/a>/ms', '', $listContent);
    $listContent = preg_replace('/<a.*?import.*?<\/a>/ms', '', $listContent);
    // Replace strings
    $listContent = str_replace('OrderInvoice', $config['singular'], $listContent);
    
    file_put_contents($listPath, $listContent);
    
    // EDIT
    $editPath = $basePath . 'edit.blade.php';
    $editContent = '@extends("admin::layouts.master")
@section("content")
<div class="row">
    <div class="col-12">
        <div class="card card-custom">
            <div class="card-header">
                <h3 class="card-title">{{ $title }}</h3>
            </div>
            <div class="card-body">
                <form action="{{ isset($item) ? route("'.$config['route'].'.edit", $item->id) : route("'.$config['route'].'.create") }}" method="POST" class="ajax-form">
                    @csrf
                    @if(isset($item)) @method("PUT") @endif
                    <div class="row">
                        '.$config['formFields'].'
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">{{ trans("save") }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection';
    file_put_contents($editPath, $editContent);
    
    // SHOW
    $showPath = $basePath . 'show.blade.php';
    $showContent = '@extends("admin::layouts.master")
@section("content")
<div class="row">
    <div class="col-12">
        <div class="card card-custom">
            <div class="card-header">
                <h3 class="card-title">{{ trans("Details") }}</h3>
            </div>
            <div class="card-body">
                '.$config['showFields'].'
            </div>
        </div>
    </div>
</div>
@endsection';
    file_put_contents($showPath, $showContent);
}

echo "Done\n";
