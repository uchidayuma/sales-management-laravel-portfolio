  <div class="tab-pane fade" id="pills-leave" role="tabpanel" aria-labelledby="pills-leave-tab">
    <table class="common-table-stripes-row text-left f10">
      <thead class="common-table-stripes-row-thead f08">
        <tr>
          <th scope="col">案件No</th>
          <th scope="col">担当FC</th>
          <th scope="col">問い合わせ日時</th>
          <th scope="col">依頼種別</th>
          <th scope="col">顧客名</th>
          <th scope="col">住所</th>
        </tr>
      </thead>
      <tbody>
      @foreach($leave_alone_list as $l)
        <tr class='f09'>
          <td class="common-table-stripes-row-tbody__td"><a href="{{ route('contact.show', ['id' => $l->id]) }}" dusk="contact-detail">{{ displayContactId($l) }}</a></td>
          <td class="common-table-stripes-row-tbody__td"><a href="{{ route('users.show', ['id' => $l->user_id]) }}" dusk="contact-detail">{{ $l['user_name'] }}</a></td>
          <td class="common-table-stripes-row-tbody__td">{{ date('m月d日', strtotime($l->created_at)) }}</td>
          <td class="common-table-stripes-row-tbody__td"><img class="contact-type-label" src="/images/icons/contact-types/{{$l->contact_type_id}}.png"></td>
          <td class="common-table-stripes-row-tbody__td">{{ customerName($l) }}</td>
          <td class="common-table-stripes-row-tbody__td">{{$l->pref}}{{$l->city}}{{$l->street}}</td>
        </tr>
      @endforeach
      </tbody>
    </table>
  </div> <!-- tab 4つめ -->
