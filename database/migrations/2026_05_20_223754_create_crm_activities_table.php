<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Polymorphic communication history. An activity (note/call/email/meeting/task)
 * attaches to any CRM subject — a lead, client, or deal — giving a single
 * timeline per relationship.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->morphs('subject'); // subject_type + subject_id (lead|client|deal)
            $table->string('type')->default('note'); // note|call|email|meeting|task
            $table->string('title')->nullable();
            $table->text('body')->nullable();
            $table->string('direction')->nullable(); // inbound|outbound for calls/emails
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
