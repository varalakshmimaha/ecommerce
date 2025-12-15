# Database Setup Instructions

## Issue
The migration is failing because the database doesn't exist or MySQL is not running.

## Solutions

### Option 1: Create Database Manually (Recommended)

1. **Start MySQL Server** (if not running):
   ```bash
   # On macOS with Homebrew:
   brew services start mysql
   
   # Or if using MAMP/XAMPP, start it from their control panel
   ```

2. **Create the Database**:
   ```bash
   # Using MySQL command line:
   mysql -u root -p
   ```
   Then in MySQL:
   ```sql
   CREATE DATABASE suvee CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   EXIT;
   ```

   Or if you don't have a password:
   ```bash
   mysql -u root -e "CREATE DATABASE suvee CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
   ```

3. **Update .env file** (if needed):
   Make sure your `.env` has:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=suvee
   DB_USERNAME=root
   DB_PASSWORD=your_password_here
   ```

4. **Run Migrations**:
   ```bash
   php artisan migrate
   ```

### Option 2: Use SQLite (For Quick Testing)

If MySQL is not available, you can use SQLite:

1. **Update .env**:
   ```
   DB_CONNECTION=sqlite
   # Remove or comment out DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD
   ```

2. **Create SQLite Database**:
   ```bash
   touch database/database.sqlite
   ```

3. **Run Migrations**:
   ```bash
   php artisan migrate
   ```

### Option 3: Check MySQL Connection

Test if MySQL is accessible:
```bash
mysql -u root -p -e "SHOW DATABASES;"
```

If this fails, MySQL is not running or not configured correctly.

