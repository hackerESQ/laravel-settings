<?php

namespace HackerESQ\Settings;

use Illuminate\Support\ServiceProvider;

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
            __DIR__ . '/../config/settings.php' => config_path('settings.php'),
        ], 'config');


        /**
         * Publish settings migration
         */
        $this->publishes([
            __DIR__.'/../database/migrations/2020_04_01_100000_create_settings_table.php' => $this->app->databasePath()."/migrations/2020_04_01_100000_create_settings_table.php",
            __DIR__.'/../database/migrations/2020_04_01_100001_update_settings_table.php' => $this->app->databasePath()."/migrations/2020_04_01_100001_update_settings_table.php",
        ], 'migrations');

    }

	/**
     * Register services.
     *
     * @return void
     */
	public function register () {

		// bind 'settings' to the class named 'settings' in the IOC container
		$this->app->singleton('settings','HackerESQ\Settings\Settings');

	}

}
