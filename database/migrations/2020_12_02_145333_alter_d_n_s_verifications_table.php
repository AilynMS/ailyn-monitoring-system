<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterDNSVerificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('d_n_s_verifications', function (Blueprint $table) {
            $table->integer('interval')->default(1)->change(); // In minutes;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('d_n_s_verifications', function (Blueprint $table) {
            $table->integer('interval')->change(); // In minutes;
        });
    }
}
