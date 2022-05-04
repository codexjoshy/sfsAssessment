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
            "firstname" => "sometimes|nullable|required|string",
            "lastname" => "sometimes|nullable|required|string",
            "email" => "sometimes|nullable|required|email|unique:users,email," . request()->user()->id,
            "phone" => "sometimes|nullable|required|numeric|min:11|unique:users,phone," . request()->user()->id,
            "password" => "sometimes|nullable|required|min:6",
            "gender" => "sometimes|nullable|required|in:male,female",
            "job" =>  "sometimes|nullable|required|string",

        ];
    }
    public function response(array $errors)
    {
        // Always return JSON.
        return response()->json($errors, 422);
    }
}