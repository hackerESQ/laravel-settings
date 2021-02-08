<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateSettingsTable extends Migration
{
    /**
     * Add tenant column and make key non-unique
     *
     * @return void
     */
    public function up()
    {
        Schema::table('settings', function (Blueprint $table) {

            $table->string('key')->index()->change();
            $table->string('tenant')->nullable();
            $table->dropPrimary('key');
            $table->dropUnique('settings_key_unique');
            
            $table->primary(['key', 'tenant']);
            $table->unique(['key', 'tenant']);
            
        });
     
    }



    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
