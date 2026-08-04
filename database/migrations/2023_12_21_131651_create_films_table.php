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
        Schema::create('films', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 255)->unique();
            $table->string('slug')->unique();
            $table->string('origin_title')->nullable();
            $table->string('duration')->nullable();
            $table->text('other_actor')->nullable();
            $table->text('note')->nullable();
            $table->longText('description')->nullable();

            $table->foreignId('author_id')->nullable()->constrained(
                table: 'users', indexName: 'films_author_id_foreign'
            );

            $table->integer('category_id')->unsigned()->nullable();
            $table->integer('year_id')->unsigned()->nullable();
            $table->integer('season_id')->unsigned()->nullable();
            $table->integer('rating_id')->unsigned()->nullable();
            $table->integer('status_id')->unsigned()->nullable();
            $table->integer('age_id')->unsigned()->nullable();
            $table->integer('quality_id')->unsigned()->nullable();
            $table->integer('duration_id')->unsigned()->nullable();

            $table->integer('view')->unsigned()->default(0);

            $table->string('thumbnail')->nullable();
            $table->string('tmdb_poster')->nullable();


            // ID відео на YouTube (не повний URL — зберігаємо тільки ID, витягуємо його при збереженні)
            $table->string('trailer_youtube_id')->nullable();
            // Шлях до власного відеофайлу (якщо трейлер завантажений вручну, а не з YouTube)
            $table->string('trailer_file')->nullable();

            $table->string('gal_image1')->nullable();
            $table->string('gal_image2')->nullable();
            $table->string('gal_image3')->nullable();
            $table->string('gal_image4')->nullable();
            $table->string('gal_image5')->nullable();




            $table->date('datepicker')->nullable();

            $table->string('publish_status')->default('draft');    //Чернетка
            /*$table->integer('is_featured')->default(0);*/  //Обрані пости
            $table->boolean('is_featured')->default(false);

            $table->integer('tmdb_id')->nullable()->unique();

            $table->string('imdb_id')->nullable();
            $table->decimal('imdb_rating', 3, 1)->nullable();


            $table->timestamps();
            $table->index('created_at');

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('films');
    }
};
