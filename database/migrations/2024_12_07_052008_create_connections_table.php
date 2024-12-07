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
        Schema::create('connections', function (Blueprint $table) {
            $table->id();
            $table->integer('acc_id');       // Account ID (the user initiating the connection)
            $table->integer('connected_id'); // The ID of the connected account (the other user in the connection)
            $table->enum('status', ['pending', 'accepted', 'blocked'])->default('pending'); // Status of the connection
            $table->boolean('delete')->default(0); //
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
        Schema::dropIfExists('connections');
    }
};
