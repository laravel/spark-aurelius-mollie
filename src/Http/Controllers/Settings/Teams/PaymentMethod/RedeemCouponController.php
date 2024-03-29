<?php

namespace Laravel\Spark\Http\Controllers\Settings\Teams\PaymentMethod;

use Laravel\Spark\Team;
use Laravel\Spark\Spark;
use Illuminate\Http\Request;
use Laravel\Spark\Http\Controllers\Controller;
use Laravel\Spark\Contracts\Repositories\CouponRepository;
use Laravel\Spark\Contracts\Interactions\Settings\PaymentMethod\RedeemCoupon;

class RedeemCouponController extends Controller
{
    /**
     * The coupon repository implementation.
     *
     * @var \Laravel\Spark\Contracts\Repositories\CouponRepository
     */
    protected $coupons;

    /**
     * Create a new controller instance.
     *
     * @param  \Laravel\Spark\Contracts\Repositories\CouponRepository  $coupons
     * @return void
     */
    public function __construct(CouponRepository $coupons)
    {
        $this->coupons = $coupons;

        $this->middleware('auth');
    }

    /**
     * Redeem the given coupon code.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Laravel\Spark\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function redeem(Request $request, Team $team)
    {
        abort_unless($request->user()->ownsTeam($team), 403);

        $this->validate($request, [
            'coupon' => 'required',
        ]);

        // We will verify that the coupon can actually be redeemed. In some cases even
        // valid coupons can not get redeemed by an existing user if this coupon is
        // running as a promotion for brand new registrations to the application.
        if (! $this->coupons->canBeRedeemed($request->coupon, $team)) {
            return response()->json([
                'errors' => [
                    'coupon' => [
                        __('This coupon code is invalid.')
                    ]
                ]
            ], 422);
        }

        Spark::interact(RedeemCoupon::class, [
            $team, $request->coupon
        ]);
    }
}
