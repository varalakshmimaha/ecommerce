<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') - Suwish</title>
    @vite(['resources/css/admin.css', 'resources/js/admin.js'])
 </head>
 <body class="bg-surface-alt text-text-body">
    <div class="flex h-screen overflow-hidden flex-col md:flex-row">
        <!-- Sidebar -->
        <aside class="bg-gradient-to-b from-surface-main to-surface-alt border-r border-ui-border shadow-lg w-full md:w-64 md:flex-shrink-0 overflow-y-auto hidden md:flex md:flex-col" id="adminSidebar">
            <div class="p-6 flex-1">
                <h1 class="text-2xl font-bold bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson bg-clip-text text-transparent mb-8">Suvee Admin</h1>
                <nav class="space-y-2">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 text-text-heading rounded-lg hover:bg-gradient-to-r hover:from-brand-gold hover:via-brand-amber hover:to-brand-crimson hover:text-white transition-all duration-300 {{ request()->routeIs('admin.dashboard') ? 'bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white shadow-md' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Dashboard
                    </a>
                    <a href="{{ route('admin.products.index') }}" class="flex items-center px-4 py-3 text-text-heading rounded-lg hover:bg-gradient-to-r hover:from-brand-gold hover:via-brand-amber hover:to-brand-crimson hover:text-white transition-all duration-300 {{ request()->routeIs('admin.products.*') ? 'bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white shadow-md' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        Products
                    </a>
                    <a href="{{ route('admin.orders.index') }}" class="flex items-center px-4 py-3 text-text-heading rounded-lg hover:bg-gradient-to-r hover:from-brand-gold hover:via-brand-amber hover:to-brand-crimson hover:text-white transition-all duration-300 {{ request()->routeIs('admin.orders.*') ? 'bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white shadow-md' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                        Orders
                    </a>
                    <a href="{{ route('admin.categories.index') }}" class="flex items-center px-4 py-3 text-text-heading rounded-lg hover:bg-gradient-to-r hover:from-brand-gold hover:via-brand-amber hover:to-brand-crimson hover:text-white transition-all duration-300 {{ request()->routeIs('admin.categories.*') ? 'bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white shadow-md' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                        Categories
                    </a>
                    <a href="{{ route('admin.banners.index') }}" class="flex items-center px-4 py-3 text-text-heading rounded-lg hover:bg-gradient-to-r hover:from-brand-gold hover:via-brand-amber hover:to-brand-crimson hover:text-white transition-all duration-300 {{ request()->routeIs('admin.banners.*') ? 'bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white shadow-md' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Banners
                    </a>
                    <a href="{{ route('admin.brands.index') }}" class="flex items-center px-4 py-3 text-text-heading rounded-lg hover:bg-gradient-to-r hover:from-brand-gold hover:via-brand-amber hover:to-brand-crimson hover:text-white transition-all duration-300 {{ request()->routeIs('admin.brands.*') ? 'bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white shadow-md' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                        Brands
                    </a>
                    <!--<a href="{{ route('admin.product-attributes.index') }}" class="flex items-center px-4 py-3 text-[#1A1A1A] rounded-lg hover:bg-gradient-to-r hover:from-[#D4AF37] hover:to-[#B8962E] hover:text-white transition-all duration-300 {{ request()->routeIs('admin.product-attributes.*') ? 'bg-gradient-to-r from-[#D4AF37] to-[#B8962E] text-white shadow-md' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                        Product Attributes
                    </a>-->
                    <a href="{{ route('admin.pages.index') }}" class="flex items-center px-4 py-3 text-text-heading rounded-lg hover:bg-gradient-to-r hover:from-brand-gold hover:via-brand-amber hover:to-brand-crimson hover:text-white transition-all duration-300 {{ request()->routeIs('admin.pages.*') ? 'bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white shadow-md' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Pages
                    </a>
                    <a href="{{ route('admin.reports.index') }}" class="flex items-center px-4 py-3 text-text-heading rounded-lg hover:bg-gradient-to-r hover:from-brand-gold hover:via-brand-amber hover:to-brand-crimson hover:text-white transition-all duration-300 {{ request()->routeIs('admin.reports.*') ? 'bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white shadow-md' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Reports
                    </a>
                    <a href="{{ route('admin.settings.index') }}" class="flex items-center px-4 py-3 text-text-heading rounded-lg hover:bg-gradient-to-r hover:from-brand-gold hover:via-brand-amber hover:to-brand-crimson hover:text-white transition-all duration-300 {{ request()->routeIs('admin.settings.*') ? 'bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white shadow-md' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Settings
                    </a>
                    <a href="{{ route('admin.payment.index') }}" class="flex items-center px-4 py-3 text-text-heading rounded-lg hover:bg-gradient-to-r hover:from-brand-gold hover:via-brand-amber hover:to-brand-crimson hover:text-white transition-all duration-300 {{ request()->routeIs('admin.payment.*') ? 'bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white shadow-md' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                        Payment Methods
                    </a>
                    <a href="{{ route('admin.shipping.index') }}" class="flex items-center px-4 py-3 text-text-heading rounded-lg hover:bg-gradient-to-r hover:from-brand-gold hover:via-brand-amber hover:to-brand-crimson hover:text-white transition-all duration-300 {{ request()->routeIs('admin.shipping.*') ? 'bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white shadow-md' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18"></path>
                        </svg>
                        Shipping
                    </a>
                    <a href="{{ route('admin.queries.index') }}" class="flex items-center px-4 py-3 text-text-heading rounded-lg hover:bg-gradient-to-r hover:from-brand-gold hover:via-brand-amber hover:to-brand-crimson hover:text-white transition-all duration-300 {{ request()->routeIs('admin.queries.*') ? 'bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white shadow-md' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Queries
                    </a>
                </nav>
            </div>
            <div class="p-6 border-t border-gray-100 flex-shrink-0">
                <a href="{{ route('admin.profile.edit') }}" class="flex items-center px-4 py-3 text-text-heading rounded-lg hover:bg-gradient-to-r hover:from-brand-gold hover:via-brand-amber hover:to-brand-crimson hover:text-white transition-all duration-300 mb-2 {{ request()->routeIs('admin.profile.*') ? 'bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white shadow-md' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Profile
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center px-4 py-3 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-lg hover:shadow-lg transition-all duration-300">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        Logout
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-white shadow-sm border-b border-ui-border">
                <div class="px-4 md:px-6 py-4 flex items-center justify-between">
                    <h2 class="text-xl md:text-2xl font-semibold text-text-heading">@yield('title', 'Dashboard')</h2>
                    <button onclick="toggleMobileSidebar()" class="md:hidden text-text-muted hover:text-ui-hover">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
            </header>
            <main class="flex-1 overflow-auto p-4 md:p-6">
                @if(session('success'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg animate-slide-down">
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg animate-slide-down">
                        {{ session('error') }}
                    </div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>
    <script>
        function toggleMobileSidebar() {
            const sidebar = document.getElementById('adminSidebar');
            sidebar.classList.toggle('hidden');
        }
    </script>
    @yield('scripts')
</body>
</html>

