<?php

namespace HackerESQ\Settings\Facades;

use Illuminate\Support\Facades\Facade;

class Settings extends Facade {


	protected static function getFacadeAccessor() {


		return 'settings';

	}


}