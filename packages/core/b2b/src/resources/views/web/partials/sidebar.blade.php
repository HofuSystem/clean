@php
    $navItems = [
        ['id' => 'dashboard', 'route' => 'client.dashboard', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6', 'permission' => 'dashboard-analytics'],
        ['id' => 'analytics', 'route' => 'client.analytics', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'permission' => 'dashboard-analytics'],
        ['id' => 'orders', 'route' => 'client.order.index', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'permission' => 'manage-orders'],
        ['id' => 'schedule', 'route' => 'client.schedule.index', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', 'permission' => 'manage-scheduling-addresses'],
        ['id' => 'monthly-statement', 'route' => 'client.monthly-invoices', 'icon' => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'permission' => 'invoices-payments'],
        ['id' => 'guest-service-pricing-management', 'route' => 'client.contracts.customer-prices', 'icon' => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z', 'permission' => 'edit-guest-pricing'],
        ['id' => 'guest_orders', 'route' => 'client.clientsOrders', 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', 'permission' => 'manage-orders'],
        ['id' => 'branches', 'route' => 'client.branches.index', 'icon' => 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z', 'permission' => 'manage-branches'],
        ['id' => 'support', 'onclick' => "openModal('ticket-new-modal')", 'icon' => 'M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z', 'permission' => null],
        ['id' => 'role_management', 'route' => 'client.employees.index', 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z', 'permission' => 'manage-user-permissions'],
        ['id' => 'account_settings', 'route' => 'client.profile.update-profile', 'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z', 'permission' => null],
        ['id' => 'subscription', 'route' => 'client.contracts.contract', 'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'permission' => 'manage-orders'],
    ];
@endphp

<!-- Sidebar -->
<aside id="main-sidebar"
    @php
        $isRtl = app()->getLocale() == 'ar';
        $sidebarXClass = $isRtl ? 'translate-x-full' : '-translate-x-full';
        $sidebarPosClass = $isRtl ? 'right-0' : 'left-0';
    @endphp
    class="fixed lg:relative {{ $sidebarPosClass }} w-72 bg-white border-x border-gray-200/50 flex flex-col shrink-0 overflow-y-auto h-[calc(100vh-90px)] print-hidden z-50 transition-transform duration-300 transform {{ $sidebarXClass }} lg:translate-x-0 custom-scrollbar">
    <nav class="flex-1 py-6 px-4">
        <ul class="space-y-1.5 text-sm font-bold text-gray-500" id="sidebar-nav">
            @foreach ($navItems as $item)
                @if (isset($item['permission']) && !\Core\B2B\Helpers\B2BHelper::hasPermission($item['permission']))
                    @continue
                @endif
                @if (isset($item['divider']))
                    <div class="h-px bg-gray-100 my-4 mx-4"></div>
                @else
                    @php
                        $isActive = isset($item['route']) && request()->routeIs($item['route']);
                        $class = $isActive 
                            ? 'bg-gray-900 text-white shadow-md' 
                            : 'hover:bg-gray-50 text-gray-500 hover:text-gray-900';
                        $href = isset($item['route']) ? route($item['route']) : 'javascript:void(0)';
                        $onclick = isset($item['onclick']) ? $item['onclick'] : '';
                    @endphp
                    <li>
                        <a href="{{ $href }}" onclick="{{ $onclick }}"
                            class="sidebar-btn w-full flex items-center gap-4 px-4 py-3.5 rounded-2xl transition-all {{ $class }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="{{ $item['icon'] }}"></path>
                            </svg>
                            <span data-i18n="{{ $item['id'] }}">{{ trans('client.' . $item['id']) }}</span>
                        </a>
                    </li>
                @endif
            @endforeach
        </ul>
        <!-- Mobile Logout -->
        <div class="mt-4 pt-4 border-t border-gray-100 lg:hidden px-4">
            <button onclick="handleLogout()"
                class="w-full flex items-center gap-4 px-4 py-3.5 rounded-2xl transition-all text-red-500 hover:bg-red-50">
                <svg class="w-5 h-5 rtl-rotate" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                    </path>
                </svg>
                <span>{{ trans('client.logout') ?? trans('logout') }}</span>
            </button>
        </div>
    </nav>
</aside>

