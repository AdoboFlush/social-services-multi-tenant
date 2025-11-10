<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Services\SEABankDeposit\SEABankDepositFacade;
use App\Repositories\User\UserInterface;

class SEABankDepositRequest extends FormRequest
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
        return [
            "country" => "bail|required|string|exists:countries,name",
            "currency" => "bail|required|string|exists:currency,name",
            "amount" => [
                "bail",
                "required",
                function ($attribute, $value, $fail) {
                    $currency = request()->currency;
                    if (!is_null($currency)) {
                        $value = str_replace(",", "", $value);
                        [$min_amount, $max_amount] = SEABankDepositFacade::getCurrencyMinMaxAmount($currency, UserInterface::ACCOUNT_VERIFIED);
                        $number_min_amount = number_format($min_amount);
                        $number_max_amount = number_format($max_amount);
                        if ($value < $min_amount || $value > $max_amount || (floor($value) !== (float) $value)) {
                            $fail(_lang("Invalid amount. Please enter a whole number amount from {$currency} {$number_min_amount} to {$currency} {$number_max_amount}"));
                        }
                    }
                }
            ]
        ];
    }
}
