    <div id="table" class="table-editable">
      <table class="table table-responsive-md text-center" id="quotationTable">
        <thead>
          <tr>
            <th class="text-center">詳細</th>
            <th class="text-center">数量</th>
            <th class="text-center">単位</th>
            <th class="text-center">税抜単価（円）</th>
            <th class="text-center">税抜金額（円）</th>
            <th class="text-center">備考欄</th>
          </tr>
        </thead>
        <tbody id='quotationTableBody'>
@foreach($quotations as $q)
          <tr>
            <td>{{ $q->product_name ? $q->product_name : $q->name }}</td>
            <td>{{ floatNumberFormat($q->num) }}</td>
            <td>{{ $q->unit }}</td>
            <td>{{ number_format(!is_null($q->outer_unit_price) ? $q->outer_unit_price : $q->product_cut_unit_price ) }}</td>
    @if(!$q->price)
            <td>{{ number_format($q->product_cut_unit_price * $q->num) }}</td>
    @else
            <td>{{ number_format($q->price) }}</td>
    @endif
            <td>{{ !is_null($q->memo) ? $q->memo : '' }}</td>
          </tr>
@endforeach
@if(!empty($quotations[0]->discount))
          <tr>
            <td>お値引き</td>
            <td></td>
            <td></td>
            <td></td>
            <td>{{ number_format($quotations[0]->discount) }}</td>
          </tr>
@endif
        </tbody>
      </table>
      <textarea class="memo p10 mb20 w100" name="q[memo]" cols="100" rows="5" style='resize: none;' readonly>{{ $quotations[0]->quotation_memo }}</textarea>
    </div>
