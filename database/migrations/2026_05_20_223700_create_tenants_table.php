<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tenants are the top-level isolation boundary. Every business-domain table
 * carries a `tenant_id` (see the BelongsToTenant trait) and is auto-scoped.
 * Cashier billing columns (stripe_id, trial_ends_at, ...) are added to this
 * table by the create_customer_columns migration that runs right after.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();                 // subdomain: acme.novabiz.ai
            $table->string('domain')->nullable()->unique();   // optional custom domain
            $table->string('plan')->default('starter');       // current plan tier
            $table->string('timezone')->default('UTC');
            $table->string('currency', 3)->default('USD');
            $table->string('logo_path')->nullable();
            $table->json('settings')->nullable();             // branding, locale, feature flags
            $table->json('limits')->nullable();               // resolved plan limits cache
            $table->boolean('is_active')->default(true);
            $table->timestamp('onboarded_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_active', 'plan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
