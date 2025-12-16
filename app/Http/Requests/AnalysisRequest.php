<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AnalysisRequest extends FormRequest
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
            // 'start' => 'required',
            'end' => ' date | after_or_equal:start',
            // 'startyear' => 'required',
            // 'endyear' => 'sometimes | after_or_equal:startyear'
        ];
    }

    public function attributes()
    {
        return [
            'start' => '開始期間',
            'end' => '終了期間',
            'startyear' => '開始期間',
            'endyear' => '終了期間',
        ];
    }
}
