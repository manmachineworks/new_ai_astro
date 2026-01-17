<?php

use Illuminate\Support\Facades\Schema;

echo "Checking tables...\n";
$tables = ['users', 'cms_pages', 'cms_banners', 'faqs', 'featured_astrologers'];

foreach ($tables as $table) {
    if (Schema::hasTable($table)) {
        echo "Table '$table' exists.\n";
    } else {
        echo "Table '$table' DOES NOT exist.\n";
    }
}
