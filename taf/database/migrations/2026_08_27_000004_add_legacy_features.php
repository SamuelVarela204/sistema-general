<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('usuarios', 'notificaciones')) {
            Schema::table('usuarios', function (Blueprint $table) {
                $table->boolean('notificaciones')->default(false)->after('estado');
            });
        }

        if (! Schema::hasTable('password_reset_tokens')) {
            Schema::create('password_reset_tokens', function (Blueprint $table) {
                $table->string('email')->primary();
                $table->string('token');
                $table->timestamp('created_at')->nullable();
            });
        }

        if (! Schema::hasTable('global_settings')) {
            Schema::create('global_settings', function (Blueprint $table) {
                $table->unsignedTinyInteger('id')->primary();
                $table->binary('glob_wall')->nullable();
                $table->string('glob_mime', 50)->nullable();
            });
            DB::table('global_settings')->insert(['id' => 1]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('global_settings');
        Schema::dropIfExists('password_reset_tokens');
        if (Schema::hasColumn('usuarios', 'notificaciones')) {
            Schema::table('usuarios', fn (Blueprint $table) => $table->dropColumn('notificaciones'));
        }
    }
};