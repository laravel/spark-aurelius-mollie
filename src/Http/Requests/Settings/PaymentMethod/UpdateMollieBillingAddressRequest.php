<?php

namespace Laravel\Spark\Http\Requests\Settings\PaymentMethod;

use Laravel\Spark\Spark;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Laravel\Spark\Http\Requests\ValidatesBillingAddresses;
use Laravel\Spark\Contracts\Http\Requests\Settings\PaymentMethod\UpdateBillingAddressRequest;

class UpdateMollieBillingAddressRequest extends FormRequest implements UpdateBillingAddressRequest
{
    use ValidatesBillingAddresses;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validator for the request.
     *
     * @return \Illuminate\Validation\Validator
     */
    public function validator()
    {
        $validator = Validator::make($this->all(), []);

        if (Spark::collectsBillingAddress()) {
            $this->validateBillingAddress($validator);
        }

        return $validator;
    }
}
