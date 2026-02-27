<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('student_id')->unique();
            $table->string('name_ar');
            $table->string('name_en')->nullable();
            $table->string('major');
            $table->decimal('gpa', 3, 2);
            $table->integer('total_credits');
            $table->enum('status', ['منتظم', 'متعثر', 'خريج']);
            $table->unsignedBigInteger('advisor_id');
            $table->foreign('advisor_id')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
