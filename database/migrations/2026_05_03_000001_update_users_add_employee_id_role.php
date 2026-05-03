<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // موجود بـ "phone" — نضيف employee_id و faculty_role كما في التوثيق
            $table->string('employee_id')->unique()->nullable()->after('email');
            $table->enum('faculty_role', ['Advisor', 'Chair', 'Dean'])->default('Advisor')->after('employee_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['employee_id', 'faculty_role']);
        });
    }
};
