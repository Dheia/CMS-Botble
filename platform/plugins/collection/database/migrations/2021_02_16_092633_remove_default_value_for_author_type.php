<?php

use Botble\ACL\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (! Schema::hasColumn('categories', 'author_type')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->string('author_type');
            });
        }

        Schema::table('categories', function (Blueprint $table) {
            $table->string('author_type')->change();
        });

        if (! Schema::hasColumn('tags', 'author_type')) {
            Schema::table('tags', function (Blueprint $table) {
                $table->string('author_type');
            });
        }

        Schema::table('tags', function (Blueprint $table) {
            $table->string('author_type')->change();
        });

        if (! Schema::hasColumn('subjects', 'author_type')) {
            Schema::table('subjects', function (Blueprint $table) {
                $table->string('author_type');
            });
        }

        Schema::table('subjects', function (Blueprint $table) {
            $table->string('author_type')->change();
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->string('author_type')->default(addslashes(User::class))->change();
        });

        Schema::table('tags', function (Blueprint $table) {
            $table->string('author_type')->default(addslashes(User::class))->change();
        });

        Schema::table('subjects', function (Blueprint $table) {
            $table->string('author_type')->default(addslashes(User::class))->change();
        });
    }
};
