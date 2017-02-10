<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGiveupToBidTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(CreateBidTable::$tableName, function (Blueprint $table) {
            $table->dateTime('giveup_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(CreateBidTable::$tableName, function (Blueprint $table) {
            $table->dropColumn('giveup_at');
        });
    }
}
