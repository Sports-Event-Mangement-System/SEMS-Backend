<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTeamRequest extends FormRequest
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
            'tournament_id' => 'required',
            'team_name' => 'required',
            'team_logo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'coach_name' => 'required',
            'phone_number' => 'required',
            'email' => 'required|email',
            'address' => 'required',
            'player_number' => 'required|integer',
            'status' => 'nullable|boolean',
            // 'players' => 'required|array|min:'. $this->tournament->min_players_per_team, // Check against tournament minimum
            'players.*.player_name' => 'required|string',
            'players.*.player_email' => 'required|email',
            // 'players.*.is_captain' => 'nullable|boolean',
        ];
    }
    public function messages(): array
    {
        $messages = [];

        foreach ($this->input('players', []) as $index => $player) {
            $playerNumber = $index + 1; // Adjust for 1-based indexing

            $messages["players.$index.player_name.required"] = "Player $playerNumber name is required.";
            $messages["players.$index.player_email.required"] = "Player $playerNumber email is required.";
            $messages["players.$index.player_email.email"] = "Player $playerNumber email must be a valid email address.";
        }

        return $messages;
    }

}
