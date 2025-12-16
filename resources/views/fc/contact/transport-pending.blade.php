<table class="common-table-stripes-row">
  <thead class="common-table-stripes-row-thead">
    <tr>
      <th scope="col">発注書No</th>
      <th scope="col">案件No</th>
      <th scope="col">発注日</th>
      <th scope="col">納品希望日</th>
      <th scope="col">名前</th>
      <th scope="col">住所</th>
      <th scope="col">追加発注</th>      
      <th scope="col">編集</th>
      <th scope="col">削除</th>
    </tr>
  </thead>
  <tbody>
  @foreach($pendings as $p)
    <tr>
      <td class="common-table-stripes-row-tbody__td"><a href="{{ route('transactions.show', ['id' => $p['transaction_id']]) }}">{{ $p['transaction_id'] }}</a></td>
      <td class="common-table-stripes-row-tbody__td"><a href="{{ !is_null($p['contact_id']) ? route('contact.show', ['id' => $p['contact_id']]) : '#' }}">{{ !is_null($p['contact_id']) ? $p['contact_id'] : '対応案件なし' }}</a></td>
      <td class="common-table-stripes-row-tbody__td">{{date('Y年m月d日', strtotime($p['transaction_created_at']))}}</td>
      <td class="common-table-stripes-row-tbody__td">{{date('Y年m月d日', strtotime($p['delivery_at']))}}</td>
      <td class="common-table-stripes-row-tbody__td js-name">{{ !is_null($p['contact_id']) ? customerName($p) : '顧客なし' }}</td>
      <td class="common-table-stripes-row-tbody__td">{{ $p['pref'] . $p['city'] . $p['street'] }}</td>
      <td class="common-table-stripes-row-tbody__td"><a href="{{ !is_null($p['contact_id']) ? route('create.order', ['contactId' => $p['contact_id']]) : '#' }}" class="{{ subTransactionLimitDate($p['contact_id']) && intval($p['transaction_count']) < 3 ? 'btn btn-primary btn-xs' : 'btn btn-secondary btn-xs disabled' }}" dusk="create{{$p['contact_id']}}">追加発注</a>
      <td class="common-table-stripes-row-tbody__td"><a href="{{ route('transactions.edit', ['transactionId' => $p['transaction_id']])}}" class="{{ transactionCancelAble($p['transaction_created_at']) ? 'btn btn-info btn-xs' : 'btn btn-secondary btn-xs disabled' }}">編集</a>
      <td class="common-table-stripes-row-tbody__td">
        <form id='delete-form' class='delete-form' action="{{ route('transactions.delete', ['transactionId' => $p['transaction_id']]) }}" method="post" enctype="multipart/form-data">
          <input id='delete-id' type='submit' class="btn btn-xs btn-danger js-remove-transaction" value='キャンセル' dusk='cancel-transaction' {{ transactionCancelAble($p['transaction_created_at']) ? '' : 'disabled' }}/>
          @csrf
        </form> 
      </td>
    </tr>
  @endforeach
  </tbody>
</table>