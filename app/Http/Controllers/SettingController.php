<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\OfficeHoliday;
use App\Models\CsvExportOptions;

class SettingController extends MyController
{
    //
    public function officeHoliday()
    {
        $holidays = OfficeHoliday::whereYear('holiday', '>=', Carbon::now()->format('Y'))->orderBy('holiday', 'ASC')->get();
        return view('admin.officeholiday.create', compact('holidays'));
    }

    public function ajaxCreateOfficeHoliday(Request $request)
    {
        $posts = $request->all();
        if( empty($posts['holiday'])){
            return response()->json(['error' => 'IDがありません']);
        }
        if( !OfficeHoliday::where('holiday', $posts['holiday'])->first() ){
            OfficeHoliday::insert(['holiday' => $posts['holiday']]);
            return response()->json(['holiday' => $posts['holiday']]);
        }
    }

    public function ajaxDestoryOfficeHoliday(Request $request)
    {
        $posts = $request->all();
        if( empty($posts['id'])){
            return response()->json(['error' => 'IDがありません']);
        }
        OfficeHoliday::where('id', $posts['id'])->delete();
    }

    public function csvExportOption()
    {
        $csv_options = CsvExportOptions::get();
        
        return view('admin.csv.csv-export-option', compact('csv_options'));
    }

    public function ajaxCsvExportOptionForm(Request $request)
    {
        # ここにAdd 処理
        $add_form_name = $request->all();

        //$form_name = $add_form_name['add_same_id'];

        CsvExportOptions::insert(['form_name' => $add_form_name['csv_form_data']]);

        return response()->json($add_form_name);
    }

    public function ajaxDestoryCsvExportOptionForm(Request $request)
    {
        $delete_form_id = $request->all();

        CsvExportOptions::where('id', $delete_form_id )->delete();

        return response()->json($delete_form_id);
    }
}
