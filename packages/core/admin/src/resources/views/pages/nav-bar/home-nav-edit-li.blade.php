<li class="dd-item"
    @foreach (\LaravelLocalization::getSupportedLocales() as $language)
        data-title-{{ $language['prefix'] ?? "" }}="{{ $li->title->{$language['prefix'] ?? ""}  ?? null}}"
    @endforeach
    data-icon="{{ $li->icon ?? '' }}"
    data-page="{{ $li->page ?? '' }}"
    data-url="{{ $li->url ?? '' }}" >
    <div class="dd-handle">
        {{ $li->title->{config('app.locale')} }}
    </div>
    <span class="fas fa-plus-circle add-child-nav"></span>
    <span class="fas fa-trash delete-nav-item"></span>
    <span data-toggle="modal" data-target="#exampleModalLong" class="fas fa-edit edit-nav-item"></span>


    @if (!empty($li->sub))
        <ol class="dd-list">
            @foreach ($li->sub as $sub_li)
                @include('admin::pages.nav-bar.home-nav-edit-li', ['li' => $sub_li])
            @endforeach
        </ol>
    @endif

</li>
