@extends('layouts.app')

@section('content')
    @php
        $orders = $orders ?? new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
        $period = (string) ($period ?? 'day');
        $search = (string) ($search ?? '');
        $selectedPayment = strtolower((string) ($selectedPayment ?? 'all'));
        $selectedStatus = strtolower((string) ($selectedStatus ?? 'all'));
        $paymentOptions = collect($paymentOptions ?? [])
            ->filter()
            ->unique()
            ->values();
        $statusOptions = collect($statusOptions ?? [])
            ->filter()
            ->unique()
            ->values();
    @endphp

    <div class="anim-enter-up w-full min-h-screen overflow-hidden bg-[#fbf9f6] lg:overflow-visible">
        <div class="grid min-h-screen grid-cols-1 lg:grid-cols-12">
            <!-- Mobile Overlay -->
            <div data-cashier-overlay class="fixed inset-0 z-40 hidden bg-[#1f1713]/50 backdrop-blur-xs lg:hidden"></div>
            
            <!-- Left Navigation Sidebar -->
            @include('cashier.sidebar.sidebar', ['activeCashierMenu' => 'history'])

            <!-- Main Workspace -->
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

                @if (session('status'))
                    <div class="mb-5 flex items-center gap-3 rounded-2xl border border-emerald-200/80 bg-emerald-50/90 px-4 py-3.5 text-xs font-semibold text-emerald-800 shadow-xs">
                        <x-icon name="check-circle" class="h-5 w-5 shrink-0 text-emerald-600" />
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-5 flex items-center gap-3 rounded-2xl border border-rose-200/80 bg-rose-50/90 px-4 py-3.5 text-xs font-semibold text-rose-800 shadow-xs">
                        <x-icon name="alert-circle" class="h-5 w-5 shrink-0 text-rose-600" />
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <!-- History Section Card -->
                <section
                    class="rounded-[28px] border border-[#ebdcd2]/80 bg-white p-5 sm:p-6 shadow-[0_4px_24px_rgba(43,33,27,0.04)]">
                    <div class="mb-5 flex flex-wrap items-center justify-between gap-3 pb-4 border-b border-[#ebdcd2]/60">
                        <div>
                            <span class="inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-[#b45309]">
                                <span class="h-1.5 w-1.5 rounded-full bg-[#b45309]"></span>
                                Cashier Transactions
                            </span>
                            <h3 class="mt-1 text-2xl font-black tracking-tight text-[#2c221e]">Order History</h3>
                            @if ($orders->count() > 0)
                                <p class="mt-1 text-xs text-[#8a7265]">
                                    Showing {{ $orders->firstItem() }}-{{ $orders->lastItem() }} of {{ $orders->total() }} total orders
                                </p>
                            @else
                                <p class="mt-1 text-xs text-[#8a7265]">No orders match the current filter</p>
                            @endif
                        </div>

                        <button type="button" data-history-filter-open aria-controls="history-filter-panel"
                            aria-expanded="false"
                            class="inline-flex items-center gap-2 rounded-xl border border-[#ebdcd2] bg-[#fbf5f0] px-4 py-2.5 text-xs font-bold text-[#665044] shadow-xs transition hover:bg-white hover:border-[#dfc3b2] active:scale-95">
                            <x-icon name="filter" class="h-4 w-4 text-[#b45309]" />
                            <span>Filter Orders</span>
                        </button>
                    </div>

                    <!-- Slide-out Filter Panel -->
                    <div data-history-filter-backdrop
                        class="fixed inset-0 z-[60] hidden bg-[#1f1713]/40 backdrop-blur-xs"></div>

                    <div id="history-filter-panel" data-history-filter-panel
                        class="fixed inset-y-0 right-0 z-[70] hidden w-full max-w-[420px] translate-x-full overflow-y-auto border-l border-[#ebdcd2] bg-white p-6 shadow-2xl transition-transform duration-300 ease-out custom-scrollbar"
                        role="dialog" aria-modal="true" aria-labelledby="history-filter-title">
                        <div class="mb-6 flex items-start justify-between gap-4 pb-4 border-b border-[#ebdcd2]/60">
                            <div>
                                <span class="text-xs font-bold uppercase tracking-wider text-[#b45309]">Search & Sort</span>
                                <h4 id="history-filter-title" class="mt-1 text-xl font-black tracking-tight text-[#2c221e]">
                                    Filter History
                                </h4>
                            </div>
                            <button type="button" data-history-filter-close
                                class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-[#ebdcd2] bg-[#fbf5f0] text-[#705547] shadow-xs transition hover:bg-white"
                                aria-label="Close filter">
                                <x-icon name="x" class="h-4 w-4" />
                            </button>
                        </div>
                        <form method="GET" action="{{ route('cashier.history') }}" class="space-y-4">
                            <div>
                                <label for="history_search"
                                    class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-[#73594b]">
                                    Search Order
                                </label>
                                <input id="history_search" type="text" name="search" value="{{ $search }}"
                                    placeholder="Order number, payment, or notes"
                                    class="w-full rounded-xl border border-[#ebdcd2] bg-[#fbf8f5] px-4 py-3 text-xs font-semibold text-[#2c221e] outline-none transition focus:border-[#b45309] focus:ring-2 focus:ring-[#b45309]/15">
                            </div>
                            <div>
                                <label for="history_period"
                                    class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-[#73594b]">
                                    Time Period
                                </label>
                                <select id="history_period" name="period"
                                    class="w-full rounded-xl border border-[#ebdcd2] bg-[#fbf8f5] px-4 py-3 text-xs font-semibold text-[#2c221e] outline-none transition focus:border-[#b45309] focus:ring-2 focus:ring-[#b45309]/15">
                                    <option value="day" {{ $period === 'day' ? 'selected' : '' }}>Today</option>
                                    <option value="week" {{ $period === 'week' ? 'selected' : '' }}>This Week</option>
                                    <option value="month" {{ $period === 'month' ? 'selected' : '' }}>This Month</option>
                                </select>
                            </div>
                            <div>
                                <label for="history_payment"
                                    class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-[#73594b]">
                                    Payment Method
                                </label>
                                <select id="history_payment" name="payment"
                                    class="w-full rounded-xl border border-[#ebdcd2] bg-[#fbf8f5] px-4 py-3 text-xs font-semibold text-[#2c221e] outline-none transition focus:border-[#b45309] focus:ring-2 focus:ring-[#b45309]/15">
                                    <option value="all" {{ $selectedPayment === 'all' ? 'selected' : '' }}>All Methods</option>
                                    @foreach ($paymentOptions as $paymentOption)
                                        @php $paymentValue = strtolower((string) $paymentOption); @endphp
                                        <option value="{{ $paymentValue }}"
                                            {{ $selectedPayment === $paymentValue ? 'selected' : '' }}>
                                            {{ $paymentValue === 'qr' ? 'KHQR / Bakong' : str($paymentValue)->replace('_', ' ')->headline() }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="history_status"
                                    class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-[#73594b]">
                                    Order Status
                                </label>
                                <select id="history_status" name="status"
                                    class="w-full rounded-xl border border-[#ebdcd2] bg-[#fbf8f5] px-4 py-3 text-xs font-semibold text-[#2c221e] outline-none transition focus:border-[#b45309] focus:ring-2 focus:ring-[#b45309]/15">
                                    <option value="all" {{ $selectedStatus === 'all' ? 'selected' : '' }}>All Statuses</option>
                                    @foreach ($statusOptions as $statusOption)
                                        @php $statusOptionValue = strtolower((string) $statusOption); @endphp
                                        <option value="{{ $statusOptionValue }}"
                                            {{ $selectedStatus === $statusOptionValue ? 'selected' : '' }}>
                                            {{ str($statusOptionValue)->replace('_', ' ')->headline() }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="grid grid-cols-2 gap-3 pt-4 border-t border-[#ebdcd2]/60">
                                <a href="{{ route('cashier.history') }}"
                                    class="inline-flex items-center justify-center rounded-xl border border-[#ebdcd2] bg-[#fbf5f0] py-3 text-xs font-bold text-[#665044] transition hover:bg-white">
                                    Reset
                                </a>
                                <button type="submit"
                                    class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-[#e07a3c] to-[#b45309] py-3 text-xs font-bold text-white shadow-md shadow-orange-900/15 transition hover:from-[#d16c2e] hover:to-[#92400e]">
                                    Apply Filter
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Orders Table -->
                    <div class="overflow-hidden rounded-2xl border border-[#ebdcd2]/80 bg-white">
                        <div class="coffee-table-scroll custom-scrollbar overflow-x-auto">
                            <table class="min-w-[650px] w-full text-left text-xs">
                                <thead class="bg-[#fcfaf7] text-[#73594b] border-b border-[#ebdcd2]">
                                    <tr>
                                        <th class="px-5 py-3.5 font-bold uppercase tracking-wider text-[11px]">Order #</th>
                                        <th class="px-5 py-3.5 font-bold uppercase tracking-wider text-[11px]">Status</th>
                                        <th class="px-5 py-3.5 font-bold uppercase tracking-wider text-[11px]">Items</th>
                                        <th class="px-5 py-3.5 font-bold uppercase tracking-wider text-[11px]">Payment</th>
                                        <th class="px-5 py-3.5 font-bold uppercase tracking-wider text-[11px]">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-[#ebdcd2]/60">
                                    @forelse ($orders as $order)
                                        @php
                                            $orderNumber = (string) ($order->order_number ?? '-');
                                            $orderDateValue = $order->placed_at ?? $order->created_at;
                                            $formattedOrderDate =
                                                $orderDateValue instanceof \Carbon\Carbon
                                                    ? $orderDateValue->format('M d, Y h:i A')
                                                    : ($orderDateValue
                                                        ? \Carbon\Carbon::parse((string) $orderDateValue)->format(
                                                            'M d, Y h:i A',
                                                        )
                                                        : '-');
                                            $statusValue = strtolower((string) ($order->status ?? 'completed'));
                                            $statusClasses = match ($statusValue) {
                                                'completed', 'paid' => 'bg-emerald-100 text-emerald-800 ring-1 ring-emerald-200',
                                                'pending', 'processing' => 'bg-amber-100 text-amber-800 ring-1 ring-amber-200',
                                                'failed',
                                                'cancelled',
                                                'canceled',
                                                'refunded'
                                                    => 'bg-rose-100 text-rose-800 ring-1 ring-rose-200',
                                                default => 'bg-slate-100 text-slate-700 ring-1 ring-slate-200',
                                            };
                                            $itemNames = collect($order->items ?? [])
                                                ->pluck('product_name')
                                                ->filter()
                                                ->values();
                                            $itemNamePreview = $itemNames->take(3)->implode(', ') ?: '-';
                                            $extraItemCount = max(0, $itemNames->count() - 3);
                                            $itemsCount = (int) ($order->items_count ?? $itemNames->count());

                                            if ($extraItemCount > 0) {
                                                $itemNamePreview .= ' +' . $extraItemCount . ' more';
                                            }
                                        @endphp
                                        <tr class="align-middle transition-colors hover:bg-[#fcfaf7]">
                                            <td class="px-5 py-4">
                                                <p class="font-extrabold text-[#2c221e] text-sm" title="{{ $orderNumber }}">
                                                    {{ $orderNumber }}
                                                </p>
                                                <p class="text-[11px] text-[#8a7265] mt-0.5">{{ $formattedOrderDate }}</p>
                                                <p class="mt-1 text-[11px] text-[#665044] truncate" title="{{ $itemNamePreview }}">
                                                    <span class="font-bold text-[#b45309]">Ordered:</span>
                                                    {{ $itemNamePreview }}
                                                </p>
                                            </td>
                                            <td class="px-5 py-4">
                                                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider {{ $statusClasses }}">
                                                    {{ str((string) ($order->status ?? 'completed'))->replace('_', ' ')->headline() }}
                                                </span>
                                            </td>
                                            <td class="px-5 py-4 font-bold text-[#2c221e]">
                                                {{ number_format($itemsCount) }} {{ Str::plural('cup', $itemsCount) }}
                                            </td>
                                            <td class="px-5 py-4 font-semibold text-[#665044]">
                                                <span class="inline-flex items-center gap-1 rounded-md bg-[#f4ebe3] px-2 py-0.5 text-[11px] font-bold text-[#73594b]">
                                                    {{ $order->payment_method === 'qr' ? 'KHQR' : str((string) ($order->payment_method ?? 'cash'))->replace('_', ' ')->headline() }}
                                                    @if ($currency = data_get($order->payment_details, 'currency'))
                                                        · {{ $currency === 'MIXED' ? 'USD + KHR' : $currency }}
                                                    @endif
                                                </span>
                                                @if ($reference = data_get($order->payment_details, 'reference'))
                                                    <p class="mt-1 max-w-44 truncate text-[10px] font-normal text-[#8a7265]" title="{{ $reference }}">Ref: {{ $reference }}</p>
                                                @endif
                                                @if ((float) data_get($order->payment_details, 'change_given', 0) > 0)
                                                    <p class="mt-1 text-[10px] font-normal text-[#8a7265]">Change:
                                                        {{ data_get($order->payment_details, 'change_currency') === 'KHR' ? '៛'.number_format(data_get($order->payment_details, 'change_given')) : '$'.number_format(data_get($order->payment_details, 'change_given'), 2) }}
                                                    </p>
                                                @endif
                                            </td>
                                            <td class="px-5 py-4 font-black text-sm text-[#b45309]">
                                                ${{ number_format((float) $order->total, 2) }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-5 py-12 text-center text-xs text-[#8a7265]">
                                                No order transactions found with the current filters.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>

                @if ($orders->hasPages())
                    <div class="mt-5">
                        {{ $orders->withQueryString()->links() }}
                    </div>
                @endif
            </main>

            <!-- Right Cart Sidebar -->
            @include('cashier.sidebar.cart', ['activeCashierMenu' => 'cart'])
        </div>
    </div>
@endsection
