# URL Shortener

A simple URL shortener app made with Laravel 12, MySQL, Bootstrap and jQuery.

This app has 3 types of users:
- **SuperAdmin** – can see all short URLs from every company
- **Admin** – can see all short URLs of their own company
- **Member** – can only see the short URLs they created

## What You Need Before Starting

Make sure these are installed on your computer:

- PHP 8.2 or higher
- Composer
- MySQL
- Node.js and NPM (only if you want to run frontend build tools)

## Setup Steps

### 1. Install the project files

Open your terminal and go to the project folder. Then run:

```bash
composer install
```

This will download all the PHP packages the project needs.

### 2. Create your `.env` file

Copy the example file and rename it:

```bash
cp .env.example .env
```

### 3. Generate the app key

```bash
php artisan key:generate
```

### 4. Create a database

Open MySQL and create an empty database. You can name it `url_shortner`.

```sql
CREATE DATABASE url_shortner;
```

### 5. Update the `.env` file

Open the `.env` file and set your database details:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=url_shortner
DB_USERNAME=root
DB_PASSWORD=your_password
```

Change `your_password` to your real MySQL password. If you don't have a password, leave it empty.

### 6. Run the migrations and seed the database

This will create all tables and add a default SuperAdmin user:

```bash
php artisan migrate --seed
```

### 7. Start the local server

```bash
php artisan serve
```

Now open your browser and go to:

```
http://localhost:8000
```

## Login Details

Use this account to log in as SuperAdmin:

- **Email:** superadmin@example.com
- **Password:** password

From here, the SuperAdmin can invite an Admin, and the Admin can invite more Admins or Members.

## How to Test the App

You can test the app in two ways:

### A) Test manually in the browser

1. Log in as SuperAdmin
2. Create a new company by inviting an Admin
3. Copy the invite link shown on screen and open it (or share with a friend)
4. Set a password to finish creating the Admin account
5. Log in as Admin and try creating a short URL
6. Try inviting a Member and test their permissions too

### B) Run the automated tests

The project comes with ready-made tests that check all the rules (who can create URLs, who can see what, etc.). Run them with:

```bash
php artisan test
```

If everything is working, you will see all tests pass in green.

## Common Problems

**Problem:** `SQLSTATE[HY000] [1049] Unknown database`
**Fix:** You forgot to create the database in MySQL. Go back to step 4.

**Problem:** Page shows a blank white screen or error about app key
**Fix:** Run `php artisan key:generate` again.

**Problem:** Styles or Bootstrap not showing properly
**Fix:** Make sure you have an internet connection, since Bootstrap and jQuery are loaded from CDN links.

## Tech Used

- Laravel 12
- MySQL
- Bootstrap 5
- jQuery + jQuery Validation
