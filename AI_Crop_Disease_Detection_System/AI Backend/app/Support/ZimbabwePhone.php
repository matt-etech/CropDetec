<?php

namespace App\Support;

class ZimbabwePhone
{
    public const REGEX = '/^\+2637[1378]\d{7}$/';

    public static function normalize(?string $phone): ?string
    {
        if ($phone === null || trim($phone) === '') {
            return null;
        }

        $phone = preg_replace('/[\s()\-]/', '', trim($phone));

        if (str_starts_with($phone, '0')) {
            return '+263'.substr($phone, 1);
        }

        if (str_starts_with($phone, '263')) {
            return '+'.$phone;
        }

        return $phone;
    }

    public static function rules(bool $sometimes = false): array
    {
        return [
            ...($sometimes ? ['sometimes'] : []),
            'nullable',
            'string',
            'regex:'.self::REGEX,
        ];
    }
}
