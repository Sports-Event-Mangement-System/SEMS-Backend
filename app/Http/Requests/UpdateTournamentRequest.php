<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTournamentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            't_name' => 'required',
            't_description' => 'required',
            'prize_pool' => 'numeric',
            't_images' => 'nullable|array',
            't_images*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'ts_date' => 'required|date',
            'te_date' => 'required|date',
            'rs_date' => 'required|date',
            're_date' => 'required|date',
            'phone_number' => 'required',
            'email' => 'nullable|email',
            'address' => 'required',
            'status' => 'required|boolean',
            'team_number' => 'required|integer',
            'featured' => 'required|boolean',
        ];
    }
    public function messages(): array
    {
        return [
            't_name.required' => 'The tournament name is required.',
            't_description.required' => 'The tournament description is required.',
            'prize_pool.integer' => 'The prize pool must be an integer.',
            't_images.image' => 'The tournament logo must be an image.',
            't_images.mimes' => 'The tournament logo must be a file of type: jpeg, png, jpg, gif.',
            't_images.max' => 'The tournament logo may not be greater than 2048 kilobytes.',
            'ts_date.required' => 'The tournament start date is required.',
            'ts_date.date' => 'The tournament start date must be a valid date.',
            'te_date.required' => 'The tournament end date is required.',
            'te_date.date' => 'The tournament end date must be a valid date.',
            'rs_date.required' => 'The registration start date is required.',
            'rs_date.date' => 'The registration start date must be a valid date.',
            're_date.required' => 'The registration end date is required.',
            're_date.date' => 'The registration end date must be a valid date.',
            'phone_number.required' => 'The phone number is required.',
            'email.email' => 'The email must be a valid email address.',
            'address.required' => 'The address is required.',
            'status.required' => 'The status is required.',
            'team_number.required' => 'The team number is required.',
            'featured.required' => 'The featured is required.',
        ];
    }

}
