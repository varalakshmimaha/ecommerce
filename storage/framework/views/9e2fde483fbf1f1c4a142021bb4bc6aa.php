<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('title', 'Suvee - Premium E-commerce'); ?></title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-md sticky top-0 z-50">
        <div class="container mx-auto px-4">
            <!-- Mobile Menu Toggle -->
            <div class="flex items-center justify-between py-4 md:hidden">
                <button id="mobile-menu-toggle" class="text-gray-700 hover:text-primary-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
                <a href="<?php echo e(route('home')); ?>" class="flex items-center">
                    <?php
                        $logo = \App\Models\Setting::get('company_logo');
                    ?>
                    <?php if($logo): ?>
                        <img src="<?php echo e(asset('storage/' . $logo)); ?>" alt="Logo" class="h-10">
                    <?php else: ?>
                        <span class="text-2xl font-bold text-primary-600">Suvee</span>
                    <?php endif; ?>
                </a>
                <a href="<?php echo e(route('cart')); ?>" class="relative">
                    <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <span class="cart-count absolute -top-2 -right-2 bg-primary-600 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">0</span>
                </a>
            </div>
            
            <!-- Desktop & Mobile Layout -->
            <div class="hidden md:flex items-center justify-between py-4">
                <!-- Left Navigation -->
                <?php
                    $navPages = \App\Models\Page::where('show_in_navbar', true)->where('is_active', true)->orderBy('sort_order')->get();
                ?>
                <nav class="flex items-center space-x-6">
                    <a href="<?php echo e(route('home')); ?>" class="text-gray-700 hover:text-primary-600 transition-colors font-medium">Home</a>
                    <a href="<?php echo e(route('products.index')); ?>" class="text-gray-700 hover:text-primary-600 transition-colors font-medium">Products</a>
                    <?php $__currentLoopData = $navPages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $navPage): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e(route('page.show', $navPage)); ?>" class="text-gray-700 hover:text-primary-600 transition-colors font-medium"><?php echo e($navPage->title); ?></a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </nav>
                
                <!-- Center Logo -->
                <div class="flex-1 flex justify-center">
                    <a href="<?php echo e(route('home')); ?>" class="flex items-center">
                        <?php
                            $logo = \App\Models\Setting::get('company_logo');
                        ?>
                        <?php if($logo): ?>
                            <img src="<?php echo e(asset('storage/' . $logo)); ?>" alt="Logo" class="h-12">
                        <?php else: ?>
                            <span class="text-3xl font-bold text-primary-600">Suvee</span>
                        <?php endif; ?>
                    </a>
                </div>
                
                <!-- Right Actions -->
                <div class="flex items-center space-x-4">
                    <form action="<?php echo e(route('products.index')); ?>" method="GET" class="hidden lg:block">
                        <input type="text" name="search" placeholder="Search products..." value="<?php echo e(request('search')); ?>" 
                            class="input-field w-64">
                    </form>
                    <a href="<?php echo e(route('cart')); ?>" class="relative">
                        <svg class="w-6 h-6 text-gray-700 hover:text-primary-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <span class="cart-count absolute -top-2 -right-2 bg-primary-600 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">0</span>
                    </a>
                </div>
            </div>
            
            <!-- Mobile Menu -->
            <div id="mobile-menu" class="hidden md:hidden pb-4 border-t border-gray-200 mt-2">
                <nav class="flex flex-col space-y-3 pt-4">
                    <a href="<?php echo e(route('home')); ?>" class="text-gray-700 hover:text-primary-600 transition-colors font-medium py-2">Home</a>
                    <a href="<?php echo e(route('products.index')); ?>" class="text-gray-700 hover:text-primary-600 transition-colors font-medium py-2">Products</a>
                    <?php $__currentLoopData = \App\Models\Page::where('show_in_navbar', true)->where('is_active', true)->orderBy('sort_order')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mobileNavPage): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e(route('page.show', $mobileNavPage)); ?>" class="text-gray-700 hover:text-primary-600 transition-colors font-medium py-2"><?php echo e($mobileNavPage->title); ?></a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <form action="<?php echo e(route('products.index')); ?>" method="GET" class="pt-2">
                        <input type="text" name="search" placeholder="Search products..." value="<?php echo e(request('search')); ?>" 
                            class="input-field">
                    </form>
                </nav>
            </div>
        </div>
    </header>

    <!-- Immediate client-side cart count (runs before DOMContentLoaded) -->
    <script>
        (function(){
            try {
                const raw = localStorage.getItem('cart');
                const cart = raw ? JSON.parse(raw) : [];
                const count = cart.reduce((s,i) => s + (i.quantity || 0), 0);
                document.querySelectorAll('.cart-count').forEach(el => el.textContent = count);
            } catch (e) {
                // ignore
            }
        })();
    </script>

    <!-- Top Banner -->
    <?php
        $banners = \App\Models\Banner::where('is_active', true)->orderBy('sort_order')->get();
    ?>
    <?php if($banners->count() > 0): ?>
    <div class="bg-primary-600 text-white py-2">
        <div class="container mx-auto px-4">
            <div class="text-center">
                <?php if($banners->first()->title): ?>
                    <p class="text-sm font-medium"><?php echo e($banners->first()->title); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Main Content -->
    <main>
        <?php echo $__env->yieldContent('content'); ?>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white mt-16">
        <div class="container mx-auto px-4 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <?php if($logo): ?>
                        <img src="<?php echo e(asset('storage/' . $logo)); ?>" alt="Logo" class="h-12 mb-4">
                    <?php else: ?>
                        <h3 class="text-2xl font-bold mb-4">Suvee</h3>
                    <?php endif; ?>
                    <p class="text-gray-400 text-sm"><?php echo e(\App\Models\Setting::get('company_description', 'Premium e-commerce platform')); ?></p>
                </div>
                <?php
                    $footerPages = \App\Models\Page::where('show_in_footer', true)->where('is_active', true)->orderBy('sort_order')->get();
                    $footerSections = \App\Models\FooterSection::with('links')->where('is_active', true)->orderBy('sort_order')->get();
                ?>
                <div>
                    <h4 class="font-semibold mb-4">Customer Service</h4>
                    <ul class="space-y-2">
                        <?php $__currentLoopData = $footerPages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fpage): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li>
                                <a href="<?php echo e(route('page.show', $fpage)); ?>" class="text-gray-400 hover:text-white transition-colors text-sm"><?php echo e($fpage->title); ?></a>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
                <?php $__currentLoopData = $footerSections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div>
                    <h4 class="font-semibold mb-4"><?php echo e($section->title); ?></h4>
                    <ul class="space-y-2">
                        <?php $__currentLoopData = $section->links->where('is_active', true); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li>
                            <a href="<?php echo e($link->url ?? '#'); ?>" class="text-gray-400 hover:text-white transition-colors text-sm"><?php echo e($link->title); ?></a>
                        </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <div>
                    <h4 class="font-semibold mb-4">Contact Us</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <?php if(\App\Models\Setting::get('whatsapp_number')): ?>
                        <li>WhatsApp: <?php echo e(\App\Models\Setting::get('whatsapp_number')); ?></li>
                        <?php endif; ?>
                        <?php if(\App\Models\Setting::get('phone_number')): ?>
                        <li>Phone: <?php echo e(\App\Models\Setting::get('phone_number')); ?></li>
                        <?php endif; ?>
                        <?php if(\App\Models\Setting::get('email')): ?>
                        <li>Email: <?php echo e(\App\Models\Setting::get('email')); ?></li>
                        <?php endif; ?>
                        <?php if(\App\Models\Setting::get('address')): ?>
                        <li><?php echo e(\App\Models\Setting::get('address')); ?></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400 text-sm">
                <p>&copy; <?php echo e(date('Y')); ?> Suvee. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Floating WhatsApp Button -->
    <?php if(\App\Models\Setting::get('whatsapp_number')): ?>
    <a href="https://wa.me/<?php echo e(str_replace('+', '', \App\Models\Setting::get('whatsapp_number'))); ?>" 
       target="_blank" 
       class="fixed bottom-6 right-6 bg-green-500 hover:bg-green-600 text-white rounded-full p-4 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-110 animate-bounce-slow z-50">
        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
        </svg>
    </a>
    <?php endif; ?>

    <!-- Global modal for messages -->
    <div id="appModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-lg max-w-lg w-full animate-fade-in">
            <div class="p-4">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 id="appModalTitle" class="text-lg font-bold"></h3>
                        <p id="appModalBody" class="text-sm text-gray-700 mt-2"></p>
                    </div>
                    <button onclick="closeModal()" class="text-gray-500">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const API_BASE = '<?php echo e(url('/api')); ?>';
        
        // Mobile Menu Toggle
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
            const mobileMenu = document.getElementById('mobile-menu');
            
            if (mobileMenuToggle && mobileMenu) {
                mobileMenuToggle.addEventListener('click', function() {
                    mobileMenu.classList.toggle('hidden');
                    // Toggle icon
                    const icon = mobileMenuToggle.querySelector('svg');
                    if (mobileMenu.classList.contains('hidden')) {
                        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>';
                    } else {
                        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>';
                    }
                });
            }
        });

        // Helper to show modal messages (supports type: 'success'|'error'|'info')
        function showModal(title, message, type = 'info') {
            const modal = document.getElementById('appModal');
            const titleEl = document.getElementById('appModalTitle');
            const bodyEl = document.getElementById('appModalBody');
            titleEl.textContent = title;
            bodyEl.textContent = message;
            // style based on type
            titleEl.className = 'text-lg font-bold ' + (type === 'success' ? 'text-green-600' : (type === 'error' ? 'text-red-600' : 'text-gray-900'));
            modal.querySelector('.animate-fade-in')?.classList.remove('ring-4','ring-green-100','ring-red-100');
            if (type === 'success') modal.querySelector('.animate-fade-in')?.classList.add('ring-4','ring-green-100');
            if (type === 'error') modal.querySelector('.animate-fade-in')?.classList.add('ring-4','ring-red-100');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            // gentle auto close
            setTimeout(() => closeModal(), 3600);
        }

        function closeModal() {
            const modal = document.getElementById('appModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

            // Global cart count updater — updates all cart-count badges
        function updateCartCount() {
            const cart = JSON.parse(localStorage.getItem('cart') || '[]');
            const count = cart.reduce((sum, item) => sum + item.quantity, 0);
            document.querySelectorAll('.cart-count').forEach(el => el.textContent = count);
        }
    </script>
</body>
</html>

<?php /**PATH /Users/vikasverma/vikas/suvee/resources/views/layouts/frontend.blade.php ENDPATH**/ ?>