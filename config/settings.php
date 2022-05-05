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
    | Keys for fillable settings
    |--------------------------------------------------------------------------
    |
    | This option defines which settings are considered "safe" and should be
    | written to the database. You can use ["*"] to make all settings 
    | fillable. This is unadvisable as any settings will be filled.
    |
    */

    'fillable' => [],  


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
    | like API keys or other high-value user data. You can use ["*"]
    | to make all settings hidden unless specifically requested.
    |
    */

    'hidden' => [],

    /*
    |--------------------------------------------------------------------------
    | Keys to cast 
    |--------------------------------------------------------------------------
    |
    | This option controls the settings that are cast to native types
    | by default. Currently you can choose 'json' or 'boolean' -
    | in future, other types will be made available.
    |
    */

    'cast' => [],

];