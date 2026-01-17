<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

echo "Dropping tables...\n";
$tables = [
    'blog_post_tags',
    'blog_posts',
    'blog_tags',
    'blog_categories',
    'featured_astrologers',
    'faqs',
    'cms_banners',
    'cms_pages',
    'recommendation_settings',
    'bookmarks',
    'user_events',
    'user_preferences'
];

// Disable foreign key checks
DB::statement('SET FOREIGN_KEY_CHECKS=0;');

foreach ($tables as $table) {
    if (Schema::hasTable($table)) {
        Schema::drop($table);
        echo "Dropped '$table'.\n";
    } else {
        echo "Table '$table' did not exist.\n";
    }
}

DB::statement('SET FOREIGN_KEY_CHECKS=1;');
echo "Cleanup done.\n";
