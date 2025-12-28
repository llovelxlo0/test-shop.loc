**Production-ready portfolio project** demonstrating practical Laravel
skills, clean MVC architecture, and real-world features of an online
store.

This project is intended for **GitHub portfolio presentation** and
evaluation by recruiters.

------------------------------------------------------------------------

## 🔍 Project Overview

A fully functional e-commerce application built with **Laravel**,
featuring a product catalog, review system with moderation, role-based
access control, and AJAX-powered filtering.

The project focuses on **backend correctness, authorization logic, and
maintainable Blade architecture**, rather than visual complexity.

------------------------------------------------------------------------

## 🚀 Key Features

### User & Auth

-   User registration and authentication
-   Role-based access (User / Admin)
-   Wishlist functionality
-   Product view history

### Product Catalog

-   Responsive product grid layout (marketplace-style)
-   Categories and subcategories
-   Product detail page
-   Related products
-   Stock availability
-   Image handling via Laravel Storage

### Reviews System

-   Product reviews with rating (1--5)
-   Optional image upload in reviews
-   Nested replies to reviews
-   Review usefulness voting (like / dislike)
-   Sorting:
    -   by date
    -   by usefulness score

### Moderation & Security

-   Review moderation workflow:
    -   `pending`
    -   `approved`
    -   `rejected`
-   Admin can change review status directly from UI
-   Authorization via **Laravel Policies**
-   Form validation via **Form Requests**
-   CSRF protection

### AJAX

-   Product filtering without page reload
-   Fetch API usage
-   Blade partials for dynamic content updates

------------------------------------------------------------------------

## 🧠 Technical Highlights (For Recruiters)

-   Clean **MVC separation**
-   Correct usage of **Eloquent relationships**
-   Authorization logic with **Policies**
-   Non-SPA AJAX approach (progressive enhancement)
-   Reusable Blade components
-   Storage abstraction for file uploads
-   Readable and maintainable code structure

------------------------------------------------------------------------

## 🧰 Tech Stack

**Backend** - PHP 8+ - Laravel - Eloquent ORM - Laravel Policies &
Requests

**Frontend** - Blade Templates - Bootstrap 5 - JavaScript (Fetch API)

**Database** - MySQL

------------------------------------------------------------------------

## 📁 Project Structure (Excerpt)

    app/
     ├── Models/
     │   ├── Goods.php
     │   ├── Review.php
     │   └── User.php
     ├── Http/
     │   ├── Controllers/
     │   ├── Requests/
     │   └── Policies/
    resources/
     ├── views/
     │   ├── goods/
     │   ├── partials/
     │   └── components/
    routes/
     └── web.php

------------------------------------------------------------------------

## ⚙️ Installation

``` bash
git clone https://github.com/your-username/laravel-ecommerce.git
cd laravel-ecommerce
composer install
npm install
npm run build
```

``` bash
cp .env.example .env
php artisan key:generate
```

Configure database connection in `.env`, then:

``` bash
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

------------------------------------------------------------------------

## 👑 Admin Access

To enable admin privileges:

``` bash
php artisan tinker
```

``` php
$user = User::find(1);
$user->is_admin = true;
$user->save();
```

------------------------------------------------------------------------

## 🏷 Review Status System

``` php
STATUS_PENDING
STATUS_APPROVED
STATUS_REJECTED
```

Helper methods:

``` php
$review->isPending();
$review->isApproved();
$review->isRejected();
```

------------------------------------------------------------------------

## 🎯 Project Goals

-   Demonstrate real Laravel backend skills
-   Show understanding of authorization & security
-   Apply clean Blade architecture without SPA frameworks
-   Build a realistic business-oriented application

------------------------------------------------------------------------

## 🛠 Possible Enhancements

-   Pagination
-   Full-text product search
-   Admin dashboard
-   REST API
-   Vue / React frontend
-   Docker environment

------------------------------------------------------------------------

## 📄 License

This project is open-source and used for portfolio purposes.
