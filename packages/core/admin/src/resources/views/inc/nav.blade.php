<nav class="layout-navbar container-fluid navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
    id="layout-navbar">
    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
            <i class="ti ti-menu-2 ti-sm"></i>
        </a>
    </div>

    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
        <!-- Search -->
        <div class="navbar-nav align-items-center">
            @can('dashboard.clear_system_cache')
                <div class="nav-item navbar-search-wrapper mb-0">
                    <a id="clearSystemCache" class="nav-item nav-link search-toggler d-flex align-items-center px-0"
                        href="javascript:void(0);">
                        <i class="fas fa-eraser"></i>
                        {{ trans('Clear system cache') }}
                    </a>
                </div>
            @endcan
            @foreach (config('backend-settings.dashboard_icons_left') ?? [] as $icon)
                <div class="nav-item navbar-search-wrapper mb-0">
                    <a id="clearSystemCache" class="nav-item nav-link search-toggler d-flex align-items-center px-0"
                        href="{{ $icon['url'] ?? 'javascript:void(0);' }}">
                        <i class="{{ $icon['icon'] ?? '' }}"></i>
                        {{ $icon['title'] ?? '' }}
                    </a>
                </div>
            @endforeach

        </div>
        <!-- /Search -->

        <ul class="navbar-nav flex-row align-items-center ms-auto">
            <!-- Language -->
            <li class="nav-item dropdown-language dropdown me-2 me-xl-0">
                <a class="nav-link dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                    <i class="ti ti-language rounded-circle ti-md"></i>
                    {{ LaravelLocalization::getCurrentLocaleNative() }}
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                        @if ($localeCode == config('app.locale'))
                            @continue
                        @endif
                        <li>
                            <a class="dropdown-item"
                                href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}"
                                data-language="{{ $localeCode }}">
                                <span class="align-middle">{{ $properties['native'] }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </li>

            <!--/ Language -->


            <!-- User -->
            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <div class="avatar avatar-online">
                        <img src="{{ auth()->user()->avatarUrl }}" alt class="h-auto rounded-circle" />
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar avatar-online">
                                        <img src="{{ auth()->user()->avatarUrl }}" alt class="h-auto rounded-circle" />
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <span class="fw-medium d-block">{{ auth()->user()->fullname }}</span>
                                    <span class="fw-small d-block">{{ auth()->user()->email }}</span>
                                    <small class="text-muted">
                                        [
                                        @foreach (auth()->user()->roles as $index => $role)
                                            @if ($index)
                                                ,
                                            @endif
                                            {{ $role->name }}
                                        @endforeach

                                        ]
                                    </small>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li>
                        <div class="dropdown-divider"></div>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('dashboard.users.edit',auth()->user()->id) }}">
                            <i class="ti ti-user-check me-2 ti-sm"></i>
                            <span class="align-middle">{{ trans('My Profile') }}</span>
                        </a>
                    </li>

                    <li>
                        <div class="dropdown-divider"></div>
                    </li>
                    <li>
                        <a class="dropdown-item" onclick="document.getElementById('logout').submit()" href="#">
                            <i class="ti ti-logout me-2 ti-sm"></i>
                            <span class="align-middle">{{ trans('Log Out') }}</span>
                        </a>

                    </li>
                </ul>
            </li>
            <!--/ User -->
        </ul>
    </div>


</nav>
<form id="logout" action="{{ route('logout') }}" class="d-none" method="POST">
    @csrf
</form>

<script></script>
