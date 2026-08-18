<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('diagnoses', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('crop_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('disease_id')->nullable()->constrained()->nullOnDelete();
            $table->string('image_path');
            $table->string('predicted_label')->nullable();
            $table->decimal('confidence', 5, 2)->default(0);
            $table->text('recommendation_snapshot')->nullable();
            $table->string('status')->default('completed');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diagnoses');
    }
};
