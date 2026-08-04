<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // 'category' або 'static'
            $table->foreignId('category_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('static_key')->nullable(); // 'home', 'selections', 'actors', 'directors', 'genres'
            $table->unsignedInteger('position')->default(0); // порядок у меню
            $table->timestamps();
        });

    }


    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
