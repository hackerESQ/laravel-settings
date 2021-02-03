<?php

namespace hackerESQ\Settings;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class Settings
{
    protected string $tenant = '';

    /**
     * Get settings from the database
     * @return array
     */
    public function resolveDB() 
    {
        return DB::table(config('settings.table','settings'))->where('tenant',$this->tenant)->pluck('value', 'key')->toArray();
    }

    /**
     * Set the tenant
     * @param string $tenant (optional)
     * @return Settings $this
     */
    public function tenant($tenant='') 
    {
        $this->tenant = $tenant;

        return $this;
    }

    /**
     * Get settings from the cache
     * @return array
     */
    public function resolveCache() 
    {
        if (config('settings.cache',true)) {
            return Cache::rememberForever('settings'.$this->tenant, function () {
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
    public function decryptHandler($settings) 
    {
        // DO WE NEED TO DECRYPT ANYTHING?
        foreach ($settings as $key => $value) {
            if ( in_array( $key, config('settings.encrypt',[]) ) && !empty($value) ) {
                Arr::set($settings, $key, decrypt($settings[$key]));
            }
        }

        return $settings;
    }

    /**
     * Get value of settings by key
     * @param string $key 
     * @return mixed string|boolean
     */
    public function get($key = NULL)
    {
        $settings = $this->decryptHandler($this->resolveCache());

        // no key passed, assuming get all settings
        if ($key == NULL) 
            return (config('settings.hidden',[]) == ['*']) ? // are we hiding everything?
                    [] : // then return nothing.
                    Arr::except($settings,config('settings.hidden',[])); // else, return everything else
        
        // array of keys passed, return those settings only
        if (is_array($key)) {
            foreach ($key as $key) {
                $result[$key] = $settings[$key];
            }
            return $result;
        }

        // single key passed, return that setting only
        if (array_key_exists($key, $settings)) {

            return $settings[$key]; 
        } 

        return false;
    }

    /**
     * Check if a given key exists
     * @param string $key
     * @return boolean
     */
    public function has($key)
    {
        $settings = $this->decryptHandler($this->resolveCache());

        return array_key_exists($key, $settings);
    }

    /**
     * Set value of setting
     * @param array $changes
     * @param array $options (optional)
     * @return boolean
     */
    public function set($changes, $options = [])
    {
        $force = $options['force'] ?? false;
        $encrypt = $options['encrypt'] ?? false;

        // DO WE NEED TO ENCRYPT ANYTHING?
        foreach ($changes as $key => $value) {
            if ( ( in_array($key, config('settings.encrypt',[]) ) || $encrypt ) && !empty($value)) {
                Arr::set($changes, $key, encrypt($value));
            }
        }

        // ARE WE FORCING? OR SHOULD WE BE SECURE?
        if (config('settings.force',false) || $force) {
            foreach ($changes as $key => $value) {
                DB::table(config('settings.table','settings'))->updateOrInsert([
                    'key'=>$key,
                    'tenant'=>$this->tenant
                ],
                [
                    'key'=>$key,
                    'value'=>$value,
                    'tenant'=>$this->tenant
                ]); 
            }
        } else {
            $settings = $this->resolveCache();

            // array_only() - will return only the specified key-value pairs from the given array
                // array_keys() - will return all the keys or a subset of the keys of an array
                    // this passes array_keys() to array_only() to give current/valid settings only
                        //checks and see if passed settings are  valid options
            foreach (Arr::only($changes, array_keys($settings)) as $key => $value) {
                DB::table(config('settings.table','settings'))->where([
                    ['key', '=', $key],
                    ['tenant', '=', $this->tenant]
                ])->update(['value'=>$value]); 
            }
        }

        // clear cache
        if (config('settings.cache',true)) {
            Cache::forget('settings'.$this->tenant);
        }

        return true;
    }
}