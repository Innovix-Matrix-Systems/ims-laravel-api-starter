## About this project

`ims-laravel-api-starter` is a streamlined backend API starter application built using the powerful [Laravel](https://laravel.com/) framework.

Our primary focus is to provide you with a hassle-free and ready-to-use local development starter project. Unlike traditional API generators or code generators, this project aims to simplify the process of setting up your local development environment quickly and efficiently.

With `ims-laravel-api-starter`, you can jumpstart your Laravel-based API development without unnecessary complexities, allowing you to focus on building your application logic rather than spending time on initial setup.

Explore this project and experience the convenience of a ready-made local development environment for your Laravel-based APIs.

## Features

-   **Authentication using Laravel Sanctum**: Implement secure authentication using [Laravel Sanctum](https://laravel.com/docs/11.x/sanctum).

-   **Extra Security with XSECURE**: IMS introduces an additional layer of security, enhancing the API's reliability and resilience [XSECURE Mode](https://github.com/Innovix-Matrix-Systems/ims-laravel-api-starter/wiki/XSECURE-setup).

-   **Role & Permission-Based Authorization**: Utilize [Laravel Permission](https://spatie.be/docs/laravel-permission/v6/introduction) for a flexible authorization system based on roles and permissions.

-   **Multiple Language Support**: Provide a multilingual experience with [Laravel Lang](https://laravel-lang.com/) to make your application accessible to a diverse user base.

-   **Application and Database Backup System**: Provide an application and database backup system with [laravel-backup](https://spatie.be/docs/laravel-backup/v8/introduction) to make your application safe and ready for quick recovery.

## Getting Started

1. **Choose Your Local Development Tool:**

   Select your preferred local development tool, such as [Laragon](https://github.com/leokhoa/laragon),[Laravel Herd](https://herd.laravel.com), XAMPP, WAMP, or any other tool that suits your needs.

   ### Version Requirments ###
   - Node 16+
   - PHP version 8.2+
   - MYSQL version 8.0+


2. **Configure Your Environment:**

   Update your `.env` file with the correct database credentials.

   *Copy .env.example to .env:*

   Before proceeding, copy the .env.example file to .env to set up your environment variables:

   ```bash
   cp .env.example .env
   ```


3. **Install Dependencies:**

   To install local development packages, including Husky and other Laravel-specific packages, run the following commands:

   ```bash
   npm install #for husky and other Laravel packages
   npx husky install #only once
   ```

   Run the following command to install the required dependencies using Composer:

   ```bash
   composer install
   ```

4. **Migrate and Seed the Database:**
    Initialize and seed the database with default data using:
    ```bash 
    php artisan migrate --seed
    ```

    Now, your project is ready for use. You can use the postman collection provided in the repo to start playing with the API. If you've run the seed command, log in with the provided credentials. Customize and expand your application as needed.

## XSECURE MODE
Please visit this [wiki page](https://github.com/Innovix-Matrix-Systems/ims-laravel-api-starter/wiki/XSECURE-setup) for XSECURE guide

## WIKI
Please Visit the [WIKI](https://github.com/Innovix-Matrix-Systems/ims-laravel-api-starter/wiki) Page for XSECURE, Docker Guide, Extra Artisan commands and much more.

## Authors

-   [@AHS12](https://www.github.com/AHS12)

## License

This project is brought to you by Innovix Matrix System and is released as open-source software under the [MIT license](https://opensource.org/licenses/MIT).

Feel free to use, modify, and distribute this starter project in accordance with the MIT license terms. We encourage collaboration and welcome contributions from the community to make this project even better.
