<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Config;
use App\Models\Product;

class ConfigController extends MyController
{
    public function __construct(Config $config)
    {
        parent::__construct();
        $this->model = $config;
    }

    public function updateLeaveDay(Request $request)
    {
        $post = $request->all();
        $this->model->where('key', 'leave_alone_days')->update(['value' => $post['day']]);
        return redirect('/')->with('success', '放置日数設定を更新しました！');
    }

    public function updateFreeItem(Request $request)
    {
        // dd($request->all());
        $free_shipping_item_id = $request->input('free-items');
        $json = json_encode($free_shipping_item_id);
        // dd($json);
        $this->model->where('key', 'free_shipping_product_id')->update(['value' => $json]);
        if(is_null($free_shipping_item_id[0])){
            return redirect('/')->with('success', "送料無料商品をなしに設定しました！");
        }elseif(count($free_shipping_item_id) == 1){
            $name = Product::find($free_shipping_item_id[0]);
            return redirect('/')->with('success', $name['name'] . "を送料無料商品に設定しました！");
        }else{
            $count = count($free_shipping_item_id);
            $name = Product::find($free_shipping_item_id[0]);
            return redirect('/')->with('success', $name['name'] . "と合わせて" . $count . "つの商品を送料無料商品に設定しました！");
        }

    }
}
