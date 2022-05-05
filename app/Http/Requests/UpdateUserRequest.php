<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
            "firstname" => "sometimes|nullable|string",
            "lastname" => "sometimes|nullable|string",
            "email" => "sometimes|nullable|email|unique:users,email," . request()->user()->id,
            "phone" => "sometimes|nullable|numeric|min:11|unique:users,phone," . request()->user()->id,
            "password" => "sometimes|nullable|min:6",
            "gender" => "sometimes|nullable|in:male,female",
            "job" =>  "sometimes|nullable|string",

        ];
    }
    public function response(array $errors)
    {
        // Always return JSON.
        return response()->json($errors, 422);
    }
}