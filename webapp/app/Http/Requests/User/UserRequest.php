<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'account_name' => ['nullable', 'alpha_num:ascii'],
            'name' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email'],
            'password' => ['required', 'ascii', 'max:20'],
        ];
    }
}
