<?php

namespace App\Support;

use Illuminate\Support\Str;

class PixPayload
{
    public static function make(
        string $key,
        string $receiverName,
        string $receiverCity,
        float $amount,
        string $transactionId
    ): string {
        $merchantAccount = self::field('00', 'BR.GOV.BCB.PIX')
            . self::field('01', trim($key));

        $additionalData = self::field('05', self::sanitize($transactionId, 25, '***'));

        $payload = self::field('00', '01')
            . self::field('26', $merchantAccount)
            . self::field('52', '0000')
            . self::field('53', '986')
            . self::field('54', number_format($amount, 2, '.', ''))
            . self::field('58', 'BR')
            . self::field('59', self::sanitize($receiverName, 25, 'LOJA'))
            . self::field('60', self::sanitize($receiverCity, 15, 'BRASIL'))
            . self::field('62', $additionalData)
            . '6304';

        return $payload . self::crc16($payload);
    }

    private static function field(string $id, string $value): string
    {
        return $id . str_pad((string) strlen($value), 2, '0', STR_PAD_LEFT) . $value;
    }

    private static function sanitize(string $value, int $limit, string $fallback): string
    {
        $value = Str::of($value)->ascii()->upper()->replaceMatches('/[^A-Z0-9 ]/', '')->squish()->toString();

        return substr($value ?: $fallback, 0, $limit);
    }

    private static function crc16(string $payload): string
    {
        $crc = 0xFFFF;

        for ($offset = 0; $offset < strlen($payload); $offset++) {
            $crc ^= ord($payload[$offset]) << 8;

            for ($bit = 0; $bit < 8; $bit++) {
                $crc = ($crc & 0x8000) ? (($crc << 1) ^ 0x1021) : ($crc << 1);
                $crc &= 0xFFFF;
            }
        }

        return strtoupper(str_pad(dechex($crc), 4, '0', STR_PAD_LEFT));
    }
}
