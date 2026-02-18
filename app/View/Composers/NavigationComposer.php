<?php

namespace App\View\Composers;

use App\Models\Page;
use Illuminate\View\View;

class NavigationComposer
{
    protected static array $footerOnlySlugs = [
        'about',
        'privacy-policy',
        'terms-of-service',
    ];

    public function compose(View $view): void
    {
        $pages = Page::published()
            ->roots()
            ->orderBy('sort_order')
            ->get();

        $view->with([
            'navPages' => $pages->filter(fn ($p) => !in_array($p->slug, self::$footerOnlySlugs)),
            'footerPages' => $pages->filter(fn ($p) => in_array($p->slug, self::$footerOnlySlugs)),
        ]);
    }
}
