<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->string('key');
            $table->text('value')->nullable();
            $table->primary('key');
        });

        // // Insert defaults
        // DB::table('settings')->insert(
        //     array(
        //         array('key' => 'firm_name', 'value' => env('APP_NAME') ),
        //         array('key' => 'firm_address', 'value' => '123 First Street'),
        //         array('key' => 'firm_address2', 'value' => ''),
        //         array('key' => 'firm_city', 'value' => 'Chicago'),
        //         array('key' => 'firm_state', 'value' => 'IL'),
        //         array('key' => 'firm_zip', 'value' => '60602'),
        //         array('key' => 'firm_tel', 'value' => '312-456-7890'),
        //         array('key' => 'firm_tel2', 'value' => ''),
        //         array('key' => 'firm_fax', 'value' => ''),
        //         array('key' => 'projects_matters_cases', 'value' => 'Projects'),
        //         array('key' => 'project_statuses', 'value' => 
        //             json_encode(
        //                 array(
        //                     'Investigation',
        //                     'Pleadings',
        //                     'Discovery',
        //                     'Settled',
        //                     'Dismissed',
        //                     'Closed'
        //                 )
        //             )
        //         ),
        //         array('key' => 'contact_types', 'value' => 
        //             json_encode(
        //                 array(
        //                     'Prospective Client',
        //                     'Client',
        //                     'Opposing Counsel',
        //                     'Adverse Party',
        //                     'Co-Litigant',
        //                     'Referring Attorney',
        //                     'Co-Counsel',
        //                     'Witness',
        //                     'Expert Witness',
        //                     'Insurer',
        //                     'Employer',
        //                     'Other'
        //                 )
        //             )
        //         ),
        //         array('key' => 'show_multiple_names', 'value' => 1),
        //         array('key' => 'textarea_height', 'value' => 7),
        //         array('key' => 'round_duration', 'value' => 1),
        //         array('key' => 'dark_theme_default', 'value' => 0),
        //         array('key' => 'allow_forgot_password', 'value' => 1),
        //         array('key' => 'google_client_id', 'value' => ''),
        //         array('key' => 'google_client_secret', 'value' => '')
        //     )
        // );
    }



    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings');
    }
}
