<x-layouts.app :title="$category->title">
    <!-- Category Header -->
    <section class="bg-base-200/50 border-b border-base-200">
        <div class="container mx-auto px-6 py-16">
            <div class="max-w-2xl">
                <!-- Breadcrumbs -->
                <nav class="text-sm mb-8">
                    <ol class="flex items-center gap-2 text-base-content/50">
                        <li><a href="{{ route('home') }}" class="hover:text-primary transition-colors">{{ __('frontend.nav.home') }}</a></li>
                        <li><span class="mx-2">/</span></li>
                        <li><a href="{{ route('articles.index') }}" class="hover:text-primary transition-colors">{{ __('frontend.nav.articles') }}</a></li>
                        <li><span class="mx-2">/</span></li>
                        <li class="text-base-content">{{ $category->title }}</li>
                    </ol>
                </nav>

                <span class="badge badge-primary mb-4">{{ __('frontend.categories.category') }}</span>
                <h1 class="font-display text-4xl md:text-5xl font-semibold mb-4">{{ $category->title }}</h1>
                @if($category->description)
                    <p class="text-lg text-base-content/60 leading-relaxed">{{ $category->description }}</p>
                @endif
            </div>
        </div>
    </section>

    <section class="container mx-auto px-6 py-12">
        @if($articles->count())
            <!-- Articles Count -->
            <div class="flex items-center justify-between mb-10">
                <p class="text-base-content/50">
                    {{ trans_choice('frontend.articles.articles_in_category', $articles->total(), ['count' => $articles->total()]) }}
                </p>
                <a href="{{ route('articles.index') }}" class="editorial-link text-sm font-medium text-base-content/60 hover:text-primary inline-flex items-center gap-2">
                    <x-lucide-arrow-left class="h-4 w-4" />
                    {{ __('frontend.articles.all_articles') }}
                </a>
            </div>

            <!-- Articles Grid -->
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($articles as $article)
                    <article class="card-lift bg-base-100 rounded-lg overflow-hidden border border-base-200">
                        <a href="{{ route('articles.show', $article->slug) }}" class="block group">
                            <div class="relative aspect-[16/10] bg-base-200 overflow-hidden">
                                @if($article->featured_image)
                                    <img src="{{ Storage::url($article->featured_image) }}" alt="{{ $article->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-primary/5 to-secondary/5">
                                        <span class="font-display text-5xl text-primary/20">{{ substr($article->title, 0, 1) }}</span>
                                    </div>
                                @endif
                            </div>
                            <div class="p-6">
                                <span class="text-xs text-base-content/40">{{ $article->published_at?->format('M d, Y') }}</span>
                                <h2 class="font-display text-xl font-semibold mt-2 mb-2 group-hover:text-primary transition-colors line-clamp-2">
                                    {{ $article->title }}
                                </h2>
                                @if($article->excerpt)
                                    <p class="text-base-content/60 text-sm line-clamp-3 mb-4">{{ $article->excerpt }}</p>
                                @endif
                                <div class="flex items-center text-sm text-primary font-medium">
                                    {{ __('frontend.articles.read_article') }}
                                    <x-lucide-arrow-right class="h-4 w-4 ml-1 group-hover:translate-x-1 transition-transform" />
                                </div>
                            </div>
                        </a>
                    </article>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($articles->hasPages())
                <div class="mt-12 flex justify-center">
                    <div class="join">
                        @if($articles->onFirstPage())
                            <button class="join-item btn btn-disabled">
                                <x-lucide-chevron-left class="h-4 w-4" />
                            </button>
                        @else
                            <a href="{{ $articles->previousPageUrl() }}" class="join-item btn">
                                <x-lucide-chevron-left class="h-4 w-4" />
                            </a>
                        @endif

                        @foreach($articles->getUrlRange(max(1, $articles->currentPage() - 3), min($articles->lastPage(), $articles->currentPage() + 3)) as $page => $url)
                            <a href="{{ $url }}" class="join-item btn {{ $page == $articles->currentPage() ? 'btn-primary' : '' }}">{{ $page }}</a>
                        @endforeach

                        @if($articles->hasMorePages())
                            <a href="{{ $articles->nextPageUrl() }}" class="join-item btn">
                                <x-lucide-chevron-right class="h-4 w-4" />
                            </a>
                        @else
                            <button class="join-item btn btn-disabled">
                                <x-lucide-chevron-right class="h-4 w-4" />
                            </button>
                        @endif
                    </div>
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="text-center py-16">
                <div class="w-20 h-20 rounded-full bg-base-200 flex items-center justify-center mx-auto mb-6">
                    <x-lucide-newspaper class="h-10 w-10 text-base-content/30" />
                </div>
                <h2 class="font-display text-2xl font-semibold mb-3">{{ __('frontend.articles.no_articles_in_category') }}</h2>
                <p class="text-base-content/60 mb-6">{{ __('frontend.articles.no_articles_in_category_desc') }}</p>
                <a href="{{ route('articles.index') }}" class="btn btn-primary">
                    <x-lucide-arrow-left class="h-4 w-4 mr-2" />
                    {{ __('frontend.articles.browse_all_articles') }}
                </a>
            </div>
        @endif
    </section>

    <!-- Other Categories -->
    @if($otherCategories->count())
        <section class="bg-base-200/30 border-t border-base-200 py-12">
            <div class="container mx-auto px-6">
                <h2 class="font-display text-xl font-semibold mb-8">{{ __('frontend.categories.explore_other') }}</h2>

                <div class="flex flex-wrap gap-3">
                    @foreach($otherCategories as $otherCategory)
                        <a href="{{ route('categories.show', $otherCategory->slug) }}" class="btn btn-outline btn-sm hover:btn-primary">
                            {{ $otherCategory->title }}
                            <span class="badge badge-ghost badge-sm">{{ $otherCategory->articles_count }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</x-layouts.app>
