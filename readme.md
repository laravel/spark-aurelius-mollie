# Laravel Spark, Mollie edition

[spark.laravel.com](https://spark.laravel.com) | [mollie.com](https://www.mollie.com)

## Support

This is an alpha version.

It is suitable for starting development of your next SAAS endeavour, as the API is not expected to change very much
before stable release.

This alpha version however is not yet ready for production. So please use your mollie test key for now.

If you'd like to have a chat, [join us on the dedicated Discord channel](https://discord.gg/z2TaZV).

Bugs and feature requests will be tracked [here](https://github.com/mollie/spark-mollie/issues) in the repository.
Feel free to open a ticket.

## Installation

Create a new Laravel project using the [Laravel installer](https://laravel.com/docs/installation):

    laravel new my-project

Next, add the following repository to your `composer.json` file:

    "repositories":[
        {
            "type": "vcs",
            "url": "git@github.com:mollie/spark-mollie.git"
        }
    ]

You should also add the following dependencies to your `composer.json` file's `require` section:

    "laravel/cashier-mollie": "^1.0",
    "laravel/spark-aurelius": "^0.1.0",
    "mpociot/vat-calculator": "^2.4",

Next, run the composer update command. You may be prompted for a GitHub token in order to install the private Spark
repository. Composer will provide a link where you can create this token.

Once the dependencies are installed, add the following service providers to your `app.php` configuration file:

    Laravel\Spark\Providers\SparkServiceProvider::class,
    Laravel\Cashier\CashierServiceProvider::class,

Next, run the install command:

    php artisan spark:install --force --mollie

or for team billing:

    php artisan spark:install --force --mollie --team-billing

Set the `MOLLIE_KEY` and database settings in the `.env` file. 

Once Spark is installed, add the following provider to your `app.php` configuration file:

    App\Providers\SparkServiceProvider::class,

Finally, you are ready to run the `npm install`, `npm run dev`, and `php artisan migrate` commands.
Once these commands have completed, you are ready to enjoy Spark!

### Linking The Storage Directory

Once Spark is installed, you should link the `public/storage` directory to your `storage/app/public` directory.
Otherwise, user profile photos stored on the local disk will not be available:

    php artisan storage:link

## Configuring billing plans

1. Set up your subscription plans as described in the
[laravel/cashier-mollie documentation](https://github.com/laravel/cashier-mollie).
2. Then configure the SparkServiceProvider as described in the
[Spark documentation](https://spark.laravel.com/docs/9.0/billing).

## Local testing

You can use `valet share` (a ngrok wrapper) to make your local setup reachable for Mollie's webhook calls.

Make sure to use the ngrok generated url both in your `.env` file (`APP_URL`) and in your browser.
