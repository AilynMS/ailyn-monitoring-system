<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSNMPVerificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('s_n_m_p_verifications', function (Blueprint $table) {
            $table->id();
            $table->string('token')->unique();
            $table->string('name');
            $table->string('description')->nullable();
            $table->boolean('status')->default(false);
            $table->boolean('ipv6')->default(false);
            $table->string('target');
            $table->string('oid');
            $table->integer('interval'); // In minutes
            $table->string('version')->default('Version3');
            $table->integer('port')->nullable();
            $table->string('template');
            $table->string('community');

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
        Schema::dropIfExists('s_n_m_p_verifications');
    }
}
