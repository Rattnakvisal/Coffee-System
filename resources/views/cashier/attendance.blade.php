@extends('layouts.app')

@section('content')
    @php
        $cashierRows = collect($cashierRows ?? []);
        $attendanceFeedback = session('status') ?: ($errors->has('attendance') ? $errors->first('attendance') : '');
        $attendanceFeedbackIsError = !session('status') && $errors->has('attendance');
    @endphp

    <div class="anim-enter-up w-full min-h-screen overflow-hidden bg-[#fbf9f6] lg:overflow-visible">
        <div class="grid min-h-screen grid-cols-1 lg:grid-cols-12">
            <!-- Mobile Overlay -->
            <div data-cashier-overlay class="fixed inset-0 z-40 hidden bg-[#1f1713]/50 backdrop-blur-xs lg:hidden"></div>
            
            <!-- Left Navigation Sidebar -->
            @include('cashier.sidebar.sidebar', ['activeCashierMenu' => 'attendance'])

            <!-- Main Workspace -->
            <main data-attendance-page
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

                <!-- Feedback Banner -->
                <div data-attendance-feedback @class([
                    'mb-5 flex items-center gap-3 rounded-2xl border px-4 py-3.5 text-xs font-semibold shadow-xs',
                    'hidden' => $attendanceFeedback === '',
                    'border-emerald-200/80 bg-emerald-50/90 text-emerald-800' =>
                        $attendanceFeedback !== '' && !$attendanceFeedbackIsError,
                    'border-rose-200/80 bg-rose-50/90 text-rose-800' =>
                        $attendanceFeedback !== '' && $attendanceFeedbackIsError,
                ])>
                    <span>{{ $attendanceFeedback }}</span>
                </div>

                <!-- Attendance Card Section -->
                <section
                    class="rounded-[28px] border border-[#ebdcd2]/80 bg-white p-5 sm:p-6 shadow-[0_4px_24px_rgba(43,33,27,0.04)]">
                    <div class="mb-5 flex flex-wrap items-center justify-between gap-3 pb-4 border-b border-[#ebdcd2]/60">
                        <div>
                            <span class="inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-[#b45309]">
                                <span class="h-1.5 w-1.5 rounded-full bg-[#b45309]"></span>
                                Cashier Shifts
                            </span>
                            <h3 class="mt-1 text-2xl font-black tracking-tight text-[#2c221e]">Daily Attendance</h3>
                        </div>
                        <span class="rounded-full bg-[#f4ebe3] px-3.5 py-1 text-xs font-bold text-[#73594b]">
                            {{ number_format($cashierRows->count()) }} {{ Str::plural('Cashier', $cashierRows->count()) }} Scheduled
                        </span>
                    </div>

                    <div class="overflow-hidden rounded-2xl border border-[#ebdcd2]/80 bg-white">
                        <div class="overflow-x-auto custom-scrollbar">
                            <table class="min-w-[700px] w-full text-left text-xs">
                                <thead class="bg-[#fcfaf7] text-[#73594b] border-b border-[#ebdcd2]">
                                    <tr>
                                        <th class="px-5 py-3.5 font-bold uppercase tracking-wider text-[11px]">Cashier Staff</th>
                                        <th class="px-5 py-3.5 font-bold uppercase tracking-wider text-[11px]">Status</th>
                                        <th class="px-5 py-3.5 font-bold uppercase tracking-wider text-[11px]">Check-In Time</th>
                                        <th class="px-5 py-3.5 font-bold uppercase tracking-wider text-[11px]">Date</th>
                                        <th class="px-5 py-3.5 text-right font-bold uppercase tracking-wider text-[11px]">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-[#ebdcd2]/60">
                                    @forelse ($cashierRows as $row)
                                        @php
                                            $cashier = $row['cashier'] ?? null;
                                            $todayAttendance = $row['todayAttendance'] ?? null;
                                            $isChecked = $todayAttendance !== null;
                                            $cashierName = (string) ($row['name'] ?? '-');
                                            $avatarInitial = strtoupper(substr($cashierName, 0, 1));
                                        @endphp
                                        <tr data-attendance-card data-cashier-id="{{ (int) ($cashier?->id ?? 0) }}"
                                            class="align-middle transition-colors hover:bg-[#fcfaf7] {{ $isChecked ? 'bg-emerald-50/30' : 'bg-white' }}">
                                            <td class="px-5 py-4">
                                                <div class="flex min-w-0 items-center gap-3">
                                                    <span data-attendance-avatar-badge
                                                        class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl {{ $isChecked ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }} text-sm font-black shadow-2xs">
                                                        {{ $avatarInitial !== '' ? $avatarInitial : 'C' }}
                                                    </span>
                                                    <div class="min-w-0">
                                                        <p class="truncate font-bold text-sm text-[#2c221e]">{{ $cashierName }}</p>
                                                        <p class="truncate text-[11px] text-[#8a7265]">{{ (string) ($row['email'] ?? '-') }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-5 py-4">
                                                <span data-attendance-status-badge
                                                    class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-[10px] font-bold uppercase tracking-wider {{ $isChecked ? 'bg-emerald-100 text-emerald-800 ring-1 ring-emerald-200' : 'bg-amber-100 text-amber-800 ring-1 ring-amber-200' }}">
                                                    <span class="h-1.5 w-1.5 rounded-full {{ $isChecked ? 'bg-emerald-600' : 'bg-amber-600' }}"></span>
                                                    {{ $isChecked ? 'Checked In' : 'Pending Check-in' }}
                                                </span>
                                            </td>
                                            <td class="px-5 py-4">
                                                <p data-attendance-time class="font-extrabold text-[#2c221e]">
                                                    {{ $todayAttendance?->checked_in_at?->format('H:i:s') ?? '--:--:--' }}
                                                </p>
                                            </td>
                                            <td class="px-5 py-4">
                                                <p data-attendance-date class="font-bold text-[#665044]">
                                                    {{ $todayAttendance?->attended_on?->format('d M Y') ?? now()->format('d M Y') }}
                                                </p>
                                            </td>
                                            <td class="px-5 py-4">
                                                <form method="POST" action="{{ route('cashier.attendance.check') }}"
                                                    class="js-attendance-check-form flex justify-end">
                                                    @csrf
                                                    <input type="hidden" name="redirect" value="attendance">
                                                    <input type="hidden" name="cashier_id"
                                                        value="{{ (int) ($cashier?->id ?? 0) }}">
                                                    <button type="submit" data-attendance-submit
                                                        @disabled($isChecked || !$cashier)
                                                        class="inline-flex min-w-36 items-center justify-center rounded-xl px-4 py-2 text-xs font-bold text-white shadow-xs transition active:scale-95 disabled:cursor-not-allowed {{ $isChecked ? 'bg-emerald-600 opacity-90' : 'bg-[#2c221e] hover:bg-[#43342e]' }}">
                                                        <span data-attendance-submit-label>
                                                            {{ $isChecked ? '✓ Attendance Checked' : 'Check Attendance' }}
                                                        </span>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-5 py-12 text-center text-xs text-[#8a7265]">
                                                No cashier records found for today.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
            </main>

            <!-- Right Cart Sidebar -->
            @include('cashier.sidebar.cart', ['activeCashierMenu' => 'cart'])
        </div>
    </div>
@endsection
