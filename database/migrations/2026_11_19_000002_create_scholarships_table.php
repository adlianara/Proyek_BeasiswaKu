<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('scholarships', function (Blueprint $table) {
            $table->id(); $table->string('name', 180); $table->string('slug')->unique(); $table->string('provider', 180);
            $table->enum('level', ['D3', 'D4', 'S1', 'S2', 'S3']); $table->date('deadline');
            $table->string('summary', 500); $table->longText('description'); $table->json('requirements')->nullable();
            $table->json('benefits')->nullable(); $table->string('application_url', 2048)->nullable();
            $table->boolean('is_active')->default(true); $table->timestamps();
            $table->index(['is_active', 'deadline']); $table->index(['level', 'provider']);
        });
    }
    public function down(): void { Schema::dropIfExists('scholarships'); }
};
