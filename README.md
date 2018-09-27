# Settings
Super simple key/value settings for Laravel 5+ that natively supports [encryption](#encryption).

* [Installation](#installation)
* [Usage](#usage)
  * [Set new setting](#set-new-setting)
  * [Get all settings](#get-all-settings)
  * [Get single setting](#get-single-setting)
  * [Get certain setting](#get-certain-setting)
  * [Encryption](#encryption)
  
  
## Installation
This package can be used in Laravel 5.4 or higher.

You can install the package via composer:

``` bash
composer require hackeresq/settings
```

In Laravel 5.5+ the service provider will automatically get registered and you can skip this step. In older versions of the framework just add the service provider in `config/app.php` file:

```php
'providers' => [
    // ...
    hackerESQ\Settings\Providers\SettingsServiceProvider::class,
];
```
The same is true for the alias. If you're running Laravel 5.5+, you can also skip this step. In older versions of the framework just add the alias in `config/app.php` file:

```php
'aliases' => [
    // ...
    'Settings' => hackerESQ\Settings\Facades\Settings::class,
];
```

You can publish [the migration](https://github.com/hackerESQ/settings/blob/master/database/migrations/create_settings_table.php) and [config](https://github.com/hackerESQ/settings/blob/master/config/settings.php) file with:

```bash
php artisan vendor:publish --provider="hackerESQ\Settings\Providers\SettingsServiceProvider"
```

After the migration has been published you can create the setting key/value table by running:

```bash
php artisan migrate
```

<b>Success!</b> Settings is now installed!

## Usage

Settings can be accessed using the easy-to-remember Facade, "Settings."

### Set new setting
You can set new settings using the "set" method, which accepts an associative array of one or more key/value pairs.

```php
Settings::set(['firm_name'=>'new']);
```

This will save the new setting and cache it to minimize database queries. When you [get](#get-all-settings) settings, it will first try to retrieve the setting from the cache.

For security reasons, this will first check to see if such a setting key exists in your "settings" table or in the cache. If a key does exist, it will set it. If the key does not exist, it will disregard the change. 

If you want to force set a setting, you can pass true for the second parameter of the set method:

```php
Settings::set(['firm_name'=>'new'],true);
```

If you will be setting variables in the local or development environment and always want to force set settings in that environment, you can do something like this:

```php
Settings::set( ['firm_name'=>'new'] , env('APP_ENV') == 'local' ? true : false );
```


### Get all settings
You can return a list of all settings using, where no parameters are passed:

```php
Settings::get();
```

### Get single setting
You can return a single setting using, where the first parameter is a string that represents a setting 'key':

```php
Settings::get('firm_name');
```

### Get certain settings
You can return a list of particular settings using, where the first parameter is an array of setting 'keys':

```php
Settings::get(['firm_name','contact_types']);
```

## Encryption

You can define keys that should be encrypted automatically within the [config/settings.php](https://github.com/hackerESQ/settings/blob/master/config/settings.php) file. To do so, add the keys as such:

```php
'encrypt' => [
        'twitter_client_id',
        'twitter_client_secret',
    ],
```


