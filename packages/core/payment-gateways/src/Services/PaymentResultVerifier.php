<?php

namespace Core\PaymentGateways\Services;

use Core\PaymentGateways\Models\PaymentTransaction;
use UnexpectedValueException;

class PaymentResultVerifier
{
    public function verify(PaymentTransaction $transaction, array $data): void
    {
        $reference = $data['CustomerReference'] ?? null;
        $invoiceId = $data['InvoiceId'] ?? null;
        if (! is_scalar($reference) || (string) $reference !== (string) $transaction->transaction_id
            || ! is_scalar($invoiceId) || (string) $invoiceId === '') {
            throw new UnexpectedValueException('Payment reference does not match the local transaction.');
        }

        $storedId = (string) $transaction->gateway_transaction_id;
        if ($storedId !== '' && $storedId !== (string) $invoiceId) {
            // Older callbacks stored PaymentId in this column instead of InvoiceId.
            $legacyIds = array_map(fn ($item) => (string) ($item['PaymentId'] ?? ''), $data['InvoiceTransactions'] ?? []);
            if (! in_array($storedId, $legacyIds, true)) {
                throw new UnexpectedValueException('Payment invoice does not match the local transaction.');
            }
        }

        $amount = $this->minorUnits($transaction->amount);
        if ($amount <= 0 || $this->minorUnits($data['InvoiceValue'] ?? null) !== $amount) {
            throw new UnexpectedValueException('Payment amount does not match the local transaction.');
        }

        // This integration creates SAR invoices. Match display currency as well as base value.
        $displayValue = $data['InvoiceDisplayValue'] ?? null;
        if (! is_string($displayValue)
            || ! preg_match('/^([0-9,.]+)\s+(SAR|SR)$/i', trim($displayValue), $matches)
            || $this->minorUnits(str_replace(',', '', $matches[1])) !== $amount) {
            throw new UnexpectedValueException('Payment display amount or currency does not match SAR.');
        }
    }

    private function minorUnits(mixed $value): int
    {
        if (! is_scalar($value) || ! preg_match('/^(\d{1,12})(?:\.(\d+))?$/', (string) $value, $parts)) {
            throw new UnexpectedValueException('Invalid payment amount.');
        }
        $fraction = rtrim($parts[2] ?? '', '0');
        if (strlen($fraction) > 2) {
            throw new UnexpectedValueException('Invalid payment precision.');
        }

        return ((int) $parts[1] * 100) + (int) str_pad($fraction, 2, '0');
    }
}
