<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add template selection and SEO fields to CMS tables
     */
    public function up(): void
    {
        // Update posts table if columns don't exist
        if (!Schema::hasColumn('posts', 'template_layout')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->string('template_layout')->default('classic-grid')->after('status');
                $table->string('og_image')->nullable()->after('featured_image');
                // Rename content to body if needed
                if (Schema::hasColumn('posts', 'content')) {
                    $table->renameColumn('content', 'body');
                }
            });
        }

        // Update pages table if columns don't exist
        if (!Schema::hasColumn('pages', 'template_layout')) {
            Schema::table('pages', function (Blueprint $table) {
                $table->string('template_layout')->default('minimalist-legal')->after('is_published');
                $table->string('og_image')->nullable()->after('meta_description');
                // Rename content to body if needed
                if (Schema::hasColumn('pages', 'content')) {
                    $table->renameColumn('content', 'body');
                }
            });
        }

        // Ensure categories has is_active
        if (!Schema::hasColumn('categories', 'is_active')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->boolean('is_active')->default(true)->after('description');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn(['template_layout', 'og_image']);
        });

        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn(['template_layout', 'og_image']);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }
};
