<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('taxon', function (Blueprint $table) {
            $table->foreignId('author_id')->nullable()->change();
        });

        Schema::table('tags', function (Blueprint $table) {
            $table->foreignId('author_id')->nullable()->change();
        });

        Schema::table('subjects', function (Blueprint $table) {
            $table->foreignId('author_id')->nullable()->change();
        });
    }
};
