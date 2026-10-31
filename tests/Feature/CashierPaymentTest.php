<?php

use App\Models\CashierAttendance;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

beforeEach(function () {
    config(['payments.khr_per_usd' => 4000, 'payments.card_enabled' => false]);
    $cashier = User::factory()->create();
    CashierAttendance::query()->create([
        'cashier_id' => $cashier->id,
        'attended_on' => now()->toDateString(),
        'checked_in_at' => now(),
    ]);
    $this->actingAs($cashier);
    $product = Product::query()->create([
        'name' => 'Payment test coffee', 'slug' => 'payment-test-coffee',
        'price' => 2.75, 'price_small' => 2.75, 'is_active' => true,
    ]);
    $this->withSession(['cashier_cart' => [
        $product->id.'-small-50' => ['product_id' => $product->id, 'size' => 'small', 'sugar' => 50, 'qty' => 1],
    ]]);
});

test('cash checkout records dollar riel and mixed tenders with the shop rate', function (array $tender, string $currency, int $change) {
    $response = $this->postJson(route('cashier.order.place'), $tender + [
        'payment_method' => 'cash', 'exchange_rate' => 4000, 'change_currency' => 'KHR',
    ]);

    $response->assertOk()->assertJsonPath('ok', true)
        ->assertJsonPath('payment_details.currency', $currency)
        ->assertJsonPath('payment_details.change_given', $change)
        ->assertSessionMissing('cashier_cart');

    $order = Order::query()->sole();
    expect((float) $order->total)->toBe(2.75)
        ->and($order->payment_details['exchange_rate'])->toBe(4000)
        ->and($order->payment_status)->toBe('paid');
    $this->assertDatabaseHas('inventory_transactions', ['amount' => 2.75, 'type' => 'money_in']);
})->with([
    'USD' => [['amount_received' => '5.00'], 'USD', 9000],
    'KHR' => [['amount_received_khr' => 11000], 'KHR', 0],
    'mixed' => [['amount_received' => '1.00', 'amount_received_khr' => 10000], 'MIXED', 3000],
]);

test('cash shortage is rejected even if its USD equivalent would round up', function () {
    $this->postJson(route('cashier.order.place'), [
        'payment_method' => 'cash', 'amount_received' => '2.74', 'amount_received_khr' => 39, 'exchange_rate' => 4000,
    ])->assertUnprocessable()->assertJsonValidationErrors('amount_received');

    expect(Order::query()->count())->toBe(0);
    expect(DB::table('inventory_transactions')->count())->toBe(0);
});

test('cash change can be recorded in dollars', function () {
    $this->postJson(route('cashier.order.place'), [
        'payment_method' => 'cash', 'amount_received_khr' => 20000,
        'change_currency' => 'USD', 'exchange_rate' => 4000,
    ])->assertOk()->assertJsonPath('payment_details.change_currency', 'USD')
        ->assertJsonPath('payment_details.change_given', 2.25);
});

test('cash checkout validates precision and ignores a forged exchange rate', function (array $input, string $field) {
    $this->postJson(route('cashier.order.place'), $input + [
        'payment_method' => 'cash', 'amount_received' => 5, 'exchange_rate' => 4000,
    ])->assertUnprocessable()->assertJsonValidationErrors($field);
    expect(Order::query()->count())->toBe(0);
})->with([
    'fractional riel' => [['amount_received_khr' => 1000.5], 'amount_received_khr'],
    'fractional cents' => [['amount_received' => '5.001'], 'amount_received'],
    'negative tender' => [['amount_received_khr' => -100], 'amount_received_khr'],
    'forged or outdated rate' => [['exchange_rate' => 1], 'exchange_rate'],
]);

test('KHQR requires merchant confirmation and a transaction reference', function (array $input, string $field) {
    $this->postJson(route('cashier.order.place'), $input + [
        'payment_method' => 'qr', 'payment_currency' => 'KHR', 'exchange_rate' => 4000,
    ])->assertUnprocessable()->assertJsonValidationErrors($field);
    expect(Order::query()->count())->toBe(0);
})->with([
    'not confirmed' => [['payment_reference' => 'KHQR-123'], 'payment_confirmed'],
    'missing reference' => [['payment_confirmed' => true], 'payment_reference'],
    'blank reference' => [['payment_confirmed' => true, 'payment_reference' => '   '], 'payment_reference'],
]);

test('verified KHQR records the original payment currency and reference', function (string $currency, int|float $paid) {
    $this->postJson(route('cashier.order.place'), [
        'payment_method' => 'qr', 'payment_currency' => $currency,
        'payment_confirmed' => true, 'payment_reference' => 'KHQR-123', 'exchange_rate' => 4000,
    ])->assertOk()->assertJsonPath('payment_details.currency', $currency)
        ->assertJsonPath('payment_details.paid_amount', $paid)
        ->assertJsonPath('payment_details.verification', 'manual');

    $order = Order::query()->sole();
    expect($order->payment_details['reference'])->toBe('KHQR-123')
        ->and((float) $order->amount_received)->toBe(2.75)
        ->and((float) $order->change_amount)->toBe(0.0);

    $this->get(route('cashier.history'))->assertOk()->assertSee('KHQR-123')->assertSee($currency);
})->with(['riel' => ['KHR', 11000], 'dollars' => ['USD', 2.75]]);

test('a fractional riel conversion can be paid using the rounded up whole riel total', function () {
    config(['payments.khr_per_usd' => 4101]);
    $this->postJson(route('cashier.order.place'), [
        'payment_method' => 'cash', 'amount_received_khr' => 11278, 'exchange_rate' => 4101,
    ])->assertOk()->assertJsonPath('payment_details.cash_khr', 11278)
        ->assertJsonPath('payment_details.change_khr', 0);
});

test('an enabled card terminal still requires manual confirmation', function () {
    config(['payments.card_enabled' => true]);
    $this->postJson(route('cashier.order.place'), [
        'payment_method' => 'card', 'payment_reference' => 'CARD-123', 'payment_confirmed' => true, 'exchange_rate' => 4000,
    ])->assertOk()->assertJsonPath('payment_details.currency', 'USD')
        ->assertJsonPath('payment_details.verification', 'manual');
});

test('card checkout is unavailable unless a terminal is enabled', function () {
    $this->postJson(route('cashier.order.place'), [
        'payment_method' => 'card', 'payment_reference' => 'CARD-123', 'payment_confirmed' => true, 'exchange_rate' => 4000,
    ])->assertUnprocessable()->assertJsonValidationErrors('payment_method');
});

test('checkout and history render Cambodia payment labels', function () {
    $this->get(route('cashier.index'))->assertOk()->assertSee('KHQR')->assertSee('Cash received')->assertSee('Shop rate:');
    $this->get(route('cashier.history'))->assertOk();
});
