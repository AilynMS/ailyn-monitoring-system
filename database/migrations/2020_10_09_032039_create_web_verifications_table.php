<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWebVerificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('web_verifications', function (Blueprint $table) {
            $table->id();
            $table->string('token')->unique();
            $table->string('name');
            $table->string('description')->nullable();
            $table->boolean('status')->default(false);
            $table->boolean('ipv6')->default(false);
            $table->boolean('https')->default(false);
            $table->string('target');
            $table->integer('port')->nullable();
            $table->integer('interval'); // In minutes
            $table->longText('headers')->nullable(); // Json
            $table->longText('response_codes')->nullable(); // Json

            $table->tenant();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('web_verifications');
    }
}
