
@if ($item and isset($item['type']) and $item['type'] == 'divided')
    <div class="menu-item">
        <div class="menu-content pt-8 pb-2">
            <span
                class="menu-section text-muted text-uppercase fs-8 ls-1">{{ $item['title'][config('app.locale')] ?? '' }}</span>
        </div>
    </div>
@elseif(!isset($item['sub']) or empty($item['sub']))
    <div class="menu-item">
      @php
         $href = null;
          if (isset($item['url']) and !empty($item['url'])){
            $href   =  $item['url'];
         }elseif(isset($item['route']) and !empty($item['route'])){
            $href   =  route($item['urt']); 
         }
      @endphp
        <a class="menu-link @if(url()->current() == $href) active @endif" href="{{ $href }}">
            <span class="menu-icon">
                <i class="{{ $item['icon'] }}  fs-3"></i>
            </span>
            <span class="menu-title">{{ $item['title'][config('app.locale')] }}</span>
        </a>
    </div>
@else
    <div data-kt-menu-trigger="click" class="menu-item menu-accordion">
        <span class="menu-link">
            <span class="menu-icon">
                <i class="{{ $item['icon'] }} fs-3"></i>
            </span>
            <span class="menu-title">{{ $item['title'][config('app.locale')] }}</span>
            <span class="menu-arrow"></span>
        </span>
        {{-- @dd($item['sub']) --}}
        <div class="menu-sub menu-sub-accordion menu-active-bg">
            @foreach ($item['sub'] as $subItem)
               @include('admin::pages.nav-bar.nav-li',['item'=>$subItem])
            @endforeach
        </div>
    </div>
@endif

