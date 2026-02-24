<x-layouts.app :title="__('frontend.nav.home')">
    <!-- Hero Section -->
    <section class="bg-base-200/50 border-b border-base-300">
        <div class="container mx-auto px-6 py-20 md:py-28">
            <div class="grid lg:grid-cols-12 gap-12 items-center">
                <div class="lg:col-span-5">
                    <span class="inline-block px-4 py-1.5 text-xs font-semibold uppercase tracking-wider border border-base-300 rounded-full mb-6 text-base-content/60">
                        {{ __('frontend.site_name') }}
                    </span>
                    <h1 class="font-display text-4xl md:text-5xl lg:text-6xl font-bold leading-[1.1] mb-6">
                        {{ __('frontend.site_tagline') }}
                    </h1>
                    <p class="text-lg text-base-content/60 mb-8 leading-relaxed">
                        {{ __('frontend.site_description') }}
                    </p>
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('articles.index') }}" class="btn btn-primary">
                            {{ __('frontend.nav.browse_articles') }}
                            <x-lucide-arrow-right class="w-4 h-4" />
                        </a>
                        <a href="#latest" class="btn btn-outline">
                            {{ __('frontend.nav.latest_posts') }}
                        </a>
                    </div>
                </div>
                <div class="lg:col-span-7">
                    @php $heroArticles = $articles->take(3); @endphp
                    @if($heroArticles->count() >= 3)
                        <div class="grid grid-cols-12 gap-4">
                            <!-- Large featured card -->
                            <a href="{{ route('articles.show', $heroArticles[0]->slug) }}" class="col-span-12 md:col-span-7 group">
                                <div class="relative aspect-[4/3] rounded-lg overflow-hidden border border-base-300 bg-base-200">
                                    @if($heroArticles[0]->featured_image)
                                        <img src="{{ Storage::url($heroArticles[0]->featured_image) }}" alt="{{ $heroArticles[0]->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                                    @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            <span class="font-display text-5xl text-base-content/10">{{ substr($heroArticles[0]->title, 0, 1) }}</span>
                                        </div>
                                    @endif
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                                    <div class="absolute bottom-0 left-0 right-0 p-5 text-white">
                                        @if($heroArticles[0]->category)
                                            <span class="badge badge-sm bg-white/20 border-0 text-white mb-2">{{ $heroArticles[0]->category->title }}</span>
                                        @endif
                                        <h3 class="font-display font-semibold text-lg leading-snug">{{ $heroArticles[0]->title }}</h3>
                                    </div>
                                </div>
                            </a>
                            <!-- Stacked smaller cards -->
                            <div class="col-span-12 md:col-span-5 flex flex-col gap-4">
                                @foreach($heroArticles->skip(1)->take(2) as $article)
                                    <a href="{{ route('articles.show', $article->slug) }}" class="group flex-1">
                                        <div class="relative h-full min-h-[140px] rounded-lg overflow-hidden border border-base-300 bg-base-200">
                                            @if($article->featured_image)
                                                <img src="{{ Storage::url($article->featured_image) }}" alt="{{ $article->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                                            @else
                                                <div class="w-full h-full flex items-center justify-center">
                                                    <span class="font-display text-3xl text-base-content/10">{{ substr($article->title, 0, 1) }}</span>
                                                </div>
                                            @endif
                                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                                            <div class="absolute bottom-0 left-0 right-0 p-4 text-white">
                                                <h3 class="font-display font-medium text-sm leading-snug line-clamp-2">{{ $article->title }}</h3>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
    @if($articles->count())
        <!-- Latest Articles -->
        <section id="latest">
            <div class="container mx-auto px-6 py-12">
                <div class="flex items-center justify-between mb-10">
                    <h2 class="font-display text-2xl font-semibold">{{ __('frontend.articles.latest') }}</h2>
                    <a href="{{ route('articles.index') }}" class="btn btn-ghost btn-sm">
                        {{ __('frontend.nav.view_all') }}
                        <x-lucide-chevron-right class="w-4 h-4" />
                    </a>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($articles->skip(3)->take(6) as $article)
                        <a href="{{ route('articles.show', $article->slug) }}" class="group block border border-base-300 rounded-lg overflow-hidden hover:border-primary transition-colors">
                            <figure class="aspect-video bg-base-200">
                                @if($article->featured_image)
                                    <img src="{{ Storage::url($article->featured_image) }}" alt="{{ $article->title }}" class="w-full h-full object-cover" />
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <span class="font-display text-4xl text-base-content/10">{{ substr($article->title, 0, 1) }}</span>
                                    </div>
                                @endif
                            </figure>
                            <div class="p-5">
                                <div class="flex items-center gap-2 text-sm mb-3">
                                    @if($article->category)
                                        <span class="badge badge-ghost badge-sm">{{ $article->category->title }}</span>
                                    @endif
                                    <span class="text-base-content/40">{{ $article->published_at?->format('M d, Y') }}</span>
                                </div>
                                <h3 class="font-display font-semibold text-lg group-hover:text-primary transition-colors line-clamp-2">{{ $article->title }}</h3>
                                @if($article->excerpt)
                                    <p class="text-base-content/60 text-sm mt-2 line-clamp-2">{{ $article->excerpt }}</p>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
        <!-- Categories -->
        @if($categories->count())
            <section>
                <div class="container mx-auto px-6 py-12">
                    <h2 class="font-display text-2xl font-semibold mb-10">{{ __('frontend.articles.browse_by_topic') }}</h2>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach($categories as $category)
                            <a href="{{ route('categories.show', $category->slug) }}" class="block p-6 border border-base-300 rounded-lg hover:border-primary transition-colors">
                                <h3 class="font-display font-semibold">{{ $category->title }}</h3>
                                <p class="text-sm text-base-content/50 mt-1">{{ $category->articles_count }} {{ Str::plural('article', $category->articles_count) }}</p>
                            </a>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
    @else
        <!-- Empty State -->
        <section class="container mx-auto px-6 py-24 text-center">
            <div class="max-w-md mx-auto">
                <div class="w-20 h-20 rounded-full border border-base-300 flex items-center justify-center mx-auto mb-6">
                    <x-lucide-newspaper class="w-10 h-10 text-base-content/30" />
                </div>
                <h2 class="font-display text-2xl font-semibold mb-3">{{ __('frontend.empty.no_content') }}</h2>
                <p class="text-base-content/60">{{ __('frontend.empty.check_back') }}</p>
            </div>
        </section>
    @endif
</x-layouts.app>
