<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('crops', function (Blueprint $table): void {
            $table->string('name_sn')->nullable()->after('name');
            $table->text('description_sn')->nullable()->after('description');
        });

        Schema::table('diseases', function (Blueprint $table): void {
            $table->string('name_sn')->nullable()->after('name');
            $table->text('description_sn')->nullable()->after('description');
            $table->text('symptoms_sn')->nullable()->after('symptoms');
            $table->text('prevention_sn')->nullable()->after('prevention');
        });

        Schema::table('treatments', function (Blueprint $table): void {
            $table->string('title_sn')->nullable()->after('title');
            $table->text('instructions_sn')->nullable()->after('instructions');
        });
    }

    public function down(): void
    {
        Schema::table('treatments', function (Blueprint $table): void {
            $table->dropColumn(['title_sn', 'instructions_sn']);
        });

        Schema::table('diseases', function (Blueprint $table): void {
            $table->dropColumn(['name_sn', 'description_sn', 'symptoms_sn', 'prevention_sn']);
        });

        Schema::table('crops', function (Blueprint $table): void {
            $table->dropColumn(['name_sn', 'description_sn']);
        });
    }
};
