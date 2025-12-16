<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomCsvRequest extends FormRequest
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
            'contact_types' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'steps.*' => 'required',
            'prefectures.*' => 'required',

        ];
    }

    public function attributes()
    {
        return [
            'contact_types' => '案件種別',
            'start_date' => '開始日',
            'end_date' => '終了日',
            'steps.*' => 'ステップ',
            'prefectures.*' => '都道府県',
        ];
    }
}
