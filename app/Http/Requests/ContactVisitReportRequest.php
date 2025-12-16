<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactVisitReportRequest extends FormRequest
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
            'c.id' => 'required',
            'c.before_image1' => 'required | image',
            'c.before_image2' => 'image',
            'c.before_image3' => 'image',
        ];
    }

    public function attributes()
    {
        return [
            'c.id' => '報告対象案件',
            'c.before_image1' => '現場確認画像1枚目',
            'c.before_image2' => '現場確認画像2枚目',
            'c.before_image3' => '現場確認画像3枚目',
        ];
    }
}
