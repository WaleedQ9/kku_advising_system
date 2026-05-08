<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add onDelete cascade to attendances foreign keys
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['student_id']);
            $table->dropForeign(['course_id']);

            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
        });

        // Add composite index on risk_flags for the frequent query:
        // WHERE student_id = ? AND is_resolved = false
        Schema::table('risk_flags', function (Blueprint $table) {
            $table->index(['student_id', 'is_resolved'], 'risk_flags_student_resolved_index');
        });
    }

    public function down(): void
    {
        Schema::table('risk_flags', function (Blueprint $table) {
            $table->dropIndex('risk_flags_student_resolved_index');
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['student_id']);
            $table->dropForeign(['course_id']);

            $table->foreign('student_id')->references('id')->on('students');
            $table->foreign('course_id')->references('id')->on('courses');
        });
    }
};
