# Laravel Spark, Mollie edition

[spark.laravel.com](https://spark.laravel.com) | [mollie.com](https://www.mollie.com)

## Installation

Create a new Laravel project using the [Laravel installer](https://laravel.com/docs/installation):

    laravel new testspark

Next, add the following repository to your `composer.json` file:

    "repositories":[
        {
            "type": "vcs",
            "url": "git@github.com:mollie/spark-mollie.git"
        }
    ]

You should also add the following dependency to your `composer.json` file's `require` section:

    "laravel/spark-aurelius": "dev-mollie",

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
