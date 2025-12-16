<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
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
            'fc.name' => 'required',
            'fc.email' => 'required | email',
            'fc.zipcode' => 'required ',
            'fc.pref' => 'required',
            'fc.city' => 'required',
            'fc.street' => 'required | min:3',
            'fc.tel' => 'required',
            'fc.fax' => 'nullable',
            'fc.staff' => 'required',
            'fc.qualified_business_number' => 'nullable | numeric | digits:13',
        ];
    }

    public function attributes()
    {
        return [
            'fc.name' => 'FC名',
            'fc.email' => 'メインメールアドレス',
            'fc.email2' => '連絡用メールアドレス',
            'fc.email3' => '連絡用メールアドレス',
            'fc.zipcode' => '郵便番号',
            'fc.pref' => '都道府県',
            'fc.city' => '市町村',
            'fc.street' => '詳細住所',
            'fc.tel' => '電話番号',
            'fc.fax' => 'FAX番号',
            'fc.staff' => 'スタッフ名',
            'fc.qualified_business_number' => '適格事業者番号',
        ];
    }
}
