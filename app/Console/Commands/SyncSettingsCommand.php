<?php

namespace App\Console\Commands;

use Ahs12\Setanjo\Facades\Settings;
use App\Enums\SettingKey;
use Illuminate\Console\Command;

class SyncSettingsCommand extends Command
{
    protected $signature = 'settings:sync';
    protected $description = 'Sync settings from the SettingKey enum';

    public function handle()
    {
        foreach (SettingKey::cases() as $settingKey) {
            $key = $settingKey->value;
            Settings::set($key, $settingKey->defaultValue());
        }
        $this->info('Settings sync completed!');
    }
}
