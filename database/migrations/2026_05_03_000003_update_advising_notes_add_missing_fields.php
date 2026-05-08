<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('advising_notes', function (Blueprint $table) {
            $table->string('title')->nullable()->after('user_id');

            $table->boolean('follow_up_required')->default(false)->after('content');

            $table->enum('note_type', ['Academic', 'Behavioral'])->default('Academic')->after('title');
        });
    }

    public function down(): void
    {
        Schema::table('advising_notes', function (Blueprint $table) {
            $table->dropColumn(['title', 'follow_up_required', 'note_type']);
        });
    }
};
