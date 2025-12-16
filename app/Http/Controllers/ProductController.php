<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends MyController
{
    public function __construct(Product $product)
    {
        parent::__construct();
        $this->model = $product;
        $this->breadcrumbs->addCrumb('<i class="fas fa-box"></i>在庫管理', 'products');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->breadcrumbs->addCrumb('在庫一覧', '/products')->setLastItemWithHref(true);

        $products = $this->model
            //販促アイテムとカット賃は、在庫一覧表から省く
            ->whereIn('product_type_id', [1, 2])
            //砂、ゴムチップを在庫一覧から省く
            ->whereNotIn('id', [25, 26])
            ->where('status', 1)
            ->orderBy('order_no', 'ASC')
            ->get();

        $breadcrumbs = $this->breadcrumbs;

        return view('share.product.index', compact('products', 'breadcrumbs'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id = null)
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->breadcrumbs->addCrumb('製品詳細');
        $breadcrumbs = $this->breadcrumbs;

        $product = $this->model->findById($id);

        return view('share.product.show', compact('product', 'breadcrumbs'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->breadcrumbs->addCrumb('製品編集');
        $breadcrumbs = $this->breadcrumbs;

        $product = $this->model->findById($id);

        return view('admin.product.edit', compact('product', 'breadcrumbs'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $post = $request->all();
        $this->model->where('id', $id)->update($post['p']);

        return redirect(route('products.index'))->with('success', '製品の修正が完了しました！');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
    }

    public function ajaxUpdate(Request $request)
    {
        $post = $request->all();

        $this->model->where('id', $post['id'])->update(['stock' => $post['stock']]);

        return response()->json(['stock' => $post['stock']]);
    }

    public function ajaxGet()
    {
        $products = $this->model->where('status', 1)->orderBy('id', 'ASC')->get();

        return response()->json($products);
    }
}
