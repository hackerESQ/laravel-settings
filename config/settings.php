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


];