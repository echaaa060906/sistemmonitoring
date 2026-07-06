<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Countries
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('iso_code', 3)->unique(); // e.g. DE, CN, ID, AU
            $table->string('currency_code', 3);      // e.g. EUR, CNY, IDR, AUD
            $table->string('region');                // e.g. Europe, Asia, America
            $table->string('language');
            $table->bigInteger('population')->default(0);
            $table->double('gdp')->default(0);       // in USD
            $table->double('inflation')->default(0); // percentage
            $table->double('export_val')->default(0);
            $table->double('import_val')->default(0);
            $table->timestamps();
        });

        // 2. Ports
        Schema::create('ports', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('country');
            $table->double('latitude');
            $table->double('longitude');
            $table->string('code')->nullable();
            $table->timestamps();
        });

        // 3. Risk Scores
        Schema::create('risk_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained()->onDelete('cascade');
            $table->double('weather_risk')->default(0);
            $table->double('inflation_risk')->default(0);
            $table->double('currency_risk')->default(0);
            $table->double('news_sentiment_risk')->default(0);
            $table->double('total_risk')->default(0);
            $table->timestamps();
        });

        // 4. News Cache
        Schema::create('news_cache', function (Blueprint $table) {
            $table->id();
            $table->string('country_code', 3)->index();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('url')->nullable();
            $table->string('source')->nullable();
            $table->string('sentiment_result')->default('Neutral'); // Positive, Neutral, Negative
            $table->integer('positive_count')->default(0);
            $table->integer('negative_count')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        // 5. Watchlists
        Schema::create('watchlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('country_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->unique(['user_id', 'country_id']);
        });

        // 6. Articles
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->string('author')->nullable();
            $table->timestamps();
        });

        // 7. Positive Words (Lexicon)
        Schema::create('positive_words', function (Blueprint $table) {
            $table->id();
            $table->string('word')->unique();
            $table->timestamps();
        });

        // 8. Negative Words (Lexicon)
        Schema::create('negative_words', function (Blueprint $table) {
            $table->id();
            $table->string('word')->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('negative_words');
        Schema::dropIfExists('positive_words');
        Schema::dropIfExists('articles');
        Schema::dropIfExists('watchlists');
        Schema::dropIfExists('news_cache');
        Schema::dropIfExists('risk_scores');
        Schema::dropIfExists('ports');
        Schema::dropIfExists('countries');
    }
};
