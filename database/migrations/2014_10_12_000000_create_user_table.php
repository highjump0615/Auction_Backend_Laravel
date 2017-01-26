<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserTable extends Migration
{
    public static $tableName = 'plh_user';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(CreateUserTable::$tableName, function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->rememberToken();
            $table->string('api_token', 60)->nullable();

            // profile info
            $table->string('photo')->nullable()->comment('profile portrait url');
            $table->date('birthday')->nullable();
            $table->tinyInteger('gender')->comment('1:Unknown, 2:Female, 3:Male');

            $table->softDeletes();
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
        Schema::drop(CreateUserTable::$tableName);
    }
}
