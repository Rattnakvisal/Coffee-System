@extends('layouts.app')

@section('content')
    @php
        $products = collect($products ?? []);
        $categories = collect($categories ?? []);
        $searchSuggestions = collect($searchSuggestions ?? []);
        $search = (string) ($search ?? '');
        $category = (string) ($category ?? '');
        $todayAttendance = $todayAttendance ?? null;
        $canWork = $todayAttendance !== null;
    @endphp

    <div class="anim-enter-up w-full min-h-screen overflow-hidden lg:overflow-visible bg-[#fbf9f6]">
        <div class="grid min-h-screen grid-cols-1 lg:grid-cols-12">
            <!-- Mobile Overlay -->
            <div data-cashier-overlay class="fixed inset-0 z-40 hidden bg-[#1f1713]/50 backdrop-blur-xs lg:hidden"></div>

            <!-- Left Navigation Sidebar -->
            @include('cashier.sidebar.sidebar', ['activeCashierMenu' => 'home'])

            <!-- Main POS Workspace -->
            <main
                class="anim-enter-up anim-delay-100 custom-scrollbar bg-[#fbf9f6] p-4 pt-18 sm:p-6 sm:pt-20 lg:col-span-6 lg:p-7 xl:col-span-7">
                
                <!-- Mobile Navigation Bar -->
                <div class="mb-5 flex items-center justify-between gap-3 rounded-2xl border border-[#ebdcd2] bg-white/95 backdrop-blur-md p-3 shadow-xs lg:hidden">
                    <button type="button" data-cashier-open-menu
                        class="inline-flex items-center gap-2 rounded-xl border border-[#ebdcd2] bg-[#fbf5f0] px-3.5 py-2 text-xs font-bold text-[#665044] shadow-xs active:scale-95 transition">
                        <x-icon name="menu" class="h-4 w-4 text-[#b45309]" />
                        <span>Menu</span>
                    </button>
                    <button type="button" data-cashier-open-cart
                        class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-[#e07a3c] to-[#b45309] px-4 py-2 text-xs font-bold text-white shadow-xs active:scale-95 transition">
                        <x-icon name="shopping-bag" class="h-4 w-4" />
                        <span>Cart</span>
                    </button>
                </div>

                <!-- Alert Messages -->
                @if (session('status'))
                    <div class="mb-5 flex items-center gap-3 rounded-2xl border border-emerald-200/80 bg-emerald-50/90 px-4 py-3.5 text-xs font-semibold text-emerald-800 shadow-xs">
                        <x-icon name="check-circle" class="h-5 w-5 shrink-0 text-emerald-600" />
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                @if ($errors->has('attendance'))
                    <div class="mb-5 flex items-center gap-3 rounded-2xl border border-rose-200/80 bg-rose-50/90 px-4 py-3.5 text-xs font-semibold text-rose-800 shadow-xs">
                        <x-icon name="alert-circle" class="h-5 w-5 shrink-0 text-rose-600" />
                        <span>{{ $errors->first('attendance') }}</span>
                    </div>
                @endif

                @unless ($canWork)
                    <div class="mb-5 flex items-center gap-3 rounded-2xl border border-amber-200 bg-amber-50/90 p-4 text-xs font-semibold text-amber-900 shadow-xs">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-800">
                            <x-icon name="lock" class="h-4 w-4" />
                        </span>
                        <div>
                            <p class="font-bold">POS Terminal Locked</p>
                            <p class="text-[11px] text-amber-700 mt-0.5">Please check attendance first in the Attendance tab to enable order placement.</p>
                        </div>
                    </div>
                @endunless

                <!-- Search Bar -->
                <div class="relative">
                    <form method="GET" action="{{ route('cashier.index') }}" class="relative w-full"
                        data-cashier-search-form data-cashier-search-suggestions='@json($searchSuggestions->values()->all())'>
                        <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-[#9e877a]">
                            <x-icon name="search" class="h-5 w-5" />
                        </span>
                        <input type="text" name="search" value="{{ $search }}" placeholder="Search coffee drinks, tea, pastry..."
                            autocomplete="off" data-cashier-search-input
                            class="w-full rounded-2xl border border-[#ebdcd2] bg-white/95 py-3.5 pl-12 pr-28 text-sm font-medium text-[#2c221e] placeholder:text-[#9e877a] shadow-xs outline-none transition focus:border-[#b45309] focus:ring-4 focus:ring-[#b45309]/10">
                        <div class="absolute right-2 top-1/2 flex -translate-y-1/2 items-center gap-1.5">
                            @if ($search !== '')
                                <a href="{{ route('cashier.index') }}"
                                    class="rounded-xl border border-[#ebdcd2] bg-white px-2.5 py-1.5 text-xs font-semibold text-[#665044] transition hover:bg-[#fbf4ee]">Clear</a>
                            @endif
                            <button type="submit"
                                class="rounded-xl bg-gradient-to-r from-[#e07a3c] to-[#b45309] px-3.5 py-1.5 text-xs font-bold text-white shadow-xs transition hover:from-[#d16c2e] hover:to-[#92400e]">
                                Search
                            </button>
                        </div>
                        <div data-cashier-search-dropdown class="coffee-search-dropdown hidden">
                            <ul data-cashier-search-results role="listbox" class="max-h-72 overflow-y-auto py-1.5"></ul>
                            <p data-cashier-search-empty
                                class="hidden px-4 py-3 text-xs font-semibold uppercase tracking-[0.08em] text-[#9f7a63]">
                                No matching menu found
                            </p>
                        </div>
                    </form>
                </div>

                <!-- Category Filters (Horizontal Scroll) -->
                <div class="mt-6 flex items-center gap-2 overflow-x-auto pb-1 custom-scrollbar">
                    <a href="{{ route('cashier.index', array_filter(['search' => $search])) }}"
                        @class([
                            'shrink-0 rounded-2xl px-5 py-2.5 text-xs font-bold transition-all duration-200',
                            'bg-[#2c221e] text-white shadow-md shadow-stone-900/15' => $category === '',
                            'border border-[#ebdcd2] bg-white/95 text-[#665044] hover:bg-[#fbf4ee] hover:text-[#2c221e] shadow-xs' =>
                                $category !== '',
                        ])>
                        All Items
                    </a>
                    @foreach ($categories as $categoryOption)
                        <a href="{{ route('cashier.index', array_filter(['search' => $search, 'category' => $categoryOption->slug])) }}"
                            @class([
                                'shrink-0 rounded-2xl px-5 py-2.5 text-xs font-bold transition-all duration-200',
                                'bg-[#2c221e] text-white shadow-md shadow-stone-900/15' =>
                                    $category === $categoryOption->slug,
                                'border border-[#ebdcd2] bg-white/95 text-[#665044] hover:bg-[#fbf4ee] hover:text-[#2c221e] shadow-xs' =>
                                    $category !== $categoryOption->slug,
                            ])>
                            {{ $categoryOption->name }}
                        </a>
                    @endforeach
                </div>

                <!-- Section Header -->
                <div class="mt-7 flex items-end justify-between">
                    <div>
                        <span class="inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-[#b45309]">
                            <span class="h-1.5 w-1.5 rounded-full bg-[#b45309]"></span>
                            Artisanal Brews
                        </span>
                        <h2 class="mt-1 text-2xl sm:text-3xl font-black tracking-tight text-[#2c221e]">Coffee Menu</h2>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="hidden sm:inline-flex rounded-full bg-[#f4ebe3] px-3 py-1 text-xs font-bold text-[#73594b]">
                            {{ $products->count() }} {{ Str::plural('drink', $products->count()) }}
                        </span>
                        <a href="{{ route('cashier.index') }}"
                            class="rounded-xl border border-[#ebdcd2] bg-white px-3.5 py-1.5 text-xs font-bold text-[#665044] shadow-xs transition hover:bg-[#fbf4ee]">
                            Refresh
                        </a>
                    </div>
                </div>

                <!-- Product Grid -->
                <div data-cashier-product-grid @class([
                    'mt-6 grid grid-cols-1 gap-5 xl:grid-cols-2',
                    'pointer-events-none opacity-60 select-none' => !$canWork,
                ])>
                    @forelse ($products as $item)
                        @php
                            $discountPercent = $item->normalizedDiscountPercent();
                            $smallBasePrice = $item->sizeBasePrice('small');
                            $mediumBasePrice = $item->sizeBasePrice('medium');
                            $largeBasePrice = $item->sizeBasePrice('large');
                            $smallPrice = $item->sizePrice('small');
                            $mediumPrice = $item->sizePrice('medium');
                            $largePrice = $item->sizePrice('large');
                            $allSizeOptions = collect([
                                [
                                    'key' => 'small',
                                    'label' => 'Small',
                                    'is_active' => $item->isSizeActive('small'),
                                ],
                                [
                                    'key' => 'medium',
                                    'label' => 'Medium',
                                    'is_active' => $item->isSizeActive('medium'),
                                ],
                                [
                                    'key' => 'large',
                                    'label' => 'Large',
                                    'is_active' => $item->isSizeActive('large'),
                                ],
                            ]);
                            $activeSizeOptions = $allSizeOptions
                                ->filter(fn(array $size): bool => (bool) $size['is_active'])
                                ->values();
                            $activeSizeKeys = $activeSizeOptions->pluck('key')->values()->all();
                            $hasActiveSizes = $activeSizeOptions->isNotEmpty();
                            $defaultSizeKey = (string) ($activeSizeOptions->first()['key'] ?? 'small');
                            $defaultSizeLabel = (string) ($activeSizeOptions->first()['label'] ?? 'Unavailable');
                            $defaultBasePrice = $hasActiveSizes ? $item->sizeBasePrice($defaultSizeKey) : 0;
                            $defaultPrice = $hasActiveSizes ? $item->sizePrice($defaultSizeKey) : 0;
                        @endphp
                        <div data-cashier-product-card
                            data-search-text="{{ str($item->name . ' ' . ($item->category?->name ?? '') . ' ' . ($item->description ?? ''))->lower()->squish() }}"
                            class="cafe-card rounded-[28px] p-4 sm:p-5 flex flex-col justify-between anim-pop anim-stagger"
                            style="--stagger: {{ $loop->index + 2 }};">
                            
                            <!-- Product Info Row -->
                            <div class="flex gap-4">
                                @php
                                    $productImageUrl = $item->imageUrl();
                                @endphp
                                @if ($productImageUrl)
                                    <img src="{{ $productImageUrl }}" alt="{{ $item->name }}"
                                        class="h-28 w-24 sm:h-32 sm:w-28 shrink-0 rounded-2xl object-cover border border-[#ebdcd2]/70 shadow-xs">
                                @else
                                    <div
                                        class="flex h-28 w-24 sm:h-32 sm:w-28 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-[#fff3ea] to-[#fbebe1] text-[#b45309] border border-[#f5d7c3] shadow-xs">
                                        <x-icon name="coffee" class="h-10 w-10 text-[#b45309]" />
                                    </div>
                                @endif

                                <div class="min-w-0 flex-1">
                                    <div class="flex items-start justify-between gap-1.5">
                                        <h3 class="truncate text-base sm:text-lg font-extrabold text-[#2c221e]">{{ $item->name }}</h3>
                                        <div class="text-right shrink-0">
                                            @if ($discountPercent > 0 && $hasActiveSizes)
                                                <p class="js-size-base-price-label text-xs font-semibold text-slate-400 line-through">
                                                    ${{ number_format($defaultBasePrice, 2) }}
                                                </p>
                                            @endif
                                            <span class="js-size-price-label text-base sm:text-lg font-black text-[#b45309]">
                                                {{ $hasActiveSizes ? '$' . number_format($defaultPrice, 2) : 'Unavailable' }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Tags Row -->
                                    <div class="flex items-center gap-1.5 mt-1">
                                        <span class="inline-flex rounded-md bg-[#f4ebe3] px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-[#73594b]">
                                            {{ $item->category?->name ?? 'Uncategorized' }}
                                        </span>
                                        @if ($discountPercent > 0)
                                            <span class="inline-flex rounded-md bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-700 border border-emerald-200/60">
                                                {{ rtrim(rtrim(number_format($discountPercent, 2), '0'), '.') }}% OFF
                                            </span>
                                        @endif
                                    </div>

                                    <p class="mt-2 text-xs text-[#8a7265] line-clamp-2">
                                        {{ $item->description ?: 'Freshly brewed and customized for quick cashier orders.' }}
                                    </p>
                                </div>
                            </div>

                            <!-- Controls Form -->
                            <form method="POST" action="{{ route('cashier.cart.add') }}"
                                class="js-product-cart-form mt-4"
                                data-base-price-small="{{ number_format($smallBasePrice, 2, '.', '') }}"
                                data-base-price-medium="{{ number_format($mediumBasePrice, 2, '.', '') }}"
                                data-base-price-large="{{ number_format($largeBasePrice, 2, '.', '') }}"
                                data-price-small="{{ number_format($smallPrice, 2, '.', '') }}"
                                data-price-medium="{{ number_format($mediumPrice, 2, '.', '') }}"
                                data-price-large="{{ number_format($largePrice, 2, '.', '') }}"
                                data-active-sizes='@json($activeSizeKeys)'>
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $item->id }}">
                                <input type="hidden" name="qty" value="1" class="js-product-qty-input">

                                <div class="rounded-2xl bg-[#fbf7f4]/90 p-3.5 border border-[#ebdcd2]/70 space-y-3">
                                    <!-- Size Selector -->
                                    <div class="space-y-1.5">
                                        <div class="flex items-center justify-between text-xs">
                                            <span class="font-bold text-[#73594b] text-[11px] uppercase tracking-wider">Cup Size</span>
                                            <span class="js-size-label rounded-md bg-[#fff3ea] px-2 py-0.5 text-[10px] font-bold uppercase text-[#b45309]">
                                                {{ $defaultSizeLabel }}
                                            </span>
                                        </div>
                                        <input type="hidden" name="size" value="{{ $defaultSizeKey }}" class="js-size-input">
                                        <div class="grid grid-cols-3 gap-1.5">
                                            @foreach ($allSizeOptions as $sizeOption)
                                                @php
                                                    $sizeKey = (string) ($sizeOption['key'] ?? 'small');
                                                    $sizeIsActive = (bool) ($sizeOption['is_active'] ?? false);
                                                    $isDefaultSize = $sizeKey === $defaultSizeKey && $sizeIsActive;
                                                @endphp
                                                <button type="button" data-size="{{ $sizeKey }}"
                                                    @class([
                                                        'js-size-option coffee-size-chip py-2 px-2 text-center text-xs font-bold transition',
                                                        'is-active' => $isDefaultSize,
                                                        'is-disabled' => !$sizeIsActive,
                                                    ])
                                                    aria-pressed="{{ $isDefaultSize ? 'true' : 'false' }}"
                                                    @disabled(!$sizeIsActive)>
                                                    {{ $sizeOption['label'] }}
                                                </button>
                                            @endforeach
                                        </div>
                                        @if (!$hasActiveSizes)
                                            <p class="text-[10px] font-bold uppercase tracking-wider text-rose-500">
                                                No active cup sizes.
                                            </p>
                                        @endif
                                    </div>

                                    <!-- Sugar Slider -->
                                    <div class="space-y-1.5">
                                        <div class="flex items-center justify-between text-xs">
                                            <span class="font-bold text-[#73594b] text-[11px] uppercase tracking-wider">Sugar Level</span>
                                            <span class="rounded-md bg-[#fff3ea] px-2 py-0.5 text-[10px] font-bold text-[#b45309]">
                                                <span class="js-sugar-label">50</span>%
                                            </span>
                                        </div>
                                        <input type="range" name="sugar" min="0" max="100" step="5" value="50"
                                            class="js-sugar-range coffee-sugar-range w-full cursor-pointer appearance-none rounded-full">
                                        <div class="flex items-center justify-between text-[10px] text-[#9b8577]">
                                            <span>0% (No sugar)</span>
                                            <span>50%</span>
                                            <span>100% (Sweet)</span>
                                        </div>
                                    </div>

                                    <!-- Quantity Stepper & Add Button -->
                                    <div class="flex items-center justify-between pt-1 border-t border-[#ebdcd2]/60">
                                        <div class="flex items-center gap-1.5 bg-white rounded-xl border border-[#ebdcd2] p-1 shadow-2xs">
                                            <button type="button"
                                                class="js-product-qty-decrease flex h-7 w-7 items-center justify-center rounded-lg bg-[#fbf5f0] text-sm font-bold text-[#665044] hover:bg-[#ebdcd2] active:scale-95 transition"
                                                aria-label="Decrease quantity">-</button>
                                            <span class="js-product-qty-label min-w-6 text-center text-xs font-bold text-[#2c221e]">1</span>
                                            <button type="button"
                                                class="js-product-qty-increase flex h-7 w-7 items-center justify-center rounded-lg bg-[#2c221e] text-sm font-bold text-white hover:bg-[#43342e] active:scale-95 transition"
                                                aria-label="Increase quantity">+</button>
                                        </div>

                                        <button type="submit" @disabled(!$hasActiveSizes || !$canWork)
                                            class="js-add-to-cart-btn inline-flex items-center gap-1.5 rounded-xl bg-gradient-to-r from-[#e07a3c] to-[#b45309] px-4 py-2.5 text-xs font-bold text-white shadow-md shadow-orange-900/15 transition hover:from-[#d16c2e] hover:to-[#92400e] active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed">
                                            <x-icon name="plus" class="h-4 w-4" />
                                            <span>Add to Cart</span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    @empty
                        <div class="col-span-full rounded-3xl border border-dashed border-[#ebdcd2] bg-white py-14 px-6 text-center shadow-xs">
                            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-[#fff3ea] text-[#b45309]">
                                <x-icon name="coffee" class="h-8 w-8 text-[#b45309]" />
                            </div>
                            <h4 class="mt-4 text-base font-bold text-[#2c221e]">No coffee products found</h4>
                            <p class="mt-1 text-xs text-[#8a7265]">Add products from the Admin Dashboard -> Products to see them here.</p>
                        </div>
                    @endforelse

                    <div data-cashier-search-no-results
                        class="col-span-full hidden rounded-3xl border border-dashed border-[#ebdcd2] bg-white py-14 px-6 text-center shadow-xs">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-[#fff3ea] text-[#b45309]">
                            <x-icon name="search" class="h-8 w-8 text-[#b45309]" />
                        </div>
                        <h4 class="mt-4 text-base font-bold text-[#2c221e]">No matching items found</h4>
                        <p class="mt-1 text-xs text-[#8a7265]">Try checking spelling or search for another keyword.</p>
                    </div>
                </div>
            </main>

            <!-- Right Cart Sidebar -->
            @include('cashier.sidebar.cart', ['activeCashierMenu' => 'cart'])
        </div>
    </div>

    <!-- Brewing Loading Overlay -->
    <div id="coffee-add-loading"
        class="fixed inset-0 z-[80] hidden items-center justify-center bg-[#1f1713]/40 backdrop-blur-sm">
        <div class="coffee-loader-card rounded-3xl bg-white/95 px-8 py-7 text-center shadow-2xl shadow-[#6a432b]/25 border border-[#ebdcd2]/70">
            <div class="coffee-loader mx-auto">
                <div class="coffee-loader-steam">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
                <div class="coffee-loader-cup"></div>
            </div>
            <p class="mt-4 text-sm font-bold tracking-wide text-[#5c4438]">Brewing your coffee...</p>
        </div>
    </div>
@endsection
