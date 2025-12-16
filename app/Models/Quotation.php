<?php

namespace App\Models;
use Illuminate\Support\Facades\DB;

class Quotation extends MyModel
{
    protected $table = 'quotations';
    protected $casts = [
      'turf_cuts' => 'json',
    ];

    public function getNomalQuotation($id)
    {
        return $this->select('quotations.*', 'quotations.id AS quotation_id', 'quotations.name AS quotation_name', 'quotations.created_at AS created_quotation', 'c.*', 'u.*',
        'u.tel as fc_tel', 'u.zipcode as fc_zipcode', 'u.pref as fc_pref', 'u.city as fc_city', 'u.street as fc_street',
        'p.name AS product_name', 'p.price AS product_unit_price', 'p.cut_price AS product_cut_unit_price', 'pq.*', 'pq.unit_price AS outer_unit_price', 'quotations.memo AS quotation_memo')
            ->where('quotations.status', 1)
            ->where('quotations.id', $id)
            ->where('pq.status', 1)
            ->leftJoin('contacts AS c', 'c.id', '=', 'quotations.contact_id')
            ->leftJoin('users AS u', 'c.user_id', '=', 'u.id')
            ->leftJoin('product_quotations AS pq', 'pq.quotation_id', '=', 'quotations.id')
            ->leftJoin('products AS p', 'pq.product_id', '=', 'p.id')
            ->get();
    }

    public function getMaterialQuotation($id)
    {
        return $this->select('quotations.*', 'quotations.id AS quotation_id', 'quotations.name AS quotation_name', 'quotations.created_at AS created_quotation','c.*',
        'u.*', 'u.tel as fc_tel', 'u.zipcode as fc_zipcode', 'u.pref as fc_pref', 'u.city as fc_city', 'u.street as fc_street',
        'p.*', 'pqm.*', 'p.name AS product_name', 'p.price AS product_unit_price', 'p.cut_price AS product_cut_unit_price', 'quotations.memo AS quotation_memo')
            ->where('quotations.status', 1)
            ->where('quotations.id', $id)
            ->where('pqm.status', 1)
            ->leftJoin('contacts AS c', 'c.id', '=', 'quotations.contact_id')
            ->leftJoin('users AS u', 'c.user_id', '=', 'u.id')
            ->leftJoin('product_quotation_materials AS pqm', 'pqm.quotation_id', '=', 'quotations.id')
            ->leftJoin('products AS p', 'pqm.product_id', '=', 'p.id')
            ->get();
    }

    public function pdfToS3($id)
    {
        $pdf = app('dompdf.wrapper');
        $products = Product::orderBy('id', 'ASC')->get();

        $quotation_type = $this->getField(['id' => $id], 'type');

        // カット陳の行は別にカウント
        $cut_row_count = 0;
        if ($quotation_type == 0) {
            $quotations = $this->getNomalQuotation($id);
            $cut_total = null;
            $cut_total_length = null;
            $pdf_product_count = ProductQuotation::where('quotation_id', $id)->where('status', 1)->count();
        } elseif ($quotation_type == 1) {
            $quotations = $this->getMaterialQuotation($id);
            $pdf_product_count = ProductQuotationMaterial::where('quotation_id', $id)->where('status', 1)->count();
            // PDFは個別のカットメニューを出せずに「カット賃」の合計を表示すればいいので、計算
            $total = 0;
            $total_length = 0.00;
            if(!$quotations[0])  \App::abort(404);
            foreach ($quotations as $q) {
                if ($q['cut'] == 1) {
                    $cut_row_count = 1;
                    foreach ($q['turf_cuts'] as $cut) {
                        if ($cut['unit'] == 'm') {
                            $cut_set_num = !empty($q['cut_set_num']) ? $q['cut_set_num'] : 1;
                            $total = $total + floatval($cut['num']) * intval($cut['unit_price'] * $cut_set_num);
                            $total_length = $total_length + floatval($cut['num'] * $cut_set_num);
                        }
                    }
                }
            }
            $cut_total = $total;
            $cut_total_length = $total_length;
        }
        foreach($quotations as $q){
            if(mb_strlen($q['memo']) > 15){
                $pdf_product_count++;
            }
        }
        if(!$quotations[0])  \App::abort(404);
        $pdf_memo_count = substr_count($quotations[0]['memo'],"\n");
        $pdf_payee_count = substr_count($quotations[0]['payee'],"\n");
        
        $pdf_product_count = $pdf_product_count + $cut_row_count;
        $pdf_quotation_row_and_memo_count = $pdf_memo_count + $pdf_product_count;

        $pdf->loadView('share.quotation.pdf', ['quotations' => $quotations, 'products' => $products, 'cut_total' => $cut_total, 'cut_total_length' => $cut_total_length ,'pdf_quotation_row_and_memo_count' => $pdf_quotation_row_and_memo_count, 'pdf_memo_count' => $pdf_memo_count, 'pdf_payee_count' => $pdf_payee_count, 'pdf_product_count' => $pdf_product_count, 'cut_row_count' => $cut_row_count]);
        sleep(9);
        $fileName = '見積書No.'.$id.'.pdf';
        $contactId = $quotations[0]['contact_id'];
        $pdf->save(public_path().'/tmp-quotations/'.$fileName);
        $path = \Storage::disk('s3')->putFileAs("/quotations/$contactId/", public_path().'/tmp-quotations/'.$fileName, $fileName, 'public');
        // \File::delete(public_path().'/tmp-quotations/'.$fileName);
    }

    static function getMostProductName($id = 1, $type = 0)
    {
        $quotation = self::query()->where('quotations.id', $id)->where('quotations.status', 1); 
        if($type == 0){
            $quotation->select('p.name', DB::raw('SUM(pq.num) AS product_area'))
                ->where('type', 0)->where('pq.status', 1)->where('p.product_type_id', 1)
                ->join('product_quotations AS pq', 'pq.quotation_id', '=', 'quotations.id')
                ->join('products AS p', 'p.id', '=', 'pq.product_id')
                ->groupBy('pq.id');
        }elseif($type == 1){
            $quotation->select('p.name',
                // DB::raw('SUM(pqm.num) as product_area')
                DB::raw('SUM(CASE WHEN pqm.cut = 0 AND p.product_type_id = 1 THEN (p.horizontal * p.vertical * pqm.num) WHEN pqm.cut = 1 AND p.product_type_id = 1 THEN pqm.num ELSE 0 END) AS product_area'),
              )->where('type', 1)->where('pqm.status', 1)->where('p.product_type_id', 1)
                ->join('product_quotation_materials AS pqm', 'pqm.quotation_id', '=', 'quotations.id')
                ->join('products AS p', 'p.id', '=', 'pqm.product_id')
                ->groupBy('pqm.id');
        }
        $quotation->orderBy('product_area', 'DESC')->limit(1);
        $result = $quotation->first();

        return $result;
    }
}
