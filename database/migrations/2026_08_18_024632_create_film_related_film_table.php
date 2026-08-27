<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('film_related_film', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('film_id');
            $table->unsignedBigInteger('related_film_id');
            $table->timestamps();

            $table->foreign('film_id')->references('id')->on('films')->cascadeOnDelete();
            $table->foreign('related_film_id')->references('id')->on('films')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('film_related_film');
    }
};
