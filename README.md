Math Class Booking System
This is a backend application built with Laravel 9 for managing Math classes' bookings. It provides endpoints to view available classes, book classes, and cancel bookings.

Getting Started
To get a local copy up and running, follow these simple steps.

Prerequisites
PHP >= 7.4
Composer
MySQL
Node.js (for frontend assets, if applicable)
Installation
Clone the repo
sh
Copy code
git clone https://github.com/berin99/technical_backend_wcn_berin
Install PHP dependencies
sh
Copy code
composer install
Create a copy of the .env.example file and rename it to .env. Update the database and other configuration values as needed.
sh
Copy code
cp .env.example .env
Generate application key
sh
Copy code
php artisan key:generate
Run database migrations
sh
Copy code
php artisan migrate
(Optional) Seed the database with sample data
sh
Copy code
php artisan db:seed
Start the development server
sh
Copy code
php artisan serve
Usage
Use the provided endpoints to manage Math class bookings. See API documentation for details.
API Documentation
Endpoint documentation and usage examples can be found in the attachment.

Contact
Your Name - yberin@gmail.com

Project Link: https://github.com/berin99/technical_backend_wcn_berin
