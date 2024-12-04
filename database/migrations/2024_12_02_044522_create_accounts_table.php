<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->integer('personal_id'); // fk personal id 
            $table->string('fullname'); // Full name
            $table->string('type'); // type of user
            $table->string('password'); // password
            $table->string('email'); // email which verified
            $table->string('email_verified_at'); // email which verified
            $table->boolean('is_verify')->default(0); // verified account
            $table->boolean('is_disable')->default(0); // Type (nullable)
            $table->boolean('is_cancel')->default(0); // Type (nullable)
            $table->timestamps();
            $table->boolean('delete')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accounts');
    }
};
