# Task Manager

A straightforward task management app built with Laravel and Livewire for the Qtec Solution Limited assessment.

## Stack
- Laravel 10
- Livewire 3
- Tailwind CSS
- MySQL

## Features
- Auth via Laravel Breeze
- CRUD operations for tasks (create, edit, delete)
- Update task status (pending, in progress, completed)
- Fast interactions using Livewire (no full page reloads)

## How to run locally

1. Run `composer install` and `npm install`
2. Compile assets: `npm run build`
3. Copy `.env.example` to `.env`, then add your database credentials. (I used a local mysql db named `task_manager`)
4. Generate the app key: `php artisan key:generate`
5. Run migrations: `php artisan migrate`
6. Start the server: `php artisan serve`

You can also run `npm run dev` if you need to mess with the frontend.

## Testing
Just run `php artisan test`. Tests cover creating, updating, deleting tasks, and making sure a user can't edit someone else's tasks.
