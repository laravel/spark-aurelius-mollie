<?php

namespace Laravel\Spark\Http\Requests\Settings\PaymentMethod;

use Illuminate\Foundation\Http\FormRequest;
use Laravel\Spark\Contracts\Http\Requests\Settings\PaymentMethod\UpdatePaymentMethodRequest;

class UpdateMolliePaymentMethodRequest extends FormRequest implements UpdatePaymentMethodRequest
{
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
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [];
    }
}
