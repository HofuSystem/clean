<?php

namespace Core\Orders\Services;

use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ZatcaService
{
    /**
     * Generate ZATCA compliant TLV encoding.
     * 
     * @param string $sellerName
     * @param string $vatNumber
     * @param string $timestamp
     * @param float $total
     * @param float $vatAmount
     * @return string Base64 encoded TLV
     */
    public function generateTlvString(string $sellerName, string $vatNumber, string $timestamp, float $total, float $vatAmount): string
    {
        $tlv = $this->toTlv(1, $sellerName);
        $tlv .= $this->toTlv(2, $vatNumber);
        $tlv .= $this->toTlv(3, $timestamp);
        $tlv .= $this->toTlv(4, (string) number_format($total, 2, '.', ''));
        $tlv .= $this->toTlv(5, (string) number_format($vatAmount, 2, '.', ''));

        return base64_encode($tlv);
    }

    /**
     * Convert key, value to TLV format.
     * 
     * @param int $tag
     * @param string $value
     * @return string
     */
    private function toTlv(int $tag, string $value): string
    {
        return chr($tag) . chr(strlen($value)) . $value;
    }

    /**
     * Generate QR Code as base64 image.
     * 
     * @param string $qrText
     * @return string
     */
    public function generateQrCode(string $qrText): string
    {
        return QrCode::size(100)->margin(1)->generate($qrText);
    }
}
