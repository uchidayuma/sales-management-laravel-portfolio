<?php

namespace App\Models;

use Illuminate\Support\Arr;

class ProductQuotation extends MyModel
{
    protected $table = 'product_quotations';

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
