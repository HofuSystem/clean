<!-- END: Main Menu-->
<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ route('dashboard.index') }}" class="app-brand-link">
            <span class="app-brand-logo demo">
                <img src="{{ config('app.logo') }}" alt="">
            </span>
            <!-- <span class="app-brand-text demo menu-text fw-bold">{{ config('app.name') }}</span> -->
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="ti menu-toggle-icon d-none d-xl-block ti-sm align-middle"></i>
            <i class="ti ti-x d-block d-xl-none ti-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1 overflow-auto">

        @foreach (\Core\Admin\Services\DashboardService::getMenu() ?? [] as $item)
            @can($item['permission'] ?? null)
                @if (isset($item['type']) and $item['type'] == 'title')
                    <li class="menu-header small text-uppercase">
                        <span class="menu-header-text">{{ $item['titleLocale'] ?? '' }}</span>
                    </li>
                @else
                    <!-- Dashboards -->
                    <li class="menu-item @if ($item['active']) active open @endif">
                        <a href="{{ $item['url'] ?? 'javascript:void(0);' }} " @if(isset($item['url']) and !str_contains($item['url'], 'cleanstation.app')) target="_blank" @endif
                            class="menu-link @if (isset($item['sub']) and !empty($item['sub'])) menu-toggle @endif">
                            <i class="{{ $item['icon'] ?? '' }} mx-2"></i>
                            <div data-i18n="{{ $item['titleLocale'] ?? '' }}">{{ $item['titleLocale'] ?? '' }}</div>
                        </a>
                        @if (isset($item['sub']) and !empty($item['sub']))
                            <ul class="menu-sub">
                                @foreach ($item['sub'] as $sub_item)
                                    @can($sub_item['permission'] ?? null)
                                        <li class="menu-item @if ($sub_item['active']) active @endif">
                                            <a href="{{ $sub_item['url'] ?? '' }}" class="menu-link" @if(isset($sub_item['url']) and !str_contains($sub_item['url'], 'cleanstation.app')) target="_blank" @endif>
                                                <div data-i18n="{{ $sub_item['titleLocale'] ?? '' }}">
                                                    {{ $sub_item['titleLocale'] ?? '' }}
                                                </div>
                                            </a>
                                        </li>
                                    @endcan
                                @endforeach
                            </ul>
                        @endif
                    </li>
                @endif
            @endcan
        @endforeach




    </ul>
</aside>

<!-- Button trigger modal -->


@push('js')

@endpush
