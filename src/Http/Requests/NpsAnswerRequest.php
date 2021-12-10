<?php

namespace Maize\Nps\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Maize\Nps\Models\Nps;

class NpsAnswerRequest extends FormRequest
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
        $nps = Nps::findOrFail(
            $this->route('id')
        );

        return [
            'value' => [
                'nullable',
                'integer',
                Rule::in(array_values($nps->values)),
            ],
            'answer' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) {
                    if (! is_null($this->value)) {
                        return;
                    }

                    if (! is_null($value)) {
                        $fail('The '.$attribute.' must be empty.');
                    }
                },
            ],
        ];
    }
}
