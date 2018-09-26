<?php

namespace hackerESQ\Settings\Controllers;

use Illuminate\Http\Request;
use Settings;
use App\Http\Controllers\Controller;


class SettingController extends Controller
{
    public function update(Request $request) {

		$this->validate($request, [
            'firm_name'=>'string',
            'contact_types' => 'json',
            'project_statuses' => 'json',
            'projects_matters_cases' => 'string',
        ]);

        
        Settings::set( $request->all() , env('APP_DEBUG') );

        


        return array("message"=>"success");

	}
}
