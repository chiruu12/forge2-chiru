<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->string('subject');
            $table->text('description')->nullable();
            $table->string('status')->default('open');
            $table->string('priority')->default('low');
            $table->foreignId('requester_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('assignee_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('organization_id');
            $table->index('status');
            $table->index('priority');
            $table->index('assignee_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
