<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBidTable extends Migration
{
    public $tableName = 'plh_bid';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->increments('id');
            $table->string('price');

            $table->timestamps();

            $table->unsignedInteger('user_id');
            $table->unsignedInteger('item_id');

            // foreign key
            $table->foreign('user_id')->references('id')
                ->on(CreateUserTable::$tableName)
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('item_id')->references('id')
                ->on(CreateItemTable::$tableName)
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop($this->tableName);
    }
}