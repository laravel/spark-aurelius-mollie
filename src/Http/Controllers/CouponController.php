<?php

namespace Laravel\Spark\Http\Controllers;

use Laravel\Spark\Spark;
use Illuminate\Http\Request;
use Laravel\Spark\Contracts\Repositories\CouponRepository;

class CouponController extends Controller
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

        $this->middleware('auth')->only('current');
    }

    /**
     * Get the specified coupon.
     *
     * This is used during registration to show the discount.
     *
     * @param  string  $code
     * @return \Illuminate\Http\Response
     */
    public function show($code)
    {
        $coupon = $this->coupons->find($code);

        return $coupon ? response()->json($coupon->toArray()) : abort(404);
    }

    /**
     * Get the current discount for the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $userId
     * @return \Illuminate\Http\Response
     */
    public function current(Request $request, $userId)
    {
        $user = Spark::user()->where('id', $userId)->firstOrFail();

        if ($coupon = $this->coupons->forBillable($user)) {
            return response()->json($coupon->toArray());
        }

        abort(204);
    }
}
