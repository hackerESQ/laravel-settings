<?php

namespace hackerESQ\Settings;

use DB;
use Cache;

class Settings
{

    /**
     * Get settings from the cache
     * @param string $tenant
     * @return array
     */
    public function resolveCache($tenant) {

        if (config('settings.cache')) {
            return Cache::rememberForever('settings'.$tenant, function () use ($tenant) {
                return DB::table('settings')->where('tenant','=',$tenant)->pluck('value', 'key')->toArray();
            });
        } else {
            return DB::table('settings')->where('tenant','=',$tenant)->pluck('value', 'key')->toArray();
        }
        
    }

    /**
     * Decrypt any settings that need to be decrypted
     * @param array $settings
     * @return array
     */
    public function decryptHandler($settings) {

        // DO WE NEED TO DECRYPT ANYTHING?
        foreach ($settings as $key => $value) {
            if ( in_array( $key, config('settings.encrypt',[]) ) && !empty($value) ) {
                array_set($settings, $key, decrypt($settings[$key]));
            }
        }

        return $settings;

    }

    /**
     * Get value of settings by key
     * @param  string  $key
     * @param  array  $options
     * @return mixed string|boolean
     */
    public function get($key = NULL, $options = [])
    {
        // is this multitenant? 
        $tenant = isset($options['tenant']) ? $options['tenant'] : '';

        $settings = $this->decryptHandler($this->resolveCache($tenant));

        // no key passed, assuming get all settings
        if ($key == NULL) {
            
            return $settings;
        }
        
        // array of keys passed, return those settings only
        if (is_array($key)) {
            foreach ($key as $key) {
                $result[] = $settings[$key];
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
     * @param  string  $key
     * @param  array  $options
     * @return boolean
     */
    public function has($key, $options = [])
    {

        // is this multitenant? 
        $tenant = isset($options['tenant']) ? $options['tenant'] : '';
        
        $settings = $this->decryptHandler($this->resolveCache($tenant));

        return array_key_exists($key, $settings);
    }

    /**
     * Set value of setting
     * @param  array  $changes
     * @param  array  $options
     * @return boolean
     */
    public function set($changes, $options = [])
    {

        $force = isset($options['force']) ? $options['force'] : false;
        $encrypt = isset($options['encrypt']) ? $options['encrypt'] : false;

        // is this multitenant? 
        $tenant = isset($options['tenant']) ? $options['tenant'] : '';

        // DO WE NEED TO ENCRYPT ANYTHING?
        foreach ($changes as $key => $value) {
            if ( ( in_array($key, config('settings.encrypt') ) || $encrypt ) && !empty($value)) {
                array_set($changes, $key, encrypt($value));
            }
        }

        // ARE WE FORCING? OR SHOULD WE BE SECURE?
        if (config('settings.force') || $force) {

            foreach ($changes as $key => $value) {

                DB::table('settings')->where([
                    ['key', '=', $key],
                    ['tenant', '=', $tenant]
                    ])->delete();    

                DB::table('settings')->insert([
                    'key'=>$key,
                    'value'=>$value,
                    'tenant'=>$tenant
                ]); 
            }

        } else {

            $settings = $this->resolveCache($tenant);

            // array_only() - will return only the specified key-value pairs from the given array
                // array_keys() - will return all the keys or a subset of the keys of an array
                    // this passes array_keys() to array_only() to give current/valid settings only
                        //checks and see if passed settings are  valid options
            foreach (array_only($changes, array_keys($settings)) as $key => $value) {
                DB::table('settings')->where([
                    ['key', '=', $key],
                    ['tenant', '=', $tenant]
                ])->update(['value'=>$value]); 
            }
        }

        if (config('settings.cache')) {
            Cache::forget('settings'.$tenant);
        }

        return true;

    }

}

