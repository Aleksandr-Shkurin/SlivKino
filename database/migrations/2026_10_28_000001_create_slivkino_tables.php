<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('genres', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('movies', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('title_en')->nullable();
            $table->string('country', 100)->nullable();
            $table->unsignedSmallInteger('year')->nullable();
            $table->string('age_rating', 10)->nullable();
            $table->date('release_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('poster')->nullable();
            $table->text('description')->nullable();
            $table->longText('full_description')->nullable();
            $table->string('director')->nullable();
            $table->json('cast')->nullable();
            $table->unsignedSmallInteger('duration_minutes')->nullable();
            $table->boolean('coming_soon')->default(false);
            $table->timestamps();
            $table->index(['coming_soon', 'release_date']);
        });

        Schema::create('cinemas', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->string('address');
            $table->string('phone', 40)->nullable();
            $table->string('email')->nullable();
            $table->string('working_hours')->nullable();
            $table->text('description')->nullable();
            $table->json('features')->nullable();
            $table->timestamps();
        });

        Schema::create('movie_genre', function (Blueprint $table) {
            $table->foreignId('movie_id')->constrained()->cascadeOnDelete();
            $table->foreignId('genre_id')->constrained()->cascadeOnDelete();
            $table->primary(['movie_id', 'genre_id']);
        });

        Schema::create('halls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cinema_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->unsignedSmallInteger('rows_count')->default(8);
            $table->unsignedSmallInteger('seats_per_row')->default(12);
            $table->json('vip_seats')->nullable();
            $table->json('disabled_seats')->nullable();
            $table->timestamps();
            $table->unique(['cinema_id', 'name']);
        });

        Schema::create('movie_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('movie_id')->constrained()->cascadeOnDelete();
            $table->foreignId('hall_id')->constrained()->cascadeOnDelete();
            $table->dateTime('starts_at');
            $table->decimal('price', 8, 2);
            $table->timestamps();
            $table->index(['starts_at', 'movie_id']);
        });

        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('movie_sessions')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('customer_name')->nullable();
            $table->string('customer_email')->nullable();
            $table->json('seats');
            $table->string('status')->default('reserved');
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('movie_sessions');
        Schema::dropIfExists('halls');
        Schema::dropIfExists('movie_genre');
        Schema::dropIfExists('cinemas');
        Schema::dropIfExists('movies');
        Schema::dropIfExists('genres');
    }
};
