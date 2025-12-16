<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateQuotationRequest extends FormRequest
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
            'q.name' => 'required | min:3',
            'pq.*.product_id' => 'not_in: 0',
            'pq.*.name' => 'min:2',
            'pq.*.unit' => 'required',
            'pq.*.unit_price' => 'required',
            'pq.*.num' => 'required | numeric',
        ];
    }

    public function attributes()
    {
        return [
            'pq.*.name' => '商品・サービス名',
            'pq.*.product_id' => '商品選択',
            'pq.*.unit' => '商品・サービスの単位',
            'pq.*.unit_price' => '商品・サービスの単価',
            'pq.*.num' => '数量',
        ];
    }
}
