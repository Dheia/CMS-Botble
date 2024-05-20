<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->index('status', 'subjects_status_index');
            $table->index('author_id', 'subjects_author_id_index');
            $table->index('author_type', 'subjects_author_type_index');
            $table->index('created_at', 'subjects_created_at_index');
        });

        Schema::table('taxons', function (Blueprint $table) {
            $table->index('parent_id', 'taxons_parent_id_index');
            $table->index('status', 'taxons_status_index');
            $table->index('created_at', 'taxons_created_at_index');
        });
    }

    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropIndex('subjects_status_index');
            $table->dropIndex('subjects_author_id_index');
            $table->dropIndex('subjects_author_type_index');
            $table->dropIndex('subjects_created_at_index');
        });

        Schema::table('taxons', function (Blueprint $table) {
            $table->dropIndex('taxons_parent_id_index');
            $table->dropIndex('taxons_status_index');
            $table->dropIndex('taxons_created_at_index');
        });
    }
};
