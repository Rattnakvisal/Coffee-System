@php
    $cartItems = collect($cartItems ?? []);
    $cartSubtotal = (float) ($cartSubtotal ?? 0);
    $cartDiscount = (float) ($cartDiscount ?? 0);
    $cartTotal = (float) ($cartTotal ?? 0);
    $itemCount = $cartItems->sum('qty');
    $exchangeRate = app(\App\Services\PosPaymentService::class)->exchangeRate();
    $totalKhr = (int) ceil(round($cartTotal * 100) * $exchangeRate / 100);
@endphp

<aside data-cashier-cart
    class="anim-enter-right custom-scrollbar translate-x-full overflow-y-auto border-l border-[#ebdcd2]/70 bg-white/95 backdrop-blur-xl p-5 sm:p-6 transition-transform duration-300 ease-out max-lg:fixed max-lg:inset-y-0 max-lg:right-0 max-lg:z-50 max-lg:w-[86vw] max-lg:max-w-[380px] max-lg:shadow-[-16px_0_48px_rgba(43,33,27,0.14)] lg:sticky lg:top-0 lg:col-span-3 lg:h-screen lg:max-h-screen lg:w-auto lg:max-w-none lg:translate-x-0 lg:self-start lg:shadow-none">
    
    <!-- Cart Header -->
    <div class="flex items-center justify-between gap-3 pb-5 border-b border-[#ebdcd2]/60">
        <div>
            <div class="flex items-center gap-2">
                <h3 class="text-lg font-black tracking-tight text-[#2c221e]">Current Order</h3>
                @if ($itemCount > 0)
                    <span class="inline-flex items-center justify-center rounded-full bg-[#fbebe1] px-2.5 py-0.5 text-xs font-bold text-[#b45309]">
                        {{ $itemCount }} {{ Str::plural('item', $itemCount) }}
                    </span>
                @endif
            </div>
            <p class="text-xs text-[#8a7265] mt-0.5">Ready for quick checkout</p>
        </div>
        <button type="button" data-cashier-close
            class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-[#ebdcd2] bg-white text-[#705547] shadow-sm transition hover:bg-[#fbf5f0] lg:hidden">
            <span class="sr-only">Close cart</span>
            <x-icon name="x" class="h-4 w-4" />
        </button>
    </div>

    <!-- Cart Item List -->
    <div class="mt-5 space-y-3">
        @forelse ($cartItems as $item)
            <div class="flex items-center gap-3 rounded-2xl border border-[#ebdcd2]/70 bg-gradient-to-br from-white to-[#fbf8f5] p-3.5 shadow-xs transition hover:border-[#dfc3b2]">
                @if (!empty($item['image_url']))
                    <img src="{{ $item['image_url'] }}" alt="{{ $item['name'] }}"
                        class="h-16 w-16 shrink-0 rounded-xl object-cover border border-[#ebdcd2]/60">
                @else
                    <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-[#fff3ea] to-[#fbebe1] text-[#b45309] border border-[#f5d7c3]">
                        <x-icon name="coffee" class="h-7 w-7" />
                    </div>
                @endif

                <div class="min-w-0 flex-1">
                    <div class="flex items-start justify-between gap-1">
                        <h4 class="truncate font-bold text-sm text-[#2c221e]">{{ $item['name'] }}</h4>
                        <span class="text-sm font-extrabold text-[#2c221e] shrink-0">
                            ${{ number_format((float) $item['line_total'], 2) }}
                        </span>
                    </div>

                    <div class="flex items-center gap-1.5 mt-1">
                        <span class="inline-flex rounded-md bg-[#f4ebe3] px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wider text-[#73594b]">
                            {{ ucfirst($item['size']) }}
                        </span>
                        <span class="inline-flex rounded-md bg-[#fff3ea] px-1.5 py-0.5 text-[10px] font-bold text-[#b45309]">
                            Sugar {{ $item['sugar'] }}%
                        </span>
                        @if ((float) ($item['line_discount'] ?? 0) > 0)
                            <span class="text-[11px] text-[#9b8577] line-through ml-auto">
                                ${{ number_format((float) ($item['line_base_total'] ?? 0), 2) }}
                            </span>
                        @endif
                    </div>

                    <div class="mt-2.5 flex items-center justify-between">
                        <span class="text-xs text-[#8a7265]">Qty:</span>
                        <div class="flex items-center gap-2">
                            <form method="POST" action="{{ route('cashier.cart.decrement', $item['item_key']) }}"
                                class="js-cart-item-form">
                                @csrf
                                <button type="submit"
                                    class="flex h-6 w-6 items-center justify-center rounded-lg border border-[#ebdcd2] bg-white text-xs font-bold text-[#665044] shadow-xs transition hover:bg-[#fbf4ee] active:scale-90"
                                    aria-label="Decrease quantity">-</button>
                            </form>
                            <span class="min-w-5 text-center text-xs font-bold text-[#2c221e]">{{ $item['qty'] }}</span>
                            <form method="POST" action="{{ route('cashier.cart.increment', $item['item_key']) }}"
                                class="js-cart-item-form">
                                @csrf
                                <button type="submit"
                                    class="flex h-6 w-6 items-center justify-center rounded-lg bg-[#2c221e] text-xs font-bold text-white shadow-xs transition hover:bg-[#43342e] active:scale-90"
                                    aria-label="Increase quantity">+</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="rounded-2xl border border-dashed border-[#ebdcd2] bg-[#fdfbf9] py-10 px-5 text-center">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-[#fff3ea] text-[#b45309] shadow-xs">
                    <x-icon name="shopping-bag" class="h-7 w-7" />
                </div>
                <h5 class="mt-3 text-sm font-bold text-[#2c221e]">Cart is empty</h5>
                <p class="mt-1 text-xs text-[#8a7265] max-w-[200px] mx-auto">Click on any coffee drink from the menu to start order.</p>
            </div>
        @endforelse
    </div>

    <!-- Bill Summary -->
    <div class="mt-6 space-y-2.5 rounded-2xl bg-[#fbf5f0]/80 p-4 border border-[#ebdcd2]/70 text-xs">
        <div class="flex justify-between text-[#705547]">
            <span>Subtotal</span>
            <span class="font-semibold text-[#2c221e]">${{ number_format($cartSubtotal, 2) }}</span>
        </div>
        @if ($cartDiscount > 0)
            <div class="flex justify-between text-emerald-700">
                <span>Discount</span>
                <span class="font-semibold">-${{ number_format($cartDiscount, 2) }}</span>
            </div>
        @endif
        <div class="flex items-baseline justify-between border-t border-[#ebdcd2] pt-2.5">
            <span class="text-sm font-bold text-[#2c221e]">Total Due</span>
            <span class="text-xl font-black text-[#b45309]">${{ number_format($cartTotal, 2) }}</span>
        </div>
        <div class="flex items-center justify-between gap-2 text-[#705547]">
            <span>សរុបជារៀល <span class="text-[#9b8577]">/ KHR</span></span>
            <span class="text-base font-bold text-[#2c221e]">៛{{ number_format($totalKhr) }}</span>
        </div>
        <p class="border-t border-[#ebdcd2]/60 pt-2 text-[10px] text-[#8a7265]">Shop rate: $1 = ៛{{ number_format($exchangeRate) }}</p>
    </div>

    @include('cashier.sidebar.payment')
</aside>
