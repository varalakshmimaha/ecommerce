<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Product;
use App\Models\Banner;
use App\Models\Setting;
use App\Models\FooterSection;
use App\Models\FooterLink;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create Admin User
        User::updateOrCreate(
            ['mobile' => '1234567890'],
            [
                'email' => 'admin@suvee.com',
                'password' => Hash::make('admin123'),
                'name' => 'Admin User',
                'is_admin' => true,
                'is_verified' => true,
            ]
        );

        // Create Sample User
        User::updateOrCreate(
            ['mobile' => '9876543210'],
            [
                'email' => 'user@suvee.com',
                'password' => Hash::make('user123'),
                'name' => 'Test User',
                'is_admin' => false,
                'is_verified' => true,
            ]
        );

        // Create Categories
        $electronics = Category::updateOrCreate(
            ['slug' => 'electronics'],
            [
                'name' => 'Electronics',
                'description' => 'Electronic products and gadgets',
                'is_active' => true,
                'sort_order' => 1,
            ]
        );

        $fashion = Category::updateOrCreate(
            ['slug' => 'fashion'],
            [
                'name' => 'Fashion',
                'description' => 'Fashion and clothing',
                'is_active' => true,
                'sort_order' => 2,
            ]
        );

        $home = Category::updateOrCreate(
            ['slug' => 'home-living'],
            [
                'name' => 'Home & Living',
                'description' => 'Home decor and living essentials',
                'is_active' => true,
                'sort_order' => 3,
            ]
        );

        // Create Sub Categories
        $mobilePhones = SubCategory::updateOrCreate(
            ['slug' => 'mobile-phones'],
            [
                'category_id' => $electronics->id,
                'name' => 'Mobile Phones',
                'is_active' => true,
                'sort_order' => 1,
            ]
        );

        $laptops = SubCategory::updateOrCreate(
            ['slug' => 'laptops'],
            [
                'category_id' => $electronics->id,
                'name' => 'Laptops',
                'is_active' => true,
                'sort_order' => 2,
            ]
        );

        $mensWear = SubCategory::updateOrCreate(
            ['slug' => 'mens-wear'],
            [
                'category_id' => $fashion->id,
                'name' => "Men's Wear",
                'is_active' => true,
                'sort_order' => 1,
            ]
        );

        $womensWear = SubCategory::updateOrCreate(
            ['slug' => 'womens-wear'],
            [
                'category_id' => $fashion->id,
                'name' => "Women's Wear",
                'is_active' => true,
                'sort_order' => 2,
            ]
        );

        // Create Sample Products
        $product1 = Product::updateOrCreate(
            ['slug' => 'premium-smartphone'],
            [
            'name' => 'Premium Smartphone',
            'slug' => 'premium-smartphone',
            'category_id' => $electronics->id,
            'sub_category_id' => $mobilePhones->id,
            'mrp' => 50000.00,
            'selling_price' => 45000.00,
            'discounted_price' => 39999.00,
            'gst' => 18.00,
            'min_order_quantity' => 1,
            'stock_quantity' => 50,
            'main_image' => 'products/sample-phone.jpg',
            'short_description' => 'Latest premium smartphone with advanced features',
            'full_description' => '<p>Experience the future with our premium smartphone featuring cutting-edge technology, stunning display, and powerful performance.</p>',
            'is_new_arrival' => true,
            'is_featured' => true,
            'is_trending' => true,
            'is_top_rated' => true,
            'status' => 'published',
            ]
        );

        $product2 = Product::updateOrCreate(
            ['slug' => 'gaming-laptop'],
            [
            'name' => 'Gaming Laptop',
            'slug' => 'gaming-laptop',
            'category_id' => $electronics->id,
            'sub_category_id' => $laptops->id,
            'mrp' => 120000.00,
            'selling_price' => 99999.00,
            'discounted_price' => 89999.00,
            'gst' => 18.00,
            'min_order_quantity' => 1,
            'stock_quantity' => 25,
            'main_image' => 'products/sample-laptop.jpg',
            'short_description' => 'High-performance gaming laptop for professionals',
            'full_description' => '<p>Powerful gaming laptop with latest graphics card, fast processor, and premium build quality.</p>',
            'is_featured' => true,
            'is_trending' => true,
            'status' => 'published',
            ]
        );

        $product3 = Product::updateOrCreate(
            ['slug' => 'designer-t-shirt'],
            [
            'name' => 'Designer T-Shirt',
            'slug' => 'designer-t-shirt',
            'category_id' => $fashion->id,
            'sub_category_id' => $mensWear->id,
            'mrp' => 2000.00,
            'selling_price' => 1500.00,
            'discounted_price' => 1299.00,
            'gst' => 12.00,
            'min_order_quantity' => 1,
            'stock_quantity' => 100,
            'main_image' => 'products/sample-tshirt.jpg',
            'short_description' => 'Comfortable and stylish designer t-shirt',
            'full_description' => '<p>Premium quality cotton t-shirt with modern design and comfortable fit.</p>',
            'is_new_arrival' => true,
            'is_featured' => true,
            'status' => 'published',
            ]
        );

        // Create Banners
        Banner::updateOrCreate(
            ['image' => 'banners/banner1.jpg'],
            [
            'title' => 'Welcome to Suvee',
            'description' => 'Premium E-commerce Platform',
            'image' => 'banners/banner1.jpg',
            'link' => '/products',
            'is_active' => true,
            'sort_order' => 1,
            ]
        );

        Banner::updateOrCreate(
            ['image' => 'banners/banner2.jpg'],
            [
            'title' => 'New Arrivals',
            'description' => 'Check out our latest products',
            'image' => 'banners/banner2.jpg',
            'link' => '/products?type=new-arrival',
            'is_active' => true,
            'sort_order' => 2,
            ]
        );

        // Create Settings
        Setting::set('company_logo', '');
        Setting::set('company_description', 'Suvee - Your trusted e-commerce platform for premium products');
        Setting::set('whatsapp_number', '+91 1234567890');
        Setting::set('phone_number', '+91 1234567890');
        Setting::set('email', 'info@suvee.com');
        Setting::set('address', '123 Main Street, City, State, PIN - 123456');
        Setting::set('bank_name', 'Sample Bank');
        Setting::set('bank_account_number', '1234567890123456');
        Setting::set('bank_ifsc', 'SAMP0001234');
        Setting::set('bank_account_holder', 'Suvee E-commerce');

        // Create Footer Sections
        $quickLinks = FooterSection::updateOrCreate(
            ['title' => 'Quick Links'],
            [
            'title' => 'Quick Links',
            'sort_order' => 1,
            'is_active' => true,
            ]
        );

        $customerService = FooterSection::updateOrCreate(
            ['title' => 'Customer Service'],
            [
            'title' => 'Customer Service',
            'sort_order' => 2,
            'is_active' => true,
            ]
        );

        // Create Footer Links
        FooterLink::updateOrCreate(
            [
                'footer_section_id' => $quickLinks->id,
                'title' => 'Home',
            ],
            [
            'footer_section_id' => $quickLinks->id,
            'title' => 'Home',
            'url' => '/',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        FooterLink::updateOrCreate(
            [
                'footer_section_id' => $quickLinks->id,
                'title' => 'Products',
            ],
            [
                'url' => '/products',
                'sort_order' => 2,
                'is_active' => true,
            ]
        );

        FooterLink::updateOrCreate(
            [
                'footer_section_id' => $customerService->id,
                'title' => 'Contact Us',
            ],
            [
                'url' => '/contact',
                'sort_order' => 1,
                'is_active' => true,
            ]
        );

        FooterLink::updateOrCreate(
            [
                'footer_section_id' => $customerService->id,
                'title' => 'About Us',
            ],
            [
                'url' => '/about',
                'sort_order' => 2,
                'is_active' => true,
            ]
        );

        $this->command->info('Database seeded successfully!');
        $this->command->info('Admin Login: Mobile - 1234567890, Password - admin123');
        $this->command->info('User Login: Mobile - 9876543210, Password - user123');
    }
}

