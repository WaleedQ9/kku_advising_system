<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('drop_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('advisor_id')->references('id')->on('users')->onDelete('cascade');
            $table->enum('status', ['Completed', 'Rejected'])->default('Completed');
            $table->text('reason')->nullable();
            // snapshot نتيجة فحص الـ policy وقت الطلب
            $table->json('eligibility_check_result')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drop_actions');
    }
};
