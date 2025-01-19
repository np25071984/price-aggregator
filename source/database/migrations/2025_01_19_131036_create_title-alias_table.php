<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('title-alias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('title_id')->constrained();
            $table->string('alias', 255);
            $table->unsignedSmallInteger('size');
            $table->timestamp('last_used_at')->nullable();
            $table->unique(['title_id', 'alias']);
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('title-alias');
    }
};
