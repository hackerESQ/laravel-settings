<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Keys to automagically encrypt and decrypt
    |--------------------------------------------------------------------------
    |
    | This option controls the settings that will automagically be encrypted
    | and decrypted by the Super Simple Laravel Settings package. Expects  
    | an object or array of setting 'keys' to be passed here. 
    |
    */

    'encrypt' => [],


    /*
    |--------------------------------------------------------------------------
    | Force save settings
    |--------------------------------------------------------------------------
    |
    | This option controls whether settings are forced to be saved. If set to
    | true, this will save any settings without regard to security (i.e. 
    | whether the setting has been previously set).
    |
    */

    'force' => false,  


    /*
    |--------------------------------------------------------------------------
    | Cache settings
    |--------------------------------------------------------------------------
    |
    | This option controls whether key/values are cached or pulled directly
    | from the database. If this setting is true, cache is enabled.
    | If this setting is false, caching is disabled.
    |
    */

    'cache' => true,  

    /*
    |--------------------------------------------------------------------------
    | Hidden settings
    |--------------------------------------------------------------------------
    |
    | This option controls the settings that are hidden when using the get() 
    | method without any specific settings. This protects sensitive data
    | like API keys or other high-value user data.
    |
    */

    'hidden' => [],


];