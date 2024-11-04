<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMatchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // Make sure to implement your authorization logic here
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'matchWinner' => 'nullable',
            'startTime' => 'nullable|date',
            'team1ResultText' => 'nullable',
            'team2ResultText' => 'nullable',
            'state' => Rule::when(fn($input) => !empty($input['matchWinner']), [
                'in:DONE,SCORE_DONE'
            ], ['nullable']),
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'matchWinner.required_if' => 'A match winner must be specified when the state is set to Done or Score Done',
            'state.required_if' => 'The state must be specified when a match winner is declared',
            'state.in' => 'When a match winner is specified, the state must be either Done or Score Done'
        ];
    }
}
