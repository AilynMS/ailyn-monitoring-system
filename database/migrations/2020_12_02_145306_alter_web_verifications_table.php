<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterWebVerificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('web_verifications', function (Blueprint $table) {
            $table->boolean('https')->default(true)->change();
            $table->integer('interval')->default(1)->change(); // In minutes
            $table->longText('response_codes')->nullable()->default(404)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('web_verifications', function (Blueprint $table) {
            $table->boolean('https')->default(false)->change();
            $table->integer('interval')->change(); // In minutes
            $table->longText('response_codes')->nullable()->change();
        });
    }
}
