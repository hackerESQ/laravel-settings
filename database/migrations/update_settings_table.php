<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateSettingsTable extends Migration {

    /**
     * Add tenant column and make key non-unique.
     * See https://github.com/hackerESQ/laravel-settings/pull/7#issuecomment-543347711
     * https://github.com/hackerESQ/laravel-settings/issues/8
     * https://laravel.com/docs/6.x/migrations#column-modifiers 
     *
     * @return void
     */
    public function up() {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('tenant')->default(\hackerESQ\Settings\Settings::DEFAULT_TENANT)->index();
            $table->dropPrimary();
            $table->primary(['key', 'tenant']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropPrimary();
            $table->dropColumn(['tenant']);
            $table->primary('key');
        });
    }

}
