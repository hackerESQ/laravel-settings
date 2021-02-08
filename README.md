# Settings

[![Latest Version on Packagist](https://img.shields.io/packagist/v/hackerESQ/laravel-settings.svg?style=flat-square)](https://packagist.org/packages/hackerESQ/laravel-settings)
[![Total Downloads](https://img.shields.io/packagist/dt/hackerESQ/laravel-settings.svg?style=flat-square)](https://packagist.org/packages/hackerESQ/laravel-settings)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)

Super simple key/value settings for Laravel 5.4+ that natively supports [encryption](#encryption) and [multi-tenancy](#multi-tenancy).

* [Installation](#installation)
* [Usage](#usage)
  * [Set new setting](#set-new-setting)
  * [Get all settings](#get-all-settings)
  * [Get single setting](#get-single-setting)
  * [Get certain setting (via array)](#get-certain-settings)
  * [Check if a setting is set](#check-if-a-setting-is-set)
* [Encryption](#encryption)
* [Multi-tenancy](#multi-tenancy)
* [Disable cache](#disable-cache)
* [Hidden settings](#hidden-settings)
* [Customize table name](#customize-table-name)
  
  
## Installation
This package can be used in Laravel 5.4 or higher.

You can install the package via composer:

``` bash
composer require hackeresq/laravel-settings
```

Since Laravel 5.5+, service providers and aliases will automatically get registered and you can skip this step. To use this package with older versions, please use release < 2.0.

You can publish [the migration](https://github.com/hackerESQ/settings/blob/master/database/migrations/create_settings_table.php) and [config](https://github.com/hackerESQ/settings/blob/master/config/settings.php) files, then migrate the new settings table all in one go, using:

```bash
php artisan vendor:publish --provider="HackerESQ\Settings\SettingsServiceProvider" --tag=migrations && php artisan vendor:publish --provider="HackerESQ\Settings\SettingsServiceProvider" --tag=config && php artisan migrate
```

<b>Success!</b> laravel-settings is now installed!

## Usage

Settings can be accessed using the easy-to-remember Facade, `Settings`.

### Set new setting
You can set new settings using the "set" method, which accepts an associative array of one or more key/value pairs. <b><mark>For security reasons,</mark></b> this will first check to see if such a setting key is "fillable," which is a configuration option in the [config/settings.php](https://github.com/hackerESQ/settings/blob/master/config/settings.php) file. 

If such a key exists in the config, it will update the key to the new value passed. If the key does not exist in the fillable config, <i>it will disregard the change.</i> So, if this is a fresh install, do not expect the following to work:

```php
Settings::set(['firm_name'=>'new']);
```

It will not set the new setting until you have either set the fillable fields in the config, or you have opted to force the setting. If you wish to force set a new setting, you should use the `force()` method before calling the `set()` method:

```php
Settings::force()->set(['firm_name'=>'new']);
```

As of version 3.0.4, the global override for forcing settings has been removed from the config file for this package. Instead, you can use a wildcard for the fillable property, like this:

```php
'fillable' => ['*']
```

This is more in line with standard Laravel syntax (e.g. for models).

### Get all settings
If no parameters are passed to the "get" method, it will return an array of all settings:

```php
Settings::get();
```

You can optionally hide specific settings using the `hidden` config as described [below](#hidden-settings). 

### Get single setting
You can return a single setting by passing a single setting key:

```php
Settings::get('firm_name');
```

### Get certain settings
You can also return a list of specified settings by passing an array of setting keys:

```php
Settings::get(['firm_name','contact_types']);
```

### Check if a setting is set
Sometimes you can't know if a setting has been set or not (i.e. boolean settings that will return false if the setting does not exist, but also if the setting has been set to false).

```php
Settings::has(['dark_theme']);
```

## Encryption

You can define keys that should be encrypted automatically within the [config/settings.php](https://github.com/hackerESQ/settings/blob/master/config/settings.php) file. To do so, add the keys as such:

```php
'encrypt' => [
        'twitter_client_id',
        'twitter_client_secret',
    ],
```

## Multi-tenancy
This package can be used in a multi-tenant environment. The [set](#set-new-setting), [get](#get-all-settings), and [has](#check-if-a-setting-is-set) methods all read an internal 'tenant' attribute that can be set with the `tenant()` method. You can set the 'tenant' attribute by calling the `tenant()` method first, like this:

```php
Settings::tenant('tenant_name')->set(['firm_name'=>'foo bar']);

// returns true (i.e. successfully set `firm name`)

```

```php
Settings::tenant('tenant_name')->get('firm_name');

// returns 'foo bar'

```

```php
Settings::tenant('tenant_name')->has('firm_name');

// returns true

```

The 'tenant' attribute passed can be any alphanumeric string. The 'tenant' attribute can also be left blank to have, for example, settings saved to a so-called "central" tenant. Note: the 'tenant' attribute is not strictly typed, and will be passed to the database query as a string. 

## Disable cache
Depending on your use case, you may like to disable the cache (enabled by default). Disable the cache by modifying the [config/settings.php](https://github.com/hackerESQ/settings/blob/master/config/settings.php) file as such:

```php
'cache' => false
```

## Hidden settings

You may wish to hide specific settings (like API keys or other sensitive user data) from inadvertent disclosure. You can set these settings in the [config/settings.php](https://github.com/hackerESQ/settings/blob/master/config/settings.php) file. To do so, add the keys as such:

```php
'hidden' => [
        'twitter_client_secret',
        'super_secret_password',
    ],
```

Once these are set, they must be specifically requested using the `get()` method. In other words, this acts like the `$hidden` attribute on Laravel Eloquent models.

In addition to hiding specific settings, you can opt to hide ALL the settings (unless specifically requested, of course). To do this, you can use a wildcard:

```php
'hidden' => ['*'],
```

## Customize table name

For some cases, it may be necessary to customize the name of the table where settings are stored. By default, the migrations that come with this package create a 'settings' table. If, for some reason, it becomes necessary to change the default table, you can set the 'table' option in the [config/settings.php](https://github.com/hackerESQ/settings/blob/master/config/settings.php) file, like this:

```php
'table' => 'user_options_table',
```

This configuration option is not included in the base config file as this is an edge case that is not commonly encountered; but nonetheless a nice convenience to have when it does come up.

## Finally
### Testing
You can run tests with the `composer test` command.

### Contributing
Feel free to create a fork and submit a pull request if you would like to contribute.

### Bug reports
Raise an issue on GitHub if you notice something broken.
