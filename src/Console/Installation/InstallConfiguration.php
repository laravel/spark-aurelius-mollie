<?php

namespace Laravel\Spark\Console\Installation;

class InstallConfiguration
{
    /**
     * The console command instance.
     *
     * @var \Illuminate\Console\Command  $command
     */
    protected $command;

    /**
     * Create a new installer instance.
     *
     * @param  \Illuminate\Console\Command  $command
     * @return void
     */
    public function __construct($command)
    {
        $this->command = $command;

        $this->command->line('Updating Configuration Values: <info>✔</info>');
    }

    /**
     * Install the components.
     *
     * @return void
     */
    public function install()
    {
        $files = $this->command->option('mollie')
            ? $this->getMollieConfigFiles()
            : $this->getStripeConfigFiles();

        collect($files)->each(function ($to, $from) {
            copy($from, $to);
        });
    }

    /**
     * Get the Stripe config file paths.
     *
     * @return array
     */
    protected function getStripeConfigFiles()
    {
        return [
            SPARK_STUB_PATH.'/config/auth.php' => config_path('auth.php'),
        ];
    }

    /**
     * Get the Mollie config file paths.
     *
     * @return array
     */
    protected function getMollieConfigFiles()
    {
        return [
            SPARK_STUB_PATH.'/config/auth.php' => config_path('auth.php'),
            SPARK_STUB_PATH.'/config/mollie/cashier.php' => config_path('cashier.php'),
            SPARK_STUB_PATH.'/config/mollie/cashier_coupons.php' => config_path('cashier_coupons.php'),
            SPARK_STUB_PATH.'/config/mollie/cashier_plans.php' => config_path('cashier_plans.php'),
        ];
    }
}
