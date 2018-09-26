<?php

namespace hackerESQ\Settings\Providers;

use Illuminate\Support\ServiceProvider;
use Route;
use Illuminate\Http\Request;

class SettingsServiceProvider extends ServiceProvider {

	protected $defer = false;

	/**
     * Define routes
     *
     * @return void
     */
    public function boot()
    {

    	/**
		 * Publish settings config file
		 */
    	$this->publishes([
            __DIR__ . '/../../config/settings.php' => config_path('settings.php'),
        ]);

        /**
		 * Register migrations
		 */
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations/create_settings_tables.php');

		/**
		 * Settings API route
		 */
		Route::group(['middleware' => ['throttle:120','auth:api'], 'prefix'=>'api' ], function () {

			// Put updates to global settings 
			Route::put('settings', 'CaseTime\Settings\Controllers\SettingController@update');

		});
    }

	/**
     * Register services.
     *
     * @return void
     */
	public function register () {


		// bind 'settings' to the class named 'settings' in the IOC container
		$this->app->singleton('settings','CaseTime\Settings\Settings');



	}



}