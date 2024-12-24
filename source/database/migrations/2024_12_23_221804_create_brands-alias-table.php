<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('brands-alias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->constrained();
            $table->string('alias', 255)->unique();
            $table->unsignedSmallInteger('size');
            $table->timestamp('last_used_at')->nullable();
        });

        DB::statement('ALTER TABLE "brands-alias" ADD COLUMN "stop-words" text[] DEFAULT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('brands-alias', function (Blueprint $table) {
            $table->dropForeign(['brand_id']);
        });
        Schema::dropIfExists('brands-alias');
    }
};
