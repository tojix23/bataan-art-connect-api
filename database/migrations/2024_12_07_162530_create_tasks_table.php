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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            // Task details
            $table->integer('creator_acc_id');
            $table->string('creator_name');
            $table->integer('assignee_acc_id');
            $table->string('assignee_name');
            $table->string('title'); // Task title
            $table->text('description')->nullable(); // Detailed task description
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending'); // Task status
            $table->dateTime('due_date')->nullable(); // Optional deadline for task completion
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
        Schema::dropIfExists('tasks');
    }
};
