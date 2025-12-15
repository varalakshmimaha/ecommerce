# Suvee - Premium E-commerce Platform

A premium Laravel-based e-commerce platform with manual payment verification, featuring a beautiful admin panel and responsive frontend.

## Features

### Frontend
- ✅ Premium UI with Tailwind CSS
- ✅ Responsive design (mobile-first)
- ✅ Animated banner slider
- ✅ Product listing with pagination
- ✅ Product detail pages
- ✅ Single-page checkout
- ✅ Guest checkout (no account required)
- ✅ Manual payment (QR code + Bank transfer)
- ✅ Payment proof upload
- ✅ Floating WhatsApp button
- ✅ Dynamic footer

### Admin Panel
- ✅ Clean dashboard with statistics
- ✅ Product management (CRUD)
- ✅ Category & Sub-category management
- ✅ Order management
- ✅ Payment verification
- ✅ Banner management
- ✅ Reports with Excel export
- ✅ Settings management (Company details, Bank details, Footer)

### Technical Features
- ✅ REST API architecture
- ✅ Mobile-based authentication
- ✅ OTP verification (during registration)
- ✅ CKEditor for rich text
- ✅ Excel export for reports
- ✅ Image uploads
- ✅ Stock management
- ✅ Order tracking

## Installation

### Prerequisites
- PHP >= 8.1
- Composer
- MySQL
- Node.js & NPM

### Steps

1. **Install Dependencies**
```bash
composer install
npm install
```

2. **Environment Setup**
```bash
cp .env.example .env
php artisan key:generate
```

3. **Configure Database**
Edit `.env` file and set your database credentials:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=suvee
DB_USERNAME=root
DB_PASSWORD=your_password
```

4. **Run Migrations**
```bash
php artisan migrate
```

5. **Create Storage Link**
```bash
php artisan storage:link
```

6. **Create Admin User**
You can create an admin user using Laravel Tinker:
```bash
php artisan tinker
```
Then run:
```php
$user = new App\Models\User();
$user->mobile = '1234567890';
$user->email = 'admin@example.com';
$user->password = Hash::make('password');
$user->is_admin = true;
$user->is_verified = true;
$user->save();
```

7. **Build Assets**
```bash
npm run dev
# Or for production:
npm run build
```

8. **Start Development Server**
```bash
php artisan serve
```

## Usage

### Admin Panel
- Access admin panel at: `http://localhost:8000/admin/dashboard`
- Login with your admin credentials

### API Endpoints

#### Public Endpoints
- `GET /api/products` - List products
- `GET /api/products/{slug}` - Get product details
- `GET /api/categories` - List categories
- `GET /api/banners` - List banners
- `POST /api/register` - User registration
- `POST /api/verify-otp` - Verify OTP
- `POST /api/login` - User login
- `POST /api/checkout/calculate` - Calculate order total
- `POST /api/checkout/place-order` - Place order

### Admin Routes
- `/admin/dashboard` - Dashboard
- `/admin/products` - Products management
- `/admin/orders` - Orders management
- `/admin/categories` - Categories management
- `/admin/banners` - Banners management
- `/admin/reports` - Reports
- `/admin/settings` - Settings

## Configuration

### Payment Settings
Configure payment details in Admin Panel > Settings:
- Upload QR code image
- Enter bank details (Bank name, Account number, IFSC, Account holder)

### Company Settings
Configure company information in Admin Panel > Settings:
- Company logo
- Company description
- Contact details (WhatsApp, Phone, Email, Address)

### Footer Settings
Manage footer sections and links in Admin Panel > Settings

## Currency
All prices are displayed in Indian Rupees (₹).

## Notes
- OTP verification is currently returned in API response (for development). In production, integrate with SMS gateway.
- Payment verification is manual - admin needs to verify payment proofs.
- Stock is automatically decremented when order is placed.

## License
MIT License

