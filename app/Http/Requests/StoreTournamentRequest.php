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
            'prize_pool' => 'numeric',
            't_images' => 'nullable|array',
            't_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048|dimensions:max_width=1920,max_height=756',
            'ts_date' => 'required|date',
            'te_date' => 'required|date',
            'rs_date' => 'required|date',
            're_date' => 'required|date',
            'phone_number' => 'required',
            'email' => 'nullable|email',
            'address' => 'required',
            'status' => 'required|boolean',
            'tournament_type' => 'required',
            'max_teams' => 'required|integer|gt:min_teams',
            'min_teams' => 'required|integer|lt:max_teams',
            'max_players_per_team' => 'required|integer|gt:min_players_per_team',
            'min_players_per_team' => 'required|integer|lt:max_players_per_team',
            'featured' => 'required|boolean',
        ];
    }

    /**
     * Get the validation error messages that apply to the request.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            't_name.required' => 'The tournament name is required.',
            't_description.required' => 'The tournament description is required.',
            'prize_pool.integer' => 'The prize pool must be an integer.',
            't_images.image' => 'The tournament Images must be an image.',
            't_images.mimes' => 'The tournament Images must be a file of type: jpeg, png, jpg, gif.',
            't_images.max' => 'The tournament logo may not be greater than 2048 kilobytes.',
            't_images.dimensions' => 'The tournament logo must not exceed 1920x756 pixels.',
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
            'status.boolean' => 'The status must be a boolean value.',
            'tournament_type.required' => 'The Tournmaent Type Field is required',
            'max_teams.required' => 'The Max team field is required.',
            'max_teams.integer' => 'The Max team must be an integer.',
            'max_teams.gt' => 'The Max team must be greater than the Minimum team.',
            'min_teams.required' => 'The Minimum team field is required.',
            'min_teams.integer' => 'The Minimum team must be an integer.',
            'min_teams.lt' => 'The Minimum team must be less than the Max team.',
            'max_players_per_team.required' => 'The Max Player Per Team field is required.',
            'max_players_per_team.integer' => 'The Max Player Per Team must be an integer.',
            'max_players_per_team.gt' => 'The Max Player Per Team must be greater than the Minimum Per player field.',
            'min_players_per_team.required' => 'The Minimum Player Per Team field is required.',
            'min_players_per_team.integer' => 'The Minimum Player Per Team must be an integer.',
            'min_players_per_team.lt' => 'The Minimum Player Per Team must be less than the Max Player.',
            'featured.required' => 'The featured status is required.',
            'featured.boolean' => 'The featured status must be a boolean value.',
        ];
    }
}
