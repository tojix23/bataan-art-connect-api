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
        Schema::create('personal_information', function (Blueprint $table) {
            $table->id(); // Primary key: ID
            $table->string('first_name'); // First Name
            $table->string('last_name'); // Last Name
            $table->text('main_address'); // Main Address
            $table->text('sub_address')->nullable(); // Sub Address (nullable)
            $table->string('occupation')->nullable(); // Occupation (nullable)
            $table->string('role')->nullable(); // Role (nullable)
            $table->string('gender')->nullable(); // Gender (male, female, other)
            $table->string('contact_number')->unique(); // Contact Number
            $table->date('birthdate'); // Birthdate
            $table->string('email')->unique(); // Email (unique)
            $table->string('type')->nullable(); // Type (nullable)
            $table->timestamps(); // Created_at and Updated_at
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
        Schema::dropIfExists('personal_information');
    }
};
