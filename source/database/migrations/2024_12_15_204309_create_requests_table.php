<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('requests', function (Blueprint $table) {
            $table->uuid()->primary();
            $table->string('result', 255);
            $table->enum('status', ['uploading', 'pending', 'processing', 'finished']);
            $table->json('stats')->default(new Expression("'{}'::json"));
            $table->timestamps();
        });
        DB::statement('ALTER TABLE requests ALTER COLUMN uuid SET DEFAULT uuid_generate_v4();');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requests');
    }
};
