<?php

namespace Laravel\Spark\Console\Installation;

class InstallModels
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

        $this->command->line('Installing Eloquent Models: <info>✔</info>');
    }

    /**
     * Install the components.
     *
     * @return void
     */
    public function install()
    {
        copy($this->getUserModel(), app_path('Models/User.php'));
        copy($this->getTeamModel(), app_path('Models/Team.php'));
    }

    /**
     * Get the path to the proper User model stub.
     *
     * @return string
     */
    protected function getUserModel()
    {
        if($this->command->option('team-billing')) {
            return $this->getTeamUserModel();
        }

        return $this->command->option('mollie')
            ? SPARK_STUB_PATH.'/app/Models/MollieUser.php'
            : SPARK_STUB_PATH.'/app/Models/User.php';
    }

    /**
     * @param string $prefix
     * @return string
     */
    protected function getTeamUserModel()
    {
        return $this->command->option('mollie')
            ? SPARK_STUB_PATH.'/app/Models/MollieTeamUser.php'
            : SPARK_STUB_PATH.'/app/Models/TeamUser.php';
    }

    /**
     * @param string $prefix
     * @return string
     */
    protected function getTeamModel()
    {
        return $this->command->option('mollie')
            ? SPARK_STUB_PATH.'/app/Models/MollieTeam.php'
            : SPARK_STUB_PATH.'/app/Models/Team.php';
    }
}
