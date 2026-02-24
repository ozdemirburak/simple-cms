<x-layouts.app :title="$article->title">
    <article>
        <!-- Article Header -->
        <header class="bg-base-200/50 border-b border-base-300">
            <div class="container mx-auto px-6 py-8">
                <!-- Breadcrumbs -->
                <nav class="text-sm mb-8">
                    <ol class="flex items-center gap-2 text-base-content/50">
                        <li><a href="{{ route('home') }}" class="hover:text-primary transition-colors">{{ __('frontend.nav.home') }}</a></li>
                        <li><span class="mx-2">/</span></li>
                        <li><a href="{{ route('articles.index') }}" class="hover:text-primary transition-colors">{{ __('frontend.nav.articles') }}</a></li>
                        @if($article->category)
                            <li><span class="mx-2">/</span></li>
                            <li><a href="{{ route('categories.show', $article->category->slug) }}" class="hover:text-primary transition-colors">{{ $article->category->title }}</a></li>
                        @endif
                    </ol>
                </nav>

                <div class="max-w-3xl">
                    <!-- Meta info -->
                    <div class="flex flex-wrap items-center gap-4 mb-6">
                        @if($article->category)
                            <a href="{{ route('categories.show', $article->category->slug) }}" class="badge badge-primary">{{ $article->category->title }}</a>
                        @endif
                        <span class="text-sm text-base-content/50">{{ $article->published_at?->format('F d, Y') }}</span>
                        <span class="text-sm text-base-content/40">·</span>
                        <span class="text-sm text-base-content/50">{{ __('frontend.articles.min_read', ['minutes' => ceil(str_word_count(strip_tags($article->content)) / 200)]) }}</span>
                    </div>

                    <!-- Title -->
                    <h1 class="font-display text-3xl md:text-5xl font-semibold leading-tight mb-6">
                        {{ $article->title }}
                    </h1>

                    <!-- Excerpt -->
                    @if($article->excerpt)
                        <p class="text-xl text-base-content/70 leading-relaxed">
                            {{ $article->excerpt }}
                        </p>
                    @endif
                </div>
            </div>
        </header>

        <!-- Main Content with Sidebar -->
        <div class="container mx-auto px-6 py-12">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
                <!-- Article Content -->
                <div class="lg:col-span-8">
                    <!-- Featured Image -->
                    @if($article->featured_image)
                        <figure class="mb-10">
                            <div class="relative aspect-video rounded-lg overflow-hidden border border-base-300">
                                <img src="{{ Storage::url($article->featured_image) }}" alt="{{ $article->title }}" class="w-full h-full object-cover" />
                            </div>
                        </figure>
                    @endif

                    <div class="prose prose-lg max-w-none">{!! $article->content !!}</div>
                    <div class="divider my-12"></div>

                    <!-- Share section -->
                    <div class="flex justify-between items-center">
                        <div id="share-buttons"></div>
                        <div class="flex items-center gap-2 text-base-content/50">
                            <x-lucide-eye class="h-5 w-5" />
                            <span class="text-sm">{{ number_format($article->views_count) }} {{ Str::plural('view', $article->views_count) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <aside class="lg:col-span-4">
                    <div class="lg:sticky lg:top-24 space-y-6">
                        @if($others->count())
                            <div class="border border-base-300 rounded-lg p-6">
                                <h3 class="font-display text-lg font-semibold mb-6 pb-4 border-b border-base-300">{{ __('frontend.articles.other_articles') }}</h3>
                                <div class="space-y-5">
                                    @foreach($others as $other)
                                        <a href="{{ route('articles.show', $other->slug) }}" class="group block">
                                            <div class="flex gap-4">
                                                <div class="shrink-0 w-20 h-16 rounded overflow-hidden border border-base-300 bg-base-200">
                                                    @if($other->featured_image)
                                                        <img src="{{ Storage::url($other->featured_image) }}" alt="{{ $other->title }}" class="w-full h-full object-cover" />
                                                    @else
                                                        <div class="w-full h-full flex items-center justify-center">
                                                            <span class="font-display text-lg text-base-content/20">{{ substr($other->title, 0, 1) }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <h4 class="font-medium text-sm line-clamp-2 group-hover:text-primary transition-colors">{{ $other->title }}</h4>
                                                    <p class="text-xs text-base-content/50 mt-1">{{ $other->published_at?->format('M d, Y') }}</p>
                                                </div>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                                <div class="pt-4 mt-4 border-t border-base-300">
                                    <a href="{{ route('articles.index') }}" class="btn btn-outline btn-sm w-full">
                                        {{ __('frontend.articles.view_all_articles') }}
                                    </a>
                                </div>
                            </div>
                        @endif

                    </div>
                </aside>
            </div>
        </div>
    </article>

    @push('scripts')
        @vite('resources/js/article.js')
    @endpush
</x-layouts.app>
