<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class CheapestRouteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'begin' => 'required|integer|min:0',
            'end' => 'required|integer|min:0',
            'currencies' => 'required|array|min:1',
            'currencies.*' => 'in:USDT,ETH,BTC',
        ];
    }
}
