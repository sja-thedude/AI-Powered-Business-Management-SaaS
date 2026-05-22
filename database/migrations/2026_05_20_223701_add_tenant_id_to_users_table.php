<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * A user belongs to exactly one tenant (workspace). The platform super-admin
 * is the single user with a null tenant_id. `position` and `phone` round out
 * the employee-style profile used across HR/CRM.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->after('id')
                ->constrained()->nullOnDelete();
            $table->string('position')->nullable()->after('email');
            $table->string('phone')->nullable()->after('position');
            $table->string('avatar_path')->nullable()->after('phone');
            $table->string('timezone')->default('UTC')->after('avatar_path');
            $table->boolean('is_active')->default(true)->after('timezone');
            $table->timestamp('last_login_at')->nullable()->after('is_active');

            $table->index(['tenant_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('tenant_id');
            $table->dropColumn([
                'position', 'phone', 'avatar_path', 'timezone', 'is_active', 'last_login_at',
            ]);
        });
    }
};
