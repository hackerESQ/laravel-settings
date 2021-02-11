<?php

namespace HackerESQ\Settings\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static object force(bool|null $force = true)
 * @method static object tenant(string|null $tenant = null)
 * @method static mixed get(mixed $key = null)
 * @method static boolean has(string|array $needle)
 * @method static boolean set(array $changes)
 *
 * @see \HackerESQ\Settings\Settings
 */
class Settings extends Facade {


	protected static function getFacadeAccessor() {


		return 'settings';

	}


}