<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactReportRequest extends FormRequest
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
            'c.after_image1' => 'required | image',
            'c.after_image2' => 'image',
            'c.after_image3' => 'image',
            'c.completed_at' => 'required',
        ];
    }

    public function attributes()
    {
        return [
            'c.after_image1' => '施工完了画像1枚目',
            'c.completed_at' => '工事完了日',
        ];
    }
}
