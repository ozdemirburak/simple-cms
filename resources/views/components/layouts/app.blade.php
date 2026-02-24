<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="editorial">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? __('frontend.nav.home') }} - {{ __('frontend.site_name') }}</title>
    <meta name="description" content="{{ $description ?? __('frontend.site_description') }}">
    <link rel="canonical" href="{{ url()->current() }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('favicon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="min-h-screen flex flex-col bg-base-100">
    <!-- Header -->
    <header class="sticky top-0 z-50 bg-base-100/95 backdrop-blur-sm border-b border-base-300">
        <div class="navbar container mx-auto px-6 justify-between">
            <div class="navbar-start">
                <a href="{{ route('home') }}" class="font-display text-xl font-semibold hover:text-primary transition-colors">
                    {{ __('frontend.site_name') }}
                </a>
            </div>
            <div class="navbar-end hidden lg:flex">
                <ul class="menu menu-horizontal gap-1">
                    <li><a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">{{ __('frontend.nav.home') }}</a></li>
                    <li><a href="{{ route('articles.index') }}" class="{{ request()->routeIs('articles.*') ? 'active' : '' }}">{{ __('frontend.nav.articles') }}</a></li>
                    @foreach($navPages as $navPage)
                        <li><a href="{{ route('pages.show', $navPage->slug) }}" class="{{ request()->is('page/'.$navPage->slug) ? 'active' : '' }}">{{ $navPage->title }}</a></li>
                    @endforeach
                </ul>
            </div>
            <div class="navbar-end lg:hidden">
                <div class="dropdown dropdown-end">
                    <div tabindex="0" role="button" class="btn btn-ghost btn-square">
                        <x-lucide-menu class="w-5 h-5" />
                    </div>
                    <ul tabindex="0" class="dropdown-content menu bg-base-200 rounded-box z-50 w-52 p-2 shadow-lg mt-3">
                        <li><a href="{{ route('home') }}">{{ __('frontend.nav.home') }}</a></li>
                        <li><a href="{{ route('articles.index') }}">{{ __('frontend.nav.articles') }}</a></li>
                        @foreach($navPages as $navPage)
                            <li><a href="{{ route('pages.show', $navPage->slug) }}">{{ $navPage->title }}</a></li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </header>
    <!-- Main content -->
    <main class="flex-1">
        {{ $slot }}
    </main>
    <!-- Footer -->
    <footer class="bg-neutral text-neutral-content">
        <div class="container mx-auto px-6 py-12">
            <div class="flex flex-col md:flex-row justify-between gap-8">
                <div>
                    <span class="font-display text-lg font-semibold">{{ __('frontend.site_name') }}</span>
                    <p class="mt-3 text-neutral-content/60 text-sm max-w-xs">
                        {{ __('frontend.site_description') }}
                    </p>
                </div>
                <div class="flex gap-12">
                    <div>
                        <h4 class="font-semibold text-sm uppercase tracking-wider text-neutral-content/50 mb-4">{{ __('frontend.nav.navigate') }}</h4>
                        <ul class="space-y-2 text-sm">
                            <li><a href="{{ route('home') }}" class="hover:text-primary transition-colors">{{ __('frontend.nav.home') }}</a></li>
                            <li><a href="{{ route('articles.index') }}" class="hover:text-primary transition-colors">{{ __('frontend.nav.articles') }}</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-semibold text-sm uppercase tracking-wider text-neutral-content/50 mb-4">{{ __('frontend.nav.info') }}</h4>
                        <ul class="space-y-2 text-sm">
                            @foreach($footerPages as $footerPage)
                                <li><a href="{{ route('pages.show', $footerPage->slug) }}" class="hover:text-primary transition-colors">{{ $footerPage->title }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            <div class="divider my-8"></div>
            <div class="flex flex-col md:flex-row justify-between items-center gap-4 text-sm text-neutral-content/50">
                <p>{{ __('frontend.footer.copyright', ['year' => date('Y'), 'name' => __('frontend.site_name')]) }}</p>
                <p>{{ __('frontend.footer.built_with') }}</p>
            </div>
        </div>
    </footer>
    @stack('scripts')
</body>
</html>
