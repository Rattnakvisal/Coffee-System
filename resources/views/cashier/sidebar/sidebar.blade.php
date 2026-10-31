@php
    $activeCashierMenu = $activeCashierMenu ?? 'home';
@endphp

<aside data-cashier-menu
    class="anim-enter-left custom-scrollbar -translate-x-full overflow-y-auto border-r border-[#ebdcd2]/70 bg-white/90 backdrop-blur-xl p-5 sm:p-6 transition-transform duration-300 ease-out max-lg:fixed max-lg:inset-y-0 max-lg:left-0 max-lg:z-50 max-lg:w-[84vw] max-lg:max-w-[320px] max-lg:shadow-[16px_0_48px_rgba(43,33,27,0.14)] lg:sticky lg:top-0 lg:z-20 lg:col-span-3 lg:h-screen lg:max-h-screen lg:w-auto lg:max-w-none lg:translate-x-0 lg:self-start lg:shadow-none xl:col-span-2">
    <div class="flex h-full flex-col justify-between">
        <div>
            <!-- Brand Section -->
            <div class="flex items-center justify-between gap-3 pb-6 border-b border-[#ebdcd2]/50">
                <div class="flex items-center gap-3">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-[#e07a3c] to-[#b45309] text-white shadow-md shadow-orange-900/20 ring-2 ring-white">
                        <x-icon name="coffee" class="h-6 w-6" :strokeWidth="2.2" />
                    </span>
                    <div class="min-w-0">
                        <p class="truncate text-base font-extrabold tracking-tight text-[#2c221e]">Purr's Coffee</p>
                        <div class="flex items-center gap-1.5 mt-0.5">
                            <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="text-[11px] font-semibold text-[#8a7265] uppercase tracking-wider">POS Terminal</span>
                        </div>
                    </div>
                </div>
                <button type="button" data-cashier-close
                    class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-[#ebdcd2] bg-white text-[#705547] shadow-sm transition hover:bg-[#fbf5f0] lg:hidden">
                    <span class="sr-only">Close menu</span>
                    <x-icon name="x" class="h-4 w-4" />
                </button>
            </div>

            <!-- Navigation Links -->
            <nav class="mt-6 space-y-1.5">
                <a href="{{ route('cashier.index') }}" @class([
                    'group flex items-center gap-3.5 rounded-2xl px-4 py-3.5 text-sm font-semibold transition-all duration-200',
                    'bg-gradient-to-r from-[#fff3ea] to-[#fdece0] text-[#b45309] shadow-sm ring-1 ring-[#f5d5be]/70' =>
                        $activeCashierMenu === 'home',
                    'text-[#665044] hover:bg-[#fbf4ee] hover:text-[#2c221e]' => $activeCashierMenu !== 'home',
                ])>
                    <span @class([
                        'flex h-8 w-8 items-center justify-center rounded-xl transition-all',
                        'bg-white text-[#b45309] shadow-xs' => $activeCashierMenu === 'home',
                        'text-[#8f776a] group-hover:text-[#2c221e]' => $activeCashierMenu !== 'home',
                    ])>
                        <x-icon name="home" class="h-5 w-5" />
                    </span>
                    <span>Menu / Order</span>
                </a>

                <a href="{{ route('cashier.attendance') }}" @class([
                    'group flex items-center gap-3.5 rounded-2xl px-4 py-3.5 text-sm font-semibold transition-all duration-200',
                    'bg-gradient-to-r from-[#fff3ea] to-[#fdece0] text-[#b45309] shadow-sm ring-1 ring-[#f5d5be]/70' =>
                        $activeCashierMenu === 'attendance',
                    'text-[#665044] hover:bg-[#fbf4ee] hover:text-[#2c221e]' => $activeCashierMenu !== 'attendance',
                ])>
                    <span @class([
                        'flex h-8 w-8 items-center justify-center rounded-xl transition-all',
                        'bg-white text-[#b45309] shadow-xs' => $activeCashierMenu === 'attendance',
                        'text-[#8f776a] group-hover:text-[#2c221e]' => $activeCashierMenu !== 'attendance',
                    ])>
                        <x-icon name="calendar-check" class="h-5 w-5" />
                    </span>
                    <span>Attendance</span>
                </a>

                <a href="{{ route('cashier.history') }}" @class([
                    'group flex items-center gap-3.5 rounded-2xl px-4 py-3.5 text-sm font-semibold transition-all duration-200',
                    'bg-gradient-to-r from-[#fff3ea] to-[#fdece0] text-[#b45309] shadow-sm ring-1 ring-[#f5d5be]/70' =>
                        $activeCashierMenu === 'history',
                    'text-[#665044] hover:bg-[#fbf4ee] hover:text-[#2c221e]' => $activeCashierMenu !== 'history',
                ])>
                    <span @class([
                        'flex h-8 w-8 items-center justify-center rounded-xl transition-all',
                        'bg-white text-[#b45309] shadow-xs' => $activeCashierMenu === 'history',
                        'text-[#8f776a] group-hover:text-[#2c221e]' => $activeCashierMenu !== 'history',
                    ])>
                        <x-icon name="history" class="h-5 w-5" />
                    </span>
                    <span>Order History</span>
                </a>
            </nav>
        </div>

        <!-- Bottom Session Card & Admin Switch -->
        <div class="pt-6 border-t border-[#ebdcd2]/60 space-y-3">
            <div class="rounded-2xl bg-gradient-to-br from-[#fbf5f0] to-[#f5ebe1] p-3.5 border border-[#ebdcd2]/70">
                <p class="text-xs font-bold text-[#3d2c23]">Purr's Artisanal Café</p>
                <p class="text-[11px] text-[#7d6659] mt-0.5">Quick, fresh service guaranteed</p>
            </div>

            <form method="POST" action="{{ route('cashier.dashboard.go') }}">
                @csrf
                <button type="submit"
                    class="group flex w-full items-center justify-between rounded-2xl border border-[#ebdcd2] bg-white px-4 py-3 text-xs font-bold text-[#5c4438] shadow-sm transition hover:border-[#dfc3b2] hover:bg-[#fff9f5] hover:text-[#2c221e] active:scale-[0.99]">
                    <span class="inline-flex items-center gap-2">
                        <x-icon name="layout-dashboard" class="h-4 w-4 text-[#b45309] transition group-hover:scale-110" />
                        Admin Dashboard
                    </span>
                    <x-icon name="chevron-right" class="h-3.5 w-3.5 text-[#9e8577] transition group-hover:translate-x-0.5" />
                </button>
            </form>
        </div>
    </div>
</aside>
