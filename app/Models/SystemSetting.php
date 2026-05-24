<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
    ];

    public static function setValue(string $key, mixed $value): void
    {
        self::updateOrCreate(
            ['key' => $key],
            ['value' => $value === null ? null : (string) $value]
        );
    }

    public static function setMany(array $settings): void
    {
        foreach ($settings as $key => $value) {
            self::setValue($key, $value);
        }
    }
}
