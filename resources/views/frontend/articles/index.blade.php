<x-layouts.app :title="__('frontend.articles.title')">
    <!-- Page Header -->
    <section class="bg-base-200/50 border-b border-base-200">
        <div class="container mx-auto px-6 py-16">
            <div class="max-w-2xl">
                <span class="badge badge-ghost mb-4">{{ __('frontend.articles.archive') }}</span>
                <h1 class="font-display text-4xl md:text-5xl font-semibold mb-4">{{ __('frontend.articles.title') }}</h1>
                <p class="text-lg text-base-content/60 leading-relaxed">
                    {{ __('frontend.articles.description') }}
                </p>
            </div>
        </div>
    </section>

    <section class="container mx-auto px-6 py-12">
        @if($articles->count())
            <!-- Category Filter -->
            @if($categories->count())
                <div class="flex flex-wrap gap-2 mb-10">
                    <span class="text-sm text-base-content/50 py-2 mr-2">{{ __('frontend.articles.filter_by_topic') }}</span>
                    @foreach($categories as $category)
                        <a href="{{ route('categories.show', $category->slug) }}" class="btn btn-sm btn-ghost border border-base-200 hover:border-primary hover:bg-primary/5">
                            {{ $category->title }}
                            <span class="badge badge-ghost badge-sm">{{ $category->articles_count }}</span>
                        </a>
                    @endforeach
                </div>
            @endif

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
                                <div class="flex items-center gap-3 mb-3">
                                    @if($article->category)
                                        <span class="badge badge-ghost badge-sm">{{ $article->category->title }}</span>
                                    @endif
                                    <span class="text-xs text-base-content/40">{{ $article->published_at?->format('M d, Y') }}</span>
                                </div>
                                <h2 class="font-display text-xl font-semibold mb-2 group-hover:text-primary transition-colors line-clamp-2">
                                    {{ $article->title }}
                                </h2>
                                @if($article->excerpt)
                                    <p class="text-base-content/60 text-sm line-clamp-3 mb-4">{{ $article->excerpt }}</p>
                                @endif
                                <div class="flex items-center text-sm text-primary font-medium">
                                    {{ __('frontend.articles.read_more') }}
                                    <x-lucide-arrow-right class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" />
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
                            <button class="join-item btn btn-disabled">«</button>
                        @else
                            <a href="{{ $articles->previousPageUrl() }}" class="join-item btn">«</a>
                        @endif

                        @foreach($articles->getUrlRange(max(1, $articles->currentPage() - 3), min($articles->lastPage(), $articles->currentPage() + 3)) as $page => $url)
                            <a href="{{ $url }}" class="join-item btn {{ $page == $articles->currentPage() ? 'btn-primary' : '' }}">{{ $page }}</a>
                        @endforeach

                        @if($articles->hasMorePages())
                            <a href="{{ $articles->nextPageUrl() }}" class="join-item btn">»</a>
                        @else
                            <button class="join-item btn btn-disabled">»</button>
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
                <h2 class="font-display text-2xl font-semibold mb-3">{{ __('frontend.articles.no_articles') }}</h2>
                <p class="text-base-content/60">{{ __('frontend.empty.check_back') }}</p>
            </div>
        @endif
    </section>
</x-layouts.app>
