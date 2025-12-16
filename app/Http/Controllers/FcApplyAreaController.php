<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FcApplyArea;

class FcApplyAreaController extends MyController
{
    public function __construct(FcApplyArea $fc_apply_area)
    {
        parent::__construct();
        $this->model = $fc_apply_area;
        $this->breadcrumbs->addCrumb('<i class="fas fa-map-location"></i>FC担当エリア', '');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->breadcrumbs->addCrumb('FC担当エリア一覧');
        $breadcrumbs = $this->breadcrumbs;
        $fc_apply_areas = $this->model->select('fc_apply_areas.*','u.id AS is_apply')->where('fc_apply_areas.status', 1)->leftJoin('users as u', 'fc_apply_areas.id', '=', 'u.fc_apply_area_id')->get();

        return view('admin.fcapplyareas.index', compact('breadcrumbs', 'fc_apply_areas'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
          'area.name' => 'required',
          'area.content' => 'required',
        ]);
        $posts = $request->all();
        $this->model->insert($posts['area']);

        return redirect(route('settings.fcapplyareas.index'))->with(['success' => $posts['area']['name'] .'を登録しました。']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $posts = $request->all();
        $this->model->where('id', $id)->update($posts['area']);

        return redirect(route('settings.fcapplyareas.index'))->with(['success' => 'エリアを更新しました。']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->model->where('id', $id)->update(['status' => 2]);

        return redirect(route('settings.fcapplyareas.index'))->with(['warning' => 'エリアを削除しました。']);
    }
}
