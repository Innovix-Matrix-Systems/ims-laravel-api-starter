## About this project

`ims-laravel-api-starter` is a streamlined backend API starter application built using the powerful [Laravel](https://laravel.com/) framework.

Our primary focus is to provide you with a hassle-free and ready-to-use local development starter project. Unlike traditional API generators or code generators, this project aims to simplify the process of setting up your local development environment quickly and efficiently.

With `ims-laravel-api-starter`, you can jumpstart your Laravel-based API development without unnecessary complexities, allowing you to focus on building your application logic rather than spending time on initial setup.

Explore this project and experience the convenience of a ready-made local development environment for your Laravel-based APIs.

## Required Commands to run locally

1.**Create Your Project from the Template:**

Begin by creating your project using the provided template.

2.**Clone the Project:**

Clone the created project repository to your local machine by running the following command, replacing `#your project git url` with your project's Git URL:

```bash
git clone git@github.com:Innovix-Matrix-Solutions/your-project.git #your project git url
```

3.**Navigate to the Project Directory:**

Move to the project directory using the following command:

Go to the project directory

```bash
cd your-project
```

4.**Copy .env.example to .env:**

Before proceeding, copy the .env.example file to .env to set up your environment variables:

```bash
cp .env.example .env
```

5.**Install Local Development Packages:**
To install local development packages, including Husky and other Laravel-specific packages, run the following commands:

```bash
npm install #for husky and other Laravel packages
npx husky install #only once
```

To install Composer packages needed for CS Fixer to run independently outside of the Docker shell, run:

```bash
composer install
```

6.**Start Docker and run:**
Launch Docker and run the application with the following command:

```bash
docker-compose up
```

Should be found running in port `8000`
and `phpmyadmin` running at port `8080`

7.**Running Migration and Other Commands:**
To execute Laravel-specific commands like migrations,queue,schedule,csfixer etc use the following command:

```bash
docker-compose exec app bash
```

8.**Generate IDE Helper Files:**
Generate general IDE helper files for improved code autocompletion and navigation by running:

```bash
php artisan ide-helper:generate
```

Generate IDE model helper files without writing to model files using:

```bash
#use any one of this two commands
php artisan ide-helper:models -N
php artisan ide-helper:models --nowrite
```


9.**Fix PHP Lint and Run CS Fixer:**
To fix PHP lint issues and run the Code Style Fixer, use the following command:

```bash
php artisan csfixer:run
```

10.**Run App health Check:**
After starting the app, it's essential to verify its health by performing the following steps:

Open your web browser or use a tool like `curl` to access the health check endpoint:

```bash
http://127.0.0.1:8000/api/healthz
```
Upon hitting the health check endpoint, the app should respond with a JSON object similar to the following:
```json
{
    "cache": true,
    "http": true,
    "storage": true,
    "database": true,
    "migration": true
}
```

If you receive a response like this, congratulations! Your app is healthy and functioning correctly.

Verifying the health of your application is an essential step to ensure that all components and services are running as expected. This check can help you identify and resolve issues promptly.

Remember to perform this health check regularly, especially after making significant changes to your application or its environment.

## Running Test
To execute tests for your application, utilize the following command:
```bash
./vendor/bin/pest
```

Running tests is crucial to ensure the reliability and correctness of your application's functionality. The above command will initiate the testing process and provide you with valuable insights into the quality of your codebase.

## Extra Artisan Commands
This project provides additional Artisan commands to simplify your workflow and enhance productivity.

### Run PHP CS Fixer
```bash
php artisan csfixer:run
```
This command ensures that your code adheres to the predefined coding standards, making your codebase clean and readable.
### Create a Service
Creating services for your application is made effortless. Use the following command to generate a service:
```bash
php artisan make:service subfolder/ServiceName
```
Replace subfolder and ServiceName with the actual values you need. You can also create a service without a subfolder:

```bash
php artisan make:service TestService
```
The newly created service will be located at `app/Http/Services/TestService.php`, ready to handle your application's business logic.

### Generate a Trait
Traits are reusable code components that enhance code organization. To create a new trait, simply run:

```bash
php artisan make:trait TestTrait
```

This command generates a new trait file for your project, promoting code reusability and maintainability.

Leverage these Artisan commands to streamline your development process and maintain a well-structured codebase.

## Authors

-   [@AHS12](https://www.github.com/AHS12)

## License

This project is brought to you by Innovix Matrix System and is released as open-source software under the [MIT license](https://opensource.org/licenses/MIT).

Feel free to use, modify, and distribute this starter project in accordance with the MIT license terms. We encourage collaboration and welcome contributions from the community to make this project even better.

