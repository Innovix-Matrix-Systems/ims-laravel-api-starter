<?php

namespace App\Enums;

enum SettingKey: string
{
    case SYSTEM_NAME = 'system_name';
    case NOTIFICATION_ENABLED = 'notification_enabled';

    // Add more settings as needed

    public function defaultValue(): mixed
    {
        return match ($this) {
            self::SYSTEM_NAME => 'IMS API STARTER TEMPLATE',
            self::NOTIFICATION_ENABLED => true,
            // Add defaults for other settings
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::SYSTEM_NAME => 'System name',
            self::NOTIFICATION_ENABLED => 'Enable/disable notifications',
            // Add descriptions for other settings
        };
    }

    public function type(): string
    {
        return match ($this) {
            self::SYSTEM_NAME => 'string',
            self::NOTIFICATION_ENABLED => 'boolean',
            // Add types for other settings (boolean, integer, string, json, etc.)
        };
    }
}
