<?php

namespace HackerESQ\Settings;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class Settings
{
    protected string $tenant = '';
    protected bool $force = false;

    /**
     * Set the tenant
     * @param string $tenant (optional)
     * @return Settings $this
     */
    public function tenant(string $tenant = '')
    {
        $this->tenant = $tenant;

        return $this;
    }

    /**
     * Should we force?
     * @param bool $force (optional)
     * @return Settings $this
     */
    public function force(bool $force = true)
    {
        $this->force = $force;

        return $this;
    }

    /**
     * Get settings from the database
     * @return array
     */
    private function resolveDB()
    {
        return DB::table(config('settings.table', 'settings'))
            ->where('tenant', $this->tenant)
            ->pluck('value', 'key')
            ->toArray();
    }

    /**
     * Get settings from the cache
     * @return array
     */
    private function resolveCache()
    {
        if (config('settings.cache', true)) {
            return Cache::rememberForever('settings' . $this->tenant, function () {
                return $this->resolveDB();
            });
        }

        return $this->resolveDB();
    }

    /**
     * Decrypt any settings that need to be decrypted
     * @param array $settings
     * @return array
     */
    private function decryptHandler(array $settings)
    {
        // DO WE NEED TO DECRYPT ANYTHING?
        foreach ($settings as $key => $value) {
            if (in_array($key, config('settings.encrypt', [])) && !empty($value)) {
                Arr::set($settings, $key, decrypt($value));
            }
        }
        return $settings;
    }

    /**
     * Encrypt any settings that need to be encrypted
     * @param array $settings
     * @return array
     */
    private function encryptHandler(array $settings)
    {
        // DO WE NEED TO ENCRYPT ANYTHING?
        foreach ($settings as $key => $value) {
            if (in_array($key, config('settings.encrypt', [])) && !empty($value)) {
                Arr::set($settings, $key, encrypt($value));
            }
        }
        return $settings;
    }

    /**
     * Upsert into the database
     * @param string $key
     * @param string $value
     * @return void
     */
    private function upsert(string $key, $value)
    {
        DB::table(config('settings.table', 'settings'))->updateOrInsert(
            [
                'key' => $key,
                'tenant' => $this->tenant
            ],
            [
                'key' => $key,
                'value' => is_array($value) ? json_encode($value) : $value,
                'tenant' => $this->tenant
            ]
        );
    }

    /**
     * Get value of settings by key
     * @param string|array $key (optional)
     * @param string|array $default (optional)
     * @return mixed string
     */
    public function get(string|array $key = NULL, string|array $default = NULL)
    {
        $settings = $this->decryptHandler($this->resolveCache());

        // no key passed, assuming get all settings
        if (is_null($key)) {
            // are we hiding everything?
            return (config('settings.hidden', []) == ['*'])
                ? [] // then return nothing.
                : array_merge(
                    $default ?? [],
                    Arr::except($settings, config('settings.hidden', []))
                );
        }

        // array of keys passed, return those settings only
        if (is_array($key)) {
            foreach ($key as $key) {
                $result[$key] = $settings[$key] ?? $default[$key] ?? NULL;
            }
            return $result;
        }

        // single key passed, return that setting only
        if (array_key_exists($key, $settings)) {

            return $settings[$key];
        }

        return $default;
    }

    /**
     * Check if a given key exists
     * @param string|array $needle
     * @return boolean
     */
    public function has(mixed $needle)
    {
        $settings = $this->decryptHandler($this->resolveCache());

        if (is_array($needle)) {
            foreach ($needle as $item) {
                if (!array_key_exists($item, $settings)) return false;
            }
            return true;
        }
        return array_key_exists($needle, $settings);
    }

    /**
     * Set value of setting
     * @param array $changes
     * @return boolean
     */
    public function set(array $changes)
    {
        $changes = $this->encryptHandler($changes);

        // Extracts only fillable key/values from array using fillable config
        if (config('settings.fillable', []) != ['*'] && !$this->force) {
            $changes = Arr::only($changes, config('settings.fillable', []));
        }

        foreach ($changes as $key => $value) {
            $this->upsert($key, $value);
        }

        // reset cache
        if (config('settings.cache', true)) {
            Cache::forget('settings' . $this->tenant);
        }

        return true;
    }
}
