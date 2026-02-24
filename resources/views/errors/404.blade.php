<x-layouts.app :title="__('frontend.errors.404.title')">
    <section class="container mx-auto px-6 py-24 text-center">
        <div class="max-w-md mx-auto">
            <div class="w-20 h-20 rounded-full bg-base-200 flex items-center justify-center mx-auto mb-6">
                <x-lucide-search-x class="w-10 h-10 text-base-content/30" />
            </div>
            <h1 class="font-display text-6xl font-bold text-primary mb-4">404</h1>
            <h2 class="font-display text-2xl font-semibold mb-3">{{ __('frontend.errors.404.title') }}</h2>
            <p class="text-base-content/60 mb-8">{{ __('frontend.errors.404.message') }}</p>
            <div class="flex flex-wrap justify-center gap-3">
                <a href="{{ route('home') }}" class="btn btn-primary">
                    <x-lucide-home class="w-4 h-4" />
                    {{ __('frontend.errors.404.back_home') }}
                </a>
                <a href="{{ route('articles.index') }}" class="btn btn-outline">
                    <x-lucide-newspaper class="w-4 h-4" />
                    {{ __('frontend.nav.articles') }}
                </a>
            </div>
        </div>
    </section>
</x-layouts.app>
