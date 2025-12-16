<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactUpdateRequest extends FormRequest
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
            'c.street' => 'required | max:100',
            'c.document1' => 'file | max:5140',
            'c.document2' => 'file | max:5140',
            'c.document3' => 'file | max:5140',
            'c.document4' => 'file | max:5140',
            'c.document5' => 'file | max:5140',
        ];
    }

    public function attributes()
    {
        return [
            'c.street' => '市町村以降の住所',
            'c.document1' => '書類その1',
            'c.document2' => '書類その2',
            'c.document3' => '書類その3',
            'c.document4' => '書類その4',
            'c.document5' => '書類その5',
        ];
    }
}
