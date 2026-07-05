<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppSetting extends Model
{
    public $incrementing = false;

    protected $primaryKey = 'key';

    protected $keyType = 'string';

    protected $fillable = ['key', 'value'];

    /**
     * Get a setting value by key.
     */
    public static function get(string $key, $default = null): ?string
    {
        $setting = self::find($key);

        return $setting ? $setting->value : $default;
    }

    /**
     * Set a setting value by key.
     */
    public static function set(string $key, ?string $value): void
    {
        self::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    /**
     * Get all organization settings as an associative array.
     */
    public static function getOrg(): array
    {
        $settings = self::where('key', 'like', 'org_%')->pluck('value', 'key')->toArray();

        return [
            'org_name' => $settings['org_name'] ?? config('organization.name'),
            'org_address' => $settings['org_address'] ?? config('organization.address'),
            'org_phone' => $settings['org_phone'] ?? config('organization.phone'),
            'org_tagline' => $settings['org_tagline'] ?? config('organization.tagline'),
            'org_city_default' => $settings['org_city_default'] ?? config('organization.city'),
        ];
    }
}
