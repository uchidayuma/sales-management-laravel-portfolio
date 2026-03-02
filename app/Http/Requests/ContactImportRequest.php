<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactImportRequest extends FormRequest
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
            'csv_file' => [
                'required',
                'file',
                'max:10240', // 10MB
                'mimes:csv,txt',
            ],
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
            'csv_file.required' => 'CSVファイルは必須です',
            'csv_file.file' => 'CSVファイルを選択してください',
            'csv_file.max' => 'ファイルサイズは10MB以下である必要があります',
            'csv_file.mimes' => 'CSVファイル形式で提出してください',
        ];
    }
}
