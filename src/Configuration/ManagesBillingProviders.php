<?php

namespace Laravel\Spark\Configuration;

use Exception;
use Laravel\Spark\Exceptions\NotSupportedByMollie;

trait ManagesBillingProviders
{
    /**
     * Indicates that the application should charge users per seat.
     *
     * @var bool
     */
    public static $chargesPerSeat = false;

    /**
     * The name used to describe a seat.
     *
     * @var string
     */
    public static $seatName;

    /**
     * Indicates that the application should charge teams per seat.
     *
     * @var bool
     */
    public static $chargesTeamsPerSeat = false;

    /**
     * The name used to describe a team seat.
     *
     * @var string
     */
    public static $teamSeatName;

    /**
     * Indicates that the application should charge users per team.
     *
     * @var bool
     */
    public static $chargesPerTeam = false;

    /**
     * Indicates that the application should charge teams per member.
     *
     * @var bool
     */
    public static $chargesTeamsPerMember = false;

    /**
     * The callback used to count the number of seats for a user subscription.
     *
     * @var \Closure
     */
    public static $seatsCountCallback;

    /**
     * The callback used to count the number of seats for a team subscription.
     *
     * @var \Closure
     */
    public static $teamSeatsCountCallback;

    /**
     * Indicates if the application requires a credit card up-front.
     *
     * @var bool
     */
    public static $cardUpFront = true;

    /**
     * Indicates if a plan change should be prorated.
     *
     * @var bool
     */
    public static $prorate = true;

    /**
     * Indicates the service the application uses for billing.
     *
     * @var bool
     */
    public static $billsUsing = 'stripe';

    /**
     * Indicates if the application collects the customer's billing address.
     *
     * @var bool
     */
    public static $collectsBillingAddress = false;

    /**
     * Indicates if the application collects European VAT.
     *
     * @var bool
     */
    public static $collectsEuropeanVat = false;

    /**
     * The application's home country where the business is incorporated.
     *
     * This value should be a two-digit country code.
     *
     * @var string
     */
    public static $homeCountry;

    /**
     * Indicate that the application should charge users per seat.
     *
     * @param  string  $name
     * @param  \Closure  $callback
     * @return void
     */
    public static function chargePerSeat($name, $callback)
    {
        static::$chargesPerSeat = true;

        static::$seatName = $name;
        static::$seatsCountCallback = $callback;
    }

    /**
     * Determine if the application charges users per seat.
     *
     * @return bool
     */
    public static function chargesUsersPerSeat()
    {
        return static::$chargesPerSeat;
    }

    /**
     * The name that describes a seat.
     *
     * @return string
     */
    public static function seatName()
    {
        return static::$seatName;
    }

    /**
     * Indicate that the application should charge teams per seat.
     *
     * @param  string  $name
     * @param  \Closure  $callback
     * @return void
     */
    public static function chargeTeamsPerSeat($name, $callback)
    {
        static::$chargesTeamsPerSeat = true;

        static::$teamSeatName = $name;
        static::$teamSeatsCountCallback = $callback;
    }

    /**
     * Determine if the application charges teams per seat.
     *
     * @return bool
     */
    public static function chargesTeamsPerSeat()
    {
        return static::$chargesTeamsPerSeat;
    }

    /**
     * The name that describes a team seat.
     *
     * @return string
     */
    public static function teamSeatName()
    {
        return static::$teamSeatName;
    }

    /**
     * Indicate that the application should charge users per team.
     *
     * @return void
     */
    public static function chargePerTeam()
    {
        static::$chargesPerTeam = true;

        static::$seatsCountCallback = function ($user) {
            return max(1, $user->ownedTeams()->count());
        };
    }

    /**
     * Determine if the application charges users per team.
     *
     * @return bool
     */
    public static function chargesUsersPerTeam()
    {
        return static::$chargesPerTeam;
    }

    /**
     * Indicate that the application should charge teams per member.
     *
     * @return void
     */
    public static function chargeTeamsPerMember()
    {
        static::$chargesTeamsPerMember = true;

        static::$teamSeatsCountCallback = function ($team) {
            return $team->users->count();
        };
    }

    /**
     * Determine if the application charges teams per member.
     *
     * @return bool
     */
    public static function chargesTeamsPerMember()
    {
        return static::$chargesTeamsPerMember;
    }


    /**
     * The number of seats the user occupies.
     *
     * @param  \Laravel\Spark\User  $user
     * @return mixed
     */
    public static function seatsCount($user)
    {
        return call_user_func(static::$seatsCountCallback, $user);
    }

    /**
     * The number of seats the team occupies.
     *
     * @param  \Laravel\Spark\Team  $team
     * @return mixed
     */
    public static function teamSeatsCount($team)
    {
        return call_user_func(static::$teamSeatsCountCallback, $team);
    }

    /**
     * Indicates that the application does not require a card up front.
     *
     * @return static
     */
    public static function noCardUpFront()
    {
        static::$cardUpFront = false;

        return new static;
    }

    /**
     * Determine if the application requires a card up front.
     *
     * @return bool
     */
    public static function needsCardUpFront()
    {
        return static::$cardUpFront;
    }

    /**
     * Determine if a plan change should be prorated.
     *
     * @return bool
     */
    public static function prorates()
    {
        return static::$prorate;
    }

    /**
     * Indicate that plan changes should not be prorated.
     *
     * @return static
     */
    public static function noProrate()
    {
        throw_if(
            static::billsUsingMollie(),
            NotSupportedByMollie::becauseOfMethod('Spark::noProrate')
        );

        static::$prorate = false;

        return new static;
    }

    /**
     * Determine if the application bills customers using a given provider.
     *
     * @param  string  $provider
     * @return bool
     */
    public static function billsUsing($provider)
    {
        return static::$billsUsing === $provider;
    }

    /**
     * Determine if the application bills customers using Stripe.
     *
     * @return bool
     */
    public static function billsUsingStripe()
    {
        return static::billsUsing('stripe');
    }

    /**
     * Indicate that the application should use Stripe for billing.
     *
     * @return static
     */
    public static function useStripe()
    {
        static::$billsUsing = 'stripe';

        return new static;
    }
    /**
     * Determine if the application bills customers using Mollie.
     *
     * @return bool
     */
    public static function billsUsingMollie()
    {
        return static::billsUsing('mollie');
    }

    /**
     * Indicate that the application should use Mollie for billing.
     *
     * @return static
     */
    public static function useMollie()
    {
        static::$billsUsing = 'mollie';
        static::noCardUpFront();

        $services = [
            'Http\Requests\Auth\RegisterRequest' => 'Http\Requests\Auth\MollieRegisterRequest',
            'Http\Requests\Settings\Subscription\CreateSubscriptionRequest' => 'Http\Requests\Settings\Subscription\CreateMollieSubscriptionRequest',
            'Http\Requests\Settings\Teams\Subscription\CreateSubscriptionRequest' => 'Http\Requests\Settings\Teams\Subscription\CreateMollieSubscriptionRequest',
            'Http\Requests\Settings\PaymentMethod\UpdatePaymentMethodRequest' => 'Http\Requests\Settings\PaymentMethod\UpdateMolliePaymentMethodRequest',
            'Http\Requests\Settings\PaymentMethod\UpdateBillingAddressRequest' => 'Http\Requests\Settings\PaymentMethod\UpdateMollieBillingAddressRequest',
            'Repositories\CouponRepository' => 'Repositories\MollieCouponRepository',
            'Repositories\CashierConfigRepository' => 'Repositories\CashierMollieConfigRepository',
            'Repositories\PerformanceIndicatorsRepository' => 'Repositories\MolliePerformanceIndicatorsRepository',
            'Interactions\Subscribe' => 'Interactions\SubscribeUsingMollie',
            'Interactions\SubscribeTeam' => 'Interactions\SubscribeTeamUsingMollie',
            'Interactions\Settings\PaymentMethod\UpdatePaymentMethod' => 'Interactions\Settings\PaymentMethod\UpdateMolliePaymentMethod',
            'Interactions\Settings\PaymentMethod\UpdateBillingAddress' => 'Interactions\Settings\PaymentMethod\UpdateMollieBillingAddress',
            'Interactions\Settings\PaymentMethod\RedeemCoupon' => 'Interactions\Settings\PaymentMethod\RedeemMollieCoupon',
        ];

        $app = app();

        foreach ($services as $key => $value) {
            $app->singleton('Laravel\Spark\Contracts\\'.$key, 'Laravel\Spark\\'.$value);
        }

        // Append a UserAgent string for Mollie support.
        mollie()->addVersionString('MollieLaravelSpark/' . static::$version);

        return new static;
    }

    /**
     * Indicate that the application should collect the customer's billing address.
     *
     * @param  bool  $value
     * @return static
     *
     * @throws \Exception
     */
    public static function collectBillingAddress($value = true)
    {
        // Mollie only supports European businesses, so collecting billing addresses will always be required for
        // the EU VAT calculation.
        if (!$value && static::billsUsingMollie()) {
            throw new Exception("Collecting billing addresses is required when using Mollie.");
        }

        static::$collectsBillingAddress = $value;

        return new static;
    }

    /**
     * Determine if the application collects the customer's billing address.
     *
     * @return bool
     */
    public static function collectsBillingAddress()
    {
        return static::$collectsBillingAddress;
    }

    /**
     * Indicate that the application should collect European VAT.
     *
     * @param  string|null  $homeCountry
     * @param  bool  $value
     * @return static
     */
    public static function collectEuropeanVat($homeCountry = null, $value = true)
    {
        static::$homeCountry = $homeCountry;
        static::$collectsEuropeanVat = $value;

        return static::collectBillingAddress($value);
    }

    /**
     * Determine if the application collects European VAT.
     *
     * @return bool
     */
    public static function collectsEuropeanVat()
    {
        return static::$collectsEuropeanVat;
    }

    /**
     * Get the home country the business is incorporated in.
     *
     * @return string|null
     */
    public static function homeCountry()
    {
        return static::$homeCountry;
    }
}
