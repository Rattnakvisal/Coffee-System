<form method="POST" action="{{ route('cashier.order.place') }}" class="js-place-order-form mt-5 space-y-4"
    data-order-total="{{ number_format($cartTotal, 2, '.', '') }}" data-exchange-rate="{{ $exchangeRate }}"
    data-cart-empty="{{ $cartItems->isEmpty() ? '1' : '0' }}">
    @csrf
    <input type="hidden" name="exchange_rate" value="{{ $exchangeRate }}">

    <fieldset class="min-w-0 rounded-2xl border border-[#ebdcd2] bg-white p-4 shadow-xs disabled:opacity-60" @disabled($cartItems->isEmpty())>
        <legend class="sr-only">Payment / ការទូទាត់</legend>
        <div class="mb-3 flex items-center justify-between gap-2">
            <h4 class="text-[11px] font-bold uppercase tracking-wider text-[#73594b]">Payment method</h4>
            <span class="text-[11px] text-[#8a7265]">ការទូទាត់</span>
        </div>

        <div class="grid grid-cols-2 gap-2">
            @foreach ([['cash', 'banknote', 'Cash', 'សាច់ប្រាក់ · USD / ៛'], ['qr', 'qr-code', 'KHQR', 'បាគង · Bank apps']] as [$value, $icon, $label, $subtitle])
                <label class="payment-option">
                    <input type="radio" name="payment_method" value="{{ $value }}" class="peer sr-only" @checked($value === 'cash')>
                    <span class="payment-option-card">
                        <x-icon :name="$icon" class="mb-2 h-5 w-5" />
                        <span class="block text-sm font-extrabold">{{ $label }}</span>
                        <span class="mt-0.5 block text-[10px] font-medium leading-relaxed">{{ $subtitle }}</span>
                        <span class="payment-option-check" aria-hidden="true"><x-icon name="check" class="h-2.5 w-2.5" /></span>
                    </span>
                </label>
            @endforeach
            @if (config('payments.card_enabled'))
                <label class="payment-option col-span-2">
                    <input type="radio" name="payment_method" value="card" class="peer sr-only">
                    <span class="payment-option-card flex items-center gap-2"><x-icon name="credit-card" class="h-4 w-4" /><span class="text-xs font-bold">Card terminal</span></span>
                </label>
            @endif
        </div>

        <fieldset data-cash-panel class="mt-4 min-w-0 space-y-3">
            <legend class="sr-only">Cash received</legend>
            <p class="text-[11px] leading-relaxed text-[#8a7265]">Enter cash received. You can combine dollars and riel.</p>
            <div class="grid grid-cols-2 gap-2.5">
                <div>
                    <label for="cash-usd" class="payment-label">US dollars</label>
                    <div class="relative mt-1.5">
                        <span class="payment-currency-symbol">$</span>
                        <input id="cash-usd" name="amount_received" type="number" min="0" max="999999.99" step="0.01"
                            inputmode="decimal" placeholder="0.00" class="payment-input pl-8" data-cash-usd>
                    </div>
                    <button type="button" data-exact="USD" class="payment-text-button">Exact USD</button>
                </div>
                <div>
                    <label for="cash-khr" class="payment-label">រៀល / KHR</label>
                    <div class="relative mt-1.5">
                        <span class="payment-currency-symbol">៛</span>
                        <input id="cash-khr" name="amount_received_khr" type="number" min="0" max="999999999" step="1"
                            inputmode="numeric" placeholder="0" class="payment-input pl-8" data-cash-khr>
                    </div>
                    <button type="button" data-exact="KHR" class="payment-text-button">Exact riel</button>
                </div>
            </div>
            <div class="flex flex-wrap gap-1.5" aria-label="Quick cash amounts">
                @foreach ([['USD', 5, '$5'], ['USD', 10, '$10'], ['KHR', 10000, '៛10,000'], ['KHR', 20000, '៛20,000']] as [$currency, $amount, $label])
                    <button type="button" data-cash-quick="{{ $currency }}" data-amount="{{ $amount }}" class="payment-quick">{{ $label }}</button>
                @endforeach
            </div>
            <div class="rounded-xl border border-[#ebdcd2] bg-[#fbf8f5] p-3" aria-live="polite" aria-atomic="true">
                <div class="flex items-center justify-between gap-2 text-[11px] text-[#8a7265]">
                    <span>Cash received</span><span data-cash-equivalent class="font-bold text-[#2c221e]">$0.00</span>
                </div>
                <div class="mt-2 flex items-center justify-between gap-2">
                    <label for="change-currency" class="text-xs font-bold text-[#2c221e]">ប្រាក់អាប់ / Change</label>
                    <select id="change-currency" name="change_currency" class="rounded-md border border-[#ebdcd2] bg-white px-2 py-1 text-[11px] font-bold text-[#705547]" data-change-currency>
                        <option value="KHR">KHR ៛</option><option value="USD">USD $</option>
                    </select>
                </div>
                <p data-change-amount class="mt-1 text-xl font-black tracking-tight text-[#2c221e]">៛0</p>
                <p data-cash-status class="mt-1 text-[11px] leading-relaxed text-[#8a7265]">Enter the customer's cash to continue.</p>
            </div>
        </fieldset>

        <fieldset data-digital-panel class="mt-4 min-w-0 space-y-3" hidden disabled>
            <legend class="sr-only">Verify electronic payment</legend>
            <div data-khqr-panel>
                <div class="mb-3 flex items-center justify-between gap-2">
                    <label for="payment-currency" class="payment-label">Collect payment in</label>
                    <select id="payment-currency" name="payment_currency" data-payment-currency class="rounded-lg border border-[#ebdcd2] bg-[#fbf8f5] px-2.5 py-1.5 text-xs font-bold text-[#705547]">
                        <option value="KHR">KHR ៛</option><option value="USD">USD $</option>
                    </select>
                </div>
                <div class="overflow-hidden rounded-xl border border-[#efd4d1] bg-[#fffaf9]">
                    <div class="flex items-center justify-between bg-[#b91c1c] px-3 py-2 text-white">
                        <span class="text-xs font-black tracking-widest">KHQR</span><span class="text-[10px]">Bakong</span>
                    </div>
                    <div class="p-3 text-center">
                        <p class="truncate text-xs font-bold text-[#2c221e]">{{ config('payments.khqr.merchant_name') }}</p>
                        <p data-digital-amount class="mt-1 text-2xl font-black text-[#2c221e]">៛{{ number_format($totalKhr) }}</p>
                        @foreach (['KHR' => 'khr_image', 'USD' => 'usd_image'] as $currency => $imageKey)
                            <div data-qr-currency="{{ $currency }}" @if ($currency === 'USD') hidden @endif>
                                @if ($image = config('payments.khqr.'.$imageKey))
                                    <img src="{{ $image }}" alt="{{ $currency }} KHQR for {{ config('payments.khqr.merchant_name') }}"
                                        class="mx-auto mt-3 max-h-52 max-w-full rounded-lg object-contain">
                                    <p class="mt-2 text-[10px] text-[#8a7265]">Scan with a KHQR-supported banking app.</p>
                                @else
                                    <div class="mt-3 flex items-center gap-2.5 rounded-lg border border-dashed border-[#e8c9c3] bg-white p-3 text-left">
                                        <x-icon name="qr-code" class="h-6 w-6 shrink-0 text-[#b91c1c]" />
                                        <p class="text-[11px] leading-relaxed text-[#73594b]">Ask the customer to scan the shop's <strong>{{ $currency }} KHQR stand</strong> and enter the amount above.</p>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
                <p class="mt-2 text-center text-[10px] text-[#8a7265]">ABA · ACLEDA · Wing · other KHQR apps</p>
            </div>
            <div data-card-panel hidden class="rounded-xl bg-[#fbf5f0] p-3 text-xs leading-relaxed text-[#705547]">Collect <strong>${{ number_format($cartTotal, 2) }}</strong> on the card terminal, then enter its transaction reference.</div>
            <div>
                <label for="payment-reference" class="payment-label">Transaction reference <span class="text-rose-600">*</span></label>
                <input id="payment-reference" name="payment_reference" type="text" maxlength="100" autocomplete="off"
                    placeholder="From merchant transaction history" data-payment-reference class="payment-input mt-1.5 px-3" required>
            </div>
            <label class="flex cursor-pointer items-start gap-2.5 rounded-xl border border-emerald-200 bg-emerald-50/60 p-3">
                <input type="checkbox" name="payment_confirmed" value="1" data-payment-confirmed required class="mt-0.5 h-4 w-4 shrink-0 accent-emerald-700">
                <span class="text-[11px] leading-relaxed text-emerald-900"><strong class="block">Payment received / បានទទួលប្រាក់</strong>I checked the shop's bank app and verified the amount and reference.</span>
            </label>
            <p class="text-[10px] leading-relaxed text-[#8a7265]">Cashier verification required. Confirm from the merchant account before completing the sale.</p>
        </fieldset>
    </fieldset>

    <p data-place-order-feedback role="status" aria-live="polite" class="hidden rounded-xl px-3 py-2.5 text-xs font-semibold leading-relaxed"></p>
    <button type="submit" data-place-order
        class="group inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-[#e07a3c] to-[#b45309] py-4 text-sm font-bold text-white shadow-lg shadow-orange-900/15 transition hover:from-[#d16c2e] hover:to-[#92400e] active:scale-[0.98] disabled:cursor-not-allowed disabled:opacity-45"
        @disabled($cartItems->isEmpty())>
        <x-icon name="check-circle" class="h-5 w-5" /><span data-place-order-label>Complete sale</span>
    </button>
    <p class="text-center text-[10px] text-[#9b8577]">USD is used for sales reports · Riel uses the shop rate</p>
</form>
