<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeVersionColumnToSNMPVerifications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('s_n_m_p_verifications', function (Blueprint $table) {
            $table->string('version')->default('Version1')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('s_n_m_p_verifications', function (Blueprint $table) {
            $table->string('version')->default('Version3')->change();
        });
    }
}
