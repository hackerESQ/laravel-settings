<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use HackerESQ\Settings\Facades\Settings;

class SettingController extends Controller
{
    public function update(Request $request) {

        /*
        |
        |   This is an example controller to update settings. You
        |   can expose this through a route file as necessary. 
        |
        */

        $this->validate($request, [
              'firm_name'=>'string',
        ]);

        Settings::set( $request->all() );

        return array("message"=>"success");

	}
}
