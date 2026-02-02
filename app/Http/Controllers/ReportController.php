<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactReportRequest;
use App\Models\Contact;
use Carbon\Carbon;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use InterventionImage;

class ReportController extends MyController
{
    public function __construct(Contact $contact)
    {
        parent::__construct();
        $this->model = $contact;
        $this->breadcrumbs->addCrumb('<i class="fas fa-envelop"></i>現場施工報告', '/report/list');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //現場報告一覧画面
        $this->breadcrumbs->addCrumb('一覧', 'list')->setLastItemWithHref(true);
        $breadcrumbs = $this->breadcrumbs;

        $user = $this->user;
        //$list = 現場報告一覧を完了したFCの一覧;
        $query = Contact::query()->where('step_id', self::STEP_REPORT_COMPLETE)->where('status', 1)
                ->orderBy('completed_at', 'DESC')->orderBy('id', 'DESC');
        if( isFc() ){
            $query->where('user_id', $user->id);
        }
        $list = $query->paginate(50);

        return view('share.progress.report-list', compact('breadcrumbs', 'list'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function pending()
    {
        $this->breadcrumbs->addCrumb('完了報告待ち案件一覧', 'list')->setLastItemWithHref(true);
        $breadcrumbs = $this->breadcrumbs;
        //現場報告一覧画面
        $user = $this->user;
        if (isAdmin()) {
            $list = Contact::where('step_id', self::STEP_COMPLETE)->where('status', 1)->paginate(20);
        } else {
            $list = Contact::where('step_id', self::STEP_COMPLETE)->where('user_id', $user->id)->where('status', 1)->paginate(20);
        }

        return view('share.progress.pending-report-list', compact('breadcrumbs', 'list'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        //施工後報告画面
        $user = $this->user;

        $contact = $this->model->findOrFail($id);
        $date = Carbon::now();

        $this->breadcrumbs->addCrumb('完了報告', 'create')->setLastItemWithHref(true);
        $breadcrumbs = $this->breadcrumbs;

        return view('fc.progress.create-report', compact('breadcrumbs', 'contact', 'date'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function update(ContactReportRequest $request, $id)
    {
        //施工後報告を保存
        $contact = $this->model->findOrFail($id);
        $inputs = $request->all();
        $completed_at = $inputs['c']['completed_at'] ?? Carbon::now();
        $update_array = ['step_id' => self::STEP_REPORT_COMPLETE, 'completed_at' => $completed_at];
        if(!empty($inputs['c']['public'])){
            $update_array['public'] = 1;
        }
        if(!is_null($inputs['c']['memo'])){
            $update_array['finish_memo'] = $inputs['c']['memo'];
        }
        //S3に画像を3枚まで登録
        for ($i = 1; $i < 4; ++$i) {
            if (!empty($inputs['c']['after_image'.$i])) {
                $file = $inputs['c']['after_image'.$i];
                $name = $file->getClientOriginalName();
                $tmpPath = storage_path('app/images/after/').$name;
                InterventionImage::read($file)
                       ->scale(width: 1000)
                       ->save($tmpPath, 60);
                $path = Storage::disk("s3")->putFileAs('/images/after/'.$id, new File($tmpPath), $name, 'public');
                $pathExplodes = explode('/', $path);
                $filename = last($pathExplodes);
                $update_array['after_image'.$i] = $filename;

                // 一時ファイルを削除
                Storage::disk('local')->delete('images/after/'.$name);
            }
        }

        $this->model->where('id', $contact['id'])->update($update_array);

        return redirect()->route('dashboard')->with('success', '施工後報告が完了しました！');
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

    public function adminFinish(Request $request)
    {
        $contactId = $request->input('contact_id');
        $this->model->where('id', $contactId)->update(['step_id' => self::STEP_REPORT_COMPLETE]);

        return redirect('/')->with('success', "納品登録を完了しました。問い合わせNo${contactId}の案件は終了です");
    }
}
