<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTournamentRequest extends FormRequest
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
            'prize_pool' => 'integer',
            't_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Added image validation
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
}
