<?php

namespace Core\Wallet\Services;

use Core\PaymentGateways\Models\PaymentTransaction;
use Core\Wallet\Models\WalletPackage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class WalletChargePricing
{
    public function quote(mixed $amount, mixed $packageId = null, bool $requireActive = true): array
    {
        $paid = $this->money($amount);
        $credit = $paid;
        if ($packageId !== null && $packageId !== '') {
            Validator::make(['check_id' => $packageId], ['check_id' => 'required|integer|min:1'])->validate();
            $package = WalletPackage::query()->when($requireActive, fn ($query) => $query->where('status', 'active'))->find($packageId);
            if (! $package || $this->money($package->price) !== $paid) {
                throw ValidationException::withMessages(['amount' => __('The wallet package is unavailable or its price has changed.')]);
            }
            $credit = $this->money($package->value);
            $packageId = $package->id;
        } else {
            $packageId = null;
        }

        return ['version' => 1, 'paid_amount' => $paid, 'credit_amount' => $credit, 'package_id' => $packageId];
    }

    public function ledgerData(PaymentTransaction $transaction): array
    {
        $metadata = $transaction->payment_data ? json_decode($transaction->payment_data, true, 512, JSON_THROW_ON_ERROR) : [];
        $quote = $metadata['wallet_quote'] ?? null;
        if ($quote === null) {
            // Old links have no server snapshot: require their paid amount to match
            // the current package price. Never trust credit metadata from request_data.
            $request = json_decode($transaction->request_data, true, 512, JSON_THROW_ON_ERROR);
            $quote = $this->quote($transaction->amount, $request['check_id'] ?? null, false);
        }
        if (! is_array($quote) || ($quote['version'] ?? null) !== 1
            || $this->money($quote['paid_amount'] ?? null) !== $this->money($transaction->amount)) {
            throw ValidationException::withMessages(['amount' => __('Invalid wallet payment details.')]);
        }

        $packageId = $quote['package_id'] ?? null;
        if ($packageId !== null && ! WalletPackage::withTrashed()->whereKey($packageId)->exists()) {
            // Keep the purchased credit even if the package was permanently removed.
            $packageId = null;
        }

        return [
            'amount' => $this->money($quote['credit_amount'] ?? null),
            'transaction_id' => $transaction->transaction_id,
            'type' => 'deposit',
            'package_id' => $packageId,
        ];
    }

    private function money(mixed $amount): string
    {
        Validator::make(['amount' => $amount], ['amount' => 'required|numeric|gt:0|max:99999999.99|decimal:0,2'])->validate();

        return number_format((float) $amount, 2, '.', '');
    }
}
