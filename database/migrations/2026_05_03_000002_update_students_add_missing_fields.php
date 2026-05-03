<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // إضافة major (موجود في التوثيق، غائب في الكود)
            $table->string('major')->nullable()->after('name_en');

            // academic_status كما في التوثيق (Regular/Warning) — status الحالية تُبقى للعرض العربي
            $table->enum('academic_status', ['Regular', 'Warning'])->default('Regular')->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['major', 'academic_status']);
        });
    }
};
