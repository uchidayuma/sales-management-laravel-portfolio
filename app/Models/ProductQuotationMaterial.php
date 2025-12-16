<?php

namespace App\Models;

use Illuminate\Support\Arr;

class ProductQuotationMaterial extends MyModel
{
    protected $table = 'product_quotation_materials';
    // JSONカラムを配列に変換して扱うには $castsを設定する必要あり
    protected $casts = [
        'turf_cuts' => 'json',
    ];

    public function addProducts($afterArray, $id)
    {
        $addProducts = [];
        foreach ($afterArray as $after) {
            if (!array_key_exists('id', $after)) {
                $after['quotation_id'] = $id;
                array_push($addProducts, $after);
            }
        }

        return $addProducts;
    }

    public function removeProducts($beforeArray, $afterArray)
    {
        foreach ($beforeArray as $key => $before) {
            foreach ($afterArray as $after) {
                if (array_key_exists('id', $after)) {
                    if ($before['id'] == $after['id']) {
                        Arr::forget($beforeArray, $key);
                    }
                }
            }
        }

        return $beforeArray;
    }
}
