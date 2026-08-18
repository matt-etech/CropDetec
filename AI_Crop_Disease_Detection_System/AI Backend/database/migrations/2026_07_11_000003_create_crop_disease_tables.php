<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crops', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->unique();
            $table->string('scientific_name')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('diseases', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('crop_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('class_label')->unique();
            $table->text('description')->nullable();
            $table->text('symptoms')->nullable();
            $table->text('prevention')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('treatments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('disease_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('instructions');
            $table->string('type')->default('general');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('treatments');
        Schema::dropIfExists('diseases');
        Schema::dropIfExists('crops');
    }
};
