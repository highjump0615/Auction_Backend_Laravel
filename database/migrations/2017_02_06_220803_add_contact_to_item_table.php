<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddContactToItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(CreateItemTable::$tableName, function (Blueprint $table) {
            $table->integer('contact')->comment('1:Single, 2:Both');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(CreateItemTable::$tableName, function (Blueprint $table) {
            $table->dropColumn('contact');
        });
    }
}
