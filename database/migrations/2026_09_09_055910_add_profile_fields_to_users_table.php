<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar')->nullable();
            $table->string('phone')->nullable();
            $table->string('education')->nullable();
            $table->text('skills')->nullable();
            $table->text('bio')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['avatar', 'phone', 'education', 'skills', 'bio']);
        });
    }
};