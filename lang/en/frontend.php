<?php

return [
    // Site
    'site_name' => 'Simple CMS',
    'site_tagline' => 'Stories & insights that matter.',
    'site_description' => 'A curated collection of thoughtful articles exploring technology, design, lifestyle, and business.',

    // Navigation
    'nav' => [
        'home' => 'Home',
        'articles' => 'Articles',
        'browse_articles' => 'Browse Articles',
        'latest_posts' => 'Latest Posts',
        'view_all' => 'View all',
        'navigate' => 'Navigate',
        'info' => 'Info',
    ],

    // Articles
    'articles' => [
        'title' => 'All Articles',
        'description' => 'Explore our collection of articles on technology, design, lifestyle, and business.',
        'latest' => 'Latest Articles',
        'browse_by_topic' => 'Browse by Topic',
        'read_more' => 'Read more',
        'read_article' => 'Read article',
        'min_read' => ':minutes min read',
        'published_on' => 'Published on :date',
        'article_count' => ':count article|:count articles',
        'articles_in_category' => ':count article in this category|:count articles in this category',
        'no_articles' => 'No articles found.',
        'no_articles_in_category' => 'No articles yet',
        'no_articles_in_category_desc' => 'There are no published articles in this category.',
        'all_articles' => 'All articles',
        'browse_all_articles' => 'Browse all articles',
        'view_all_articles' => 'View All Articles',
        'share' => 'Share This Article',
        'related' => 'Related Articles',
        'other_articles' => 'Other Articles',
        'filter_by_topic' => 'Filter by topic:',
        'archive' => 'Archive',
    ],

    // Categories
    'categories' => [
        'title' => 'Categories',
        'category' => 'Category',
        'articles_in' => 'Articles in :category',
        'explore_other' => 'Explore Other Topics',
    ],

    // Pages
    'pages' => [
        'not_found' => 'Page not found.',
        'last_updated' => 'Last updated: :date',
        'related_pages' => 'Related Pages',
        'quick_contact' => 'Quick Contact',
        'contact_address' => '123 Demo Street, City, Country',
        'contact_email' => 'hello@example.com',
        'contact_hours' => 'Mon - Fri: 9:00 - 17:00',
    ],

    // Social Share
    'share' => [
        'x' => 'Post on X',
        'facebook' => 'Share on Facebook',
        'linkedin' => 'Share on LinkedIn',
    ],

    // Footer
    'footer' => [
        'copyright' => '© :year :name. All rights reserved.',
        'built_with' => 'Built with Laravel, Filament & DaisyUI.',
    ],

    // Empty States
    'empty' => [
        'no_content' => 'No content yet',
        'check_back' => 'Check back soon for articles.',
    ],

    // Errors
    'errors' => [
        '404' => [
            'title' => 'Page Not Found',
            'message' => 'Sorry, the page you are looking for could not be found.',
            'back_home' => 'Back to Home',
        ],
        '500' => [
            'title' => 'Server Error',
            'message' => 'Something went wrong. Please try again later.',
        ],
    ],
];
