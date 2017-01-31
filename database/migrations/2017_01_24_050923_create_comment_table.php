<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentTable extends Migration
{
    public $tableName = 'plh_comment';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->string('comment');
            $table->integer('parent_id')->comment('the comment to which has been replied');

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
