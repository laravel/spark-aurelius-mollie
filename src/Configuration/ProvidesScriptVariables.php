<?php

namespace Laravel\Spark\Configuration;

use Laravel\Spark\Contracts\Repositories\CashierConfigRepository;
use Laravel\Spark\Spark;
use Laravel\Cashier\Cashier;
use Illuminate\Support\Facades\Auth;
use Laravel\Spark\Contracts\InitialFrontendState;

trait ProvidesScriptVariables
{
    /**
     * Get the default JavaScript variables for Spark.
     *
     * @return array
     */
    public static function scriptVariables()
    {
        /** @var CashierConfigRepository $cashierConfig */
        $cashierConfig = app()->make(CashierConfigRepository::class);

        return [
            'translations' => static::getTranslations() + ['teams.team' => trans('teams.team'), 'teams.member' => trans('teams.member')],
            'cardUpFront' => Spark::needsCardUpFront(),
            'collectsBillingAddress' => Spark::collectsBillingAddress(),
            'collectsEuropeanVat' => Spark::collectsEuropeanVat(),
            'createsAdditionalTeams' => Spark::createsAdditionalTeams(),
            'csrfToken' => csrf_token(),
            'currencySymbol' => Cashier::usesCurrencySymbol(),
            'defaultBillableCountry' => Spark::defaultBillableCountry(),
            'currency' => $cashierConfig->currency(),
            'currencyLocale' => $cashierConfig->currencyLocale(),
            'env' => config('app.env'),
            'roles' => Spark::roles(),
            'state' => Spark::call(InitialFrontendState::class.'@forUser', [Auth::user()]),
            'stripeKey' => $cashierConfig->authKey(),
            'cashierPath' => $cashierConfig->path(),
            'teamsPrefix' => Spark::teamsPrefix(),
            'teamsIdentifiedByPath' => Spark::teamsIdentifiedByPath(),
            'userId' => Auth::id(),
            'usesApi' => Spark::usesApi(),
            'usesMollie' => Spark::billsUsingMollie(),
            'usesTeams' => Spark::usesTeams(),
            'usesStripe' => Spark::billsUsingStripe(),
            'chargesUsersPerSeat' => Spark::chargesUsersPerSeat(),
            'seatName' => Spark::seatName(),
            'chargesTeamsPerSeat' => Spark::chargesTeamsPerSeat(),
            'teamSeatName' => Spark::teamSeatName(),
            'chargesUsersPerTeam' => Spark::chargesUsersPerTeam(),
            'chargesTeamsPerMember' => Spark::chargesTeamsPerMember(),
        ];
    }

    /**
     * Get the translation keys from file.
     *
     * @return array
     */
    private static function getTranslations()
    {
        $translationFile = resource_path('lang/'.app()->getLocale().'.json');

        if (! is_readable($translationFile)) {
            $translationFile = resource_path('lang/'.app('translator')->getFallback().'.json');
        }

        return json_decode(file_get_contents($translationFile), true);
    }
}
