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
        Schema::create('artist_packages', function (Blueprint $table) {
            $table->id();
            $table->integer('acc_id');
            $table->string('package_name');
            $table->text('package_desc');
            $table->string('amount');
            $table->boolean('is_active');
            $table->string('image_attach')->nullable();
            $table->boolean('delete')->default(0);
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
        Schema::dropIfExists('artist_packages');
    }
};
