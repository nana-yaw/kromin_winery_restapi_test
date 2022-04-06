# Kromin Italian Winery REST API App 
## Application summary
These REST APIs allow an Italian winery to showcase online all their most prestigious bottles of wine.

Users can check the bottles out through a URL and Admins can edit the information to showcase.

## Pre-requisites
- Composer. More on [this](https://getcomposer.org).
- Laravel 8. Check out the Laravel 8 [docs](https://laravel.com/docs/8.x) for more info.
- MySQL Database (or your preferred SQL Database).
- Your preferred text editor (VS Code) or PHP IDE (PHPStorm) etc.

## Setting Up Development Environment
- Clone this [repo](https://github.com/nana-yaw/kromin_winery_restapi_test.git)
- In your OS terminal, `cd` into the root folder/directory of this repo and run `composer install`.
- After composer is done installing dependencies, run this command in your terminal: `php artisan key:generate`.
- Also run the terminal command `php artisan passport:install` to generate your `Oauth` client keys. More on [this](https://laravel.com/docs/8.x/passport#installation)
- Run the terminal command `php artisan storage:link`. More on [this](https://laravel.com/docs/8.x/filesystem#the-public-disk)
- Run the terminal command `php artisan migrate:fresh --seed` to set up your app database tables and with mock data.
- Finally, run the terminal command `php artisan serve` to start your local REST API server.

Happy hacking!!!
