<?php

namespace App\Services;

use Illuminate\Validation\ValidationException;

class PosPaymentService
{
    public function exchangeRate(): int
    {
        $rate = (int) config('payments.khr_per_usd', 4000);

        if ($rate < 1 || $rate > 100000) {
            throw ValidationException::withMessages([
                'payment' => 'The shop exchange rate must be between 1 and 100,000 KHR per USD.',
            ]);
        }

        return $rate;
    }

    public function settle(array $input, float $total): array
    {
        $rate = $this->exchangeRate();
        $totalCents = (int) round($total * 100);
        $method = $input['payment_method'];
        $details = ['exchange_rate' => $rate, 'base_currency' => 'USD'];

        if ($method === 'cash') {
            $usdCents = (int) round((float) ($input['amount_received'] ?? 0) * 100);
            $riel = (int) ($input['amount_received_khr'] ?? 0);
            // Integer hundredths of a riel retain the full value of both tenders.
            $received = $usdCents * $rate + $riel * 100;
            $due = $totalCents * $rate;

            if ($received < $due) {
                throw ValidationException::withMessages([
                    'amount_received' => 'Cash received is short. Add USD or KHR to cover the total.',
                ]);
            }

            $change = $received - $due;
            $changeCurrency = $input['change_currency'] ?? 'KHR';
            $changeUsd = intdiv($change + intdiv($rate, 2), $rate) / 100;
            $changeKhr = intdiv($change, 100);
            $details += [
                'currency' => $usdCents > 0 && $riel > 0 ? 'MIXED' : ($riel > 0 ? 'KHR' : 'USD'),
                'cash_usd' => $usdCents / 100,
                'cash_khr' => $riel,
                'change_currency' => $changeCurrency,
                'change_usd' => $changeUsd,
                'change_khr' => $changeKhr,
                'change_given' => $changeCurrency === 'KHR' ? $changeKhr : $changeUsd,
                'verification' => 'cashier',
            ];

            return [
                'amount_received' => intdiv($received + intdiv($rate, 2), $rate) / 100,
                'change_amount' => $changeUsd,
                'details' => $details,
            ];
        }

        if (! filter_var($input['payment_confirmed'] ?? false, FILTER_VALIDATE_BOOLEAN)) {
            throw ValidationException::withMessages([
                'payment_confirmed' => 'Confirm that the shop has received this payment before placing the order.',
            ]);
        }

        $reference = trim((string) ($input['payment_reference'] ?? ''));

        if ($reference === '') {
            throw ValidationException::withMessages([
                'payment_reference' => 'Enter the transaction reference from the merchant payment history.',
            ]);
        }

        $currency = $method === 'qr' ? ($input['payment_currency'] ?? 'KHR') : 'USD';
        $details += [
            'currency' => $currency,
            'paid_amount' => $currency === 'KHR' ? intdiv($totalCents * $rate + 99, 100) : $totalCents / 100,
            'reference' => $reference,
            'verification' => 'manual',
            'confirmed_at' => now()->toIso8601String(),
            'change_currency' => null,
            'change_given' => 0,
            'change_usd' => 0,
            'change_khr' => 0,
        ];

        return ['amount_received' => $totalCents / 100, 'change_amount' => 0.0, 'details' => $details];
    }
}
