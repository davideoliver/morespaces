<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('levels', function (Blueprint $table): void {
            $table->id();
            $table->unsignedSmallInteger('number')->unique();
            $table->string('name', 120);
            $table->string('summary', 255)->nullable();
            $table->text('description');
            $table->enum('danger_level', ['safe', 'low', 'moderate', 'high', 'lethal'])
                ->default('moderate')
                ->index();
            $table->foreignId('created_by')
                ->nullable()
                ->index()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('levels');
    }
};
