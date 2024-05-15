<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('subjects_translations')) {
            Schema::create('subjects_translations', function (Blueprint $table) {
                $table->string('lang_code', 20);
                $table->foreignId('subjects_id');
                $table->string('name')->nullable();
                $table->string('description', 400)->nullable();
                $table->longText('content')->nullable();

                $table->primary(['lang_code', 'subjects_id'], 'subjects_translations_primary');
            });
        }

        if (! Schema::hasTable('categories_translations')) {
            Schema::create('categories_translations', function (Blueprint $table) {
                $table->string('lang_code', 20);
                $table->foreignId('categories_id');
                $table->string('name')->nullable();
                $table->string('description', 400)->nullable();

                $table->primary(['lang_code', 'categories_id'], 'categories_translations_primary');
            });
        }

        if (! Schema::hasTable('tags_translations')) {
            Schema::create('tags_translations', function (Blueprint $table) {
                $table->string('lang_code', 20);
                $table->foreignId('tags_id');
                $table->string('name')->nullable();
                $table->string('description', 400)->nullable();

                $table->primary(['lang_code', 'tags_id'], 'tags_translations_primary');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('subjects_translations');
        Schema::dropIfExists('categories_translations');
        Schema::dropIfExists('tags_translations');
    }
};
