<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') - {{ \App\Models\Setting::get('company_name', 'Suwish') }}</title>
  <!--<title>Suwish Admin</title>-->
    @vite(['resources/css/admin.css', 'resources/js/admin.js'])

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <!-- Dynamic Theme Colors -->
    @if(isset($themeColors))
        <style>
            :root {
                @foreach($themeColors as $variable => $value)
                    {{ $variable }}: {{ $value }} !important;
                @endforeach
            }
            
            /* Override specific classes with theme colors */
            .bg-brand-gold { background-color: var(--brand-gold) !important; }
            .bg-brand-amber { background-color: var(--brand-amber) !important; }
            .bg-brand-burnt { background-color: var(--brand-burnt) !important; }
            .bg-brand-crimson { background-color: var(--brand-crimson) !important; }
            
            .text-brand-gold { color: var(--brand-gold) !important; }
            .text-brand-amber { color: var(--brand-amber) !important; }
            .text-brand-burnt { color: var(--brand-burnt) !important; }
            .text-brand-crimson { color: var(--brand-crimson) !important; }
            
            .text-text-heading { color: var(--text-heading) !important; }
            .text-text-body { color: var(--text-body) !important; }
            .text-text-muted { color: var(--text-muted) !important; }
            
            .bg-surface-primary { background-color: var(--surface-primary) !important; }
            .bg-surface-secondary { background-color: var(--surface-secondary) !important; }
            .bg-surface-light { background-color: var(--surface-light) !important; }
            .bg-surface-medium { background-color: var(--surface-medium) !important; }
            .bg-surface-dark { background-color: var(--surface-dark) !important; }
            
            .border-ui-border { border-color: var(--ui-border) !important; }
            .focus\\:ring-ui-focus:focus { --tw-ring-color: var(--ui-focus) !important; }
            .accent-brand-gold { accent-color: var(--brand-gold) !important; }
        </style>
    @endif
 </head>
 <body class="bg-surface-secondary text-text-body">
    <div class="flex h-screen overflow-hidden flex-col md:flex-row">
        <!-- Sidebar -->
        <aside class="bg-gradient-to-b from-surface-main to-surface-alt border-r border-ui-border shadow-lg w-full md:w-64 md:flex-shrink-0 overflow-y-auto hidden md:flex md:flex-col" id="adminSidebar">
            <div class="p-6 flex-1">
                <h1 class="text-2xl font-bold bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson bg-clip-text text-transparent mb-8">{{ \App\Models\Setting::get('company_name', 'Suwish') }} Admin</h1>
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
                    <a href="{{ route('admin.customers.index') }}" class="flex items-center px-4 py-3 text-text-heading rounded-lg hover:bg-gradient-to-r hover:from-brand-gold hover:via-brand-amber hover:to-brand-crimson hover:text-white transition-all duration-300 {{ request()->routeIs('admin.customers.*') ? 'bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white shadow-md' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m9-7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        Customers
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
                    <a href="{{ route('admin.product-attributes.index') }}" class="flex items-center px-4 py-3 text-text-heading rounded-lg hover:bg-gradient-to-r hover:from-brand-gold hover:via-brand-amber hover:to-brand-crimson hover:text-white transition-all duration-300 {{ request()->routeIs('admin.product-attributes.*') ? 'bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white shadow-md' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                        Product Attributes
                    </a>
                    <a href="{{ route('admin.product-attribute-values.index') }}" class="flex items-center px-4 py-3 text-text-heading rounded-lg hover:bg-gradient-to-r hover:from-brand-gold hover:via-brand-amber hover:to-brand-crimson hover:text-white transition-all duration-300 {{ request()->routeIs('admin.product-attribute-values.*') ? 'bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white shadow-md' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                        Attribute Values
                    </a>
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
                    <a href="{{ route('admin.theme-colors.index') }}" class="flex items-center px-4 py-3 text-text-heading rounded-lg hover:bg-gradient-to-r hover:from-brand-gold hover:via-brand-amber hover:to-brand-crimson hover:text-white transition-all duration-300 {{ request()->routeIs('admin.theme-colors.*') ? 'bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white shadow-md' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                        </svg>
                        Theme Colors
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

                    <div class="pt-4 mt-4 border-t border-gray-100">
                        <div class="px-4 pb-2 text-xs font-semibold uppercase tracking-wider text-text-muted">User Hierarchy</div>
                        <a href="{{ route('admin.managers.index') }}" class="flex items-center px-4 py-3 text-text-heading rounded-lg hover:bg-gradient-to-r hover:from-brand-gold hover:via-brand-amber hover:to-brand-crimson hover:text-white transition-all duration-300 {{ request()->routeIs('admin.managers.*') ? 'bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white shadow-md' : '' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            Managers
                        </a>
                        <a href="{{ route('admin.rms.index') }}" class="flex items-center px-4 py-3 text-text-heading rounded-lg hover:bg-gradient-to-r hover:from-brand-gold hover:via-brand-amber hover:to-brand-crimson hover:text-white transition-all duration-300 {{ request()->routeIs('admin.rms.*') ? 'bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white shadow-md' : '' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m9-7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            Relationship Managers
                        </a>
                        <a href="{{ route('admin.affiliates.index') }}" class="flex items-center px-4 py-3 text-text-heading rounded-lg hover:bg-gradient-to-r hover:from-brand-gold hover:via-brand-amber hover:to-brand-crimson hover:text-white transition-all duration-300 {{ request()->routeIs('admin.affiliates.*') ? 'bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white shadow-md' : '' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                            </svg>
                            Affiliates
                        </a>
                        <a href="{{ route('admin.commissions.index') }}" class="flex items-center px-4 py-3 text-text-heading rounded-lg hover:bg-gradient-to-r hover:from-brand-gold hover:via-brand-amber hover:to-brand-crimson hover:text-white transition-all duration-300 {{ request()->routeIs('admin.commissions.*') ? 'bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white shadow-md' : '' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Commissions
                        </a>
                        <a href="{{ route('admin.withdrawals.index') }}" class="flex items-center px-4 py-3 text-text-heading rounded-lg hover:bg-gradient-to-r hover:from-brand-gold hover:via-brand-amber hover:to-brand-crimson hover:text-white transition-all duration-300 {{ request()->routeIs('admin.withdrawals.*') ? 'bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white shadow-md' : '' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            Withdrawals
                        </a>
                        <a href="{{ route('admin.commission-settings.index') }}" class="flex items-center px-4 py-3 text-text-heading rounded-lg hover:bg-gradient-to-r hover:from-brand-gold hover:via-brand-amber hover:to-brand-crimson hover:text-white transition-all duration-300 {{ request()->routeIs('admin.commission-settings.*') ? 'bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white shadow-md' : '' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Commission Settings
                        </a>
                    </div>
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

