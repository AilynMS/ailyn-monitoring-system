<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnDataTypeToSNMPVerifications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('s_n_m_p_verifications', function (Blueprint $table) {
            $table->string('data_type');
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
            $table->dropColumn('data_type');
        });
    }
}
