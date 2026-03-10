<li class="dd-item"
    @foreach (config('app.activeLangs') as $language)
        data-title-{{ $language['prefix'] ?? '' }}="{{ $li->title?->{$language['prefix'] ?? ''} ?? null }}"
    @endforeach
    data-type="{{ $li->type ?? '' }}" 
    data-icon="{{ $li->icon ?? '' }}" 
    data-route="{{ $li->route ?? '' }}"
    @if (isset($li->routeArray) and !empty($li->routeArray))
        data-route-array='{!! json_encode($li->routeArray)  !!}'
    @endif
    data-permission="{{ $li->permission ?? '' }}" 
    data-package="{{ $li->package ?? '' }}"
    data-url="{{ $li->url ?? '' }}" 
    >
    <div class="dd-handle">
        {{ $li->title?->{config('app.locale')} ?? null}}
    </div>
    <span class="fas fa-plus-circle add-child-nav"></span>
    <span class="fas fa-trash delete-nav-item"></span>
    <span data-bs-toggle="modal" data-bs-target="#exampleModalLong" class="fas fa-edit edit-nav-item"></span>


    @if (!empty($li->sub))
        <ol class="dd-list">
            @foreach ($li->sub as $sub_li)
                @include('admin::pages.nav-bar.nav-edit-li', ['li' => $sub_li])
            @endforeach
        </ol>
    @endif

</li>
