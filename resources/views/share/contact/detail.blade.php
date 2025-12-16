@extends('layouts.layout')

@section('css')
<link href="{{ asset('plugins/flatpickr/flatpickr.min.css') }}" rel="stylesheet" />
<link href="{{ asset('styles/contact/datail.min.css') }}" rel="stylesheet" />
@endsection

@section('javascript')
<script src="{{ asset('plugins/flatpickr/flatpickr.js') }}" defer></script>
<script src="{{ asset('plugins/flatpickr/ja.js') }}" defer></script>
<script src="{{ asset('js/contact/detail.js') }}" defer></script>
<script src="{{ asset('js/quotation/select.js') }}" defer></script>
<!-- <script src="{{ asset('js/share/drag-and-drop.js') }}" type="text/javascript" defer></script> -->
@endsection

@section('content')
  <div>
@if(isFc())
  @if($isMainFc === false && $existenceMainFc === false && $isOnSite === true)
    <p class="px-xl-5 alert alert-warning alert-font f11 bold">他FCが対応している可能性があります</p>
  @elseif($isMainFc === false && $existenceMainFc === true)
    <p class="px-xl-5 alert alert-warning alert-font f11 bold">他FC対応中のため、見積書の作成はできません</p>
  @elseif($isMainFc === true && $existenceMainFc === true)
    <p class="px-xl-5 alert alert-warning alert-font f11 bold">担当中の案件です</p>
  @endif 
@endif 
  </div>
<div class="breadcrumbs d-flex justify-content-between">
  <div>{!! $breadcrumbs->render() !!}</div>
  <div>
    <a class="px-xl-5 btn btn-light mr-3" href="{{route('contact.customers')}}">戻る</a>
@if(isAdmin())
    <a type="button" href="{{ route('assigned.edit', ['id' => $contact->id ]) }}" class="px-xl-5 edit-buttn btn btn-warning mr-3" dusk='edit'>編集</a>
@endif
  </div>
</div>
<div class="datail-table p20">
  <table class="common-table-stripes-column">
    <tbody>
  @if(!empty($quotations[0]) && (isAdmin() || $isMainFc == true || is_null($contact->main_user_id)))
      <tr class='p10'>
        <th>見積もり書</th>
        <td>{{ !empty($quotations) ? count($quotations) : '0' }}枚発行済み <button class='btn btn-success ml20' dusk='select-quotation-modal' type='button' data-toggle="modal" data-target="#select-modal">{{ is_null($contact->quotation_id) && !empty($quotations[1]) ? '採用された見積書を選択して確定する' : 'この案件の見積もり書一覧を確認'}}</button></td>
      </tr>
  @endif
  @if(!empty($contact->quotation_id))
      <tr class='p10'>
        <th>顧客採用見積もり書</th>
        <td><a href="{{ route('quotations.show', ['id' => $contact->quotation_id]) }}" target="blank">見積もり書No{{$contact->quotation_id}}を確認<i class="fas fa-external-link-alt ml5"></i></a></td>
      </tr>
  @endif
      <tr>
        <th>案件No</th>
        <td>{{ displayContactId($contact) }}</td>
      </tr>
      <tr>
        <th>登録日</th>
        <td>{{ date('Y年m月d日', strtotime($contact->created_at)) }}</td>
      </tr>
  @if (!is_null($contact->fc_assigned_at))
      <tr>
        <th>依頼日</th>
        <td>{{ date('Y年m月d日', strtotime($contact->fc_assigned_at)) }}</td>
      </tr>
  @endif
      <tr>
        <th>お問い合わせ種別</th>
        <td>{{ ($contact->free_sample_required == 1 ? 'サンプル請求 → ' : '').$contact->contact_type_name }}</td>
      </tr>
  @if (isAdmin() && !is_null($same_customer_contacts_id))
      <tr>
        <th>同一顧客</th>
        <td>
    @foreach($same_customer_contacts_id as $same)
          <a href="{{route('contact.show', ['id' => $same['contact_id']]) }}" target='blank' class='mr20' dusk='same-link'>{{ $same['contact_id'] }}</a>
    @endforeach
        </td>
      </tr>
  @endif
      <tr>
        <th>無料サンプル</th>
        <td>{{ $contact->free_sample }}</td>
      </tr>
      <tr>
        <th>名前</th>
        <td>{{ isCompany($contact) ? $contact->company_name : $contact->surname. $contact->name }}</td>
      </tr>
      <tr>
        <th>フリガナ</th>
        <td>{{ isCompany($contact) ? $contact->company_ruby : $contact->surname_ruby. $contact->name_ruby }}</td>
      </tr>
  @if(isCompany($contact))
      <tr>
        <th>担当者名</th>
        <td>{{ $contact->surname. $contact->name }}</td>
      </tr>
      <tr>
        <th>担当者名フリガナ</th>
        <td>{{ $contact->surname_ruby. $contact->name_ruby }}</td>
      </tr>
      <tr>
        <th>業種</th>
        <td>{{ $contact->industry }}</td>
      </tr>
  @endif
  @if(isAdmin() && !is_null($contact->registered_user_id))
      <tr>
        <th>案件登録FC</th>
        <td><a href="{{route('users.show', ['id' => $contact->registered_user_id]) }}" target='blank'>{{ $contact->registered_user_name }}</a></td>
      <tr>
  @endif
  @if(isAdmin() && !empty($contact->fc_name))
      <tr>
        <th>担当FC</th>       
        <td><a href="{{route('users.show', ['id' => $contact->fcid]) }}" target='blank'>{{ $contact->fc_name }}</a></td>
      <tr>
  @endif
        <th>郵便番号</th>
        <td>{{ $contact->zipcode }}</td>
      </tr>
      <tr>
        <th>住所</th>
        <td>{{ $contact->pref }}{{ $contact->city }}{{ $contact->street }}</td>
      </tr>
      <tr>
        <th>TEL</th>
        <td class='p10 desired'>
          ①：{{ $contact->tel }}
          <br>
          ②：{{ $contact->tel2 }}
        </td>
      <tr>
        <th>FAX</th>
        <td>{{ $contact->fax }}</td>
      </tr>
      <tr>
        <th>MAIL</th>
        <td>{{ $contact->email }}</td>
      </tr>
      <tr>
        <th>年代</th>
        <td>{{ $contact->age }}</td>
      </tr>
    @if(isContactType($contact->contact_type_id, [2,3,6,7]))
      <tr>
        <th>お見積もり内容</th>
        <td>{{ !empty($contact->quote_details) ? $contact->quote_details : '---' }}</td>
      </tr>
    @endif
      <tr>
        <th>下地状況</th>
         <td>{{ $contact->ground_condition }}</td>
      </tr>
      <tr>
        <th>施工場所面積</th>
        <td>縦{{ $contact->vertical_size }}{{ is_numeric($contact->vertical_size) ? 'm' : '' }} × 横{{ $contact->horizontal_size }}{{ is_numeric($contact->horizontal_size) ? 'm' : '' }}</td>
      </tr>
      <tr>
        <th>平米数</th>
        <td>{{ $contact->square_meter }}平米</td>
      </tr>
      <tr>
        <th>希望商品</th>
        <td>{{ $contact->desired_product }}</td>
      </tr>
      <tr>
        <th>希望日</th>
        <td class='p10 desired'>
          第一希望日：{{ !empty($contact->desired_datetime1) ? date('Y年m月d日 G時i分', strtotime($contact->desired_datetime1)) : '' }}
          <br>
          第二希望日：{{ !empty($contact->desired_datetime2) ? date('Y年m月d日 G時i分', strtotime($contact->desired_datetime2)) : '' }}
        </td>
      </tr>
      <tr>
        <th>訪問先住所</th>
        <td>
          {{ $contact->visit_address }}
        </td>
      </tr>
      <tr>
        <th>人工芝の使用用途</th>
        <td>{{ $contact->use_application }}</td>
      </tr>
      <tr>
        <th>認知経路</th>
        <td>{{ $contact->where_find }}</td>
      </tr>
      <tr>
        <th>SNS</th>
        <td>{{ $contact->sns }}</td>
      </tr>
      <tr>
        <th>必要事項</th>
        <td>{{ $contact->requirement }}</td>
      </tr>
      <!-- 施工画像ダウンロード -->
  @if($contact->before_image1 || $contact->before_image2 || $contact->before_image3)
      <tr>
        <th>施工前画像</th>
        <td class='d-flex justify-content-between p10'>
          <div class="uploader js-uploader text-center w33 mb20">
            <h6 class='uploader__description mb10 bold'>施工前画像①</h6>
        @if( !empty($contact->before_image1))
            <img id='before1' class='w90 mb10' src="{{s3Url()}}images/before/{{$contact->id}}/{{$contact->before_image1}}" />
            <a class="btn btn-primary" href="{{ route('download.image', ['id' => $contact->id, 'path' => 'before', 'file' => $contact->before_image1, 'name' => '施工前画像①']) }}">ダウンロード</a>
        @endif
          </div>
          <div class="uploader js-uploader text-center w33 mb20">
            <h6 class='uploader__description mb10 bold'>施工前画像②</h6>
        @if( !empty($contact->before_image2))
            <img id='before2'  class='w90 mb10' src="{{s3Url()}}images/before/{{$contact->id}}/{{$contact->before_image2}}" />
            <a class="btn btn-primary" href="{{ route('download.image', ['id' => $contact->id, 'path' => 'before', 'file' => $contact->before_image2, 'name' => '施工前画像②' ]) }}">ダウンロード</a>
        @endif
          </div>
          <div class="uploader js-uploader text-center w33 mb20">
            <h6 class='uploader__description mb10 bold'>施工前画像③</h6>
        @if( !empty($contact->before_image3))
            <img id='before3'  class='w90 mb10' src="{{s3Url()}}images/before/{{$contact->id}}/{{$contact->before_image3}}" />
            <a class="btn btn-primary" href="{{ route('download.image', ['id' => $contact->id, 'path' => 'before', 'file' => $contact->before_image3, 'name' => '施工前画像③' ]) }}">ダウンロード</a>
        @endif
          </div>
        </td>
      </tr>
  @endif
      <!-- 完了画像ダウンロード -->
  @if($contact->after_image1 || $contact->after_image2 || $contact->after_image3)
      <tr>
        <th>施工完了後画像<br>（{!! !$contact->public ? '広告使用<span class="color-red">NG</span>' : '広告使用<span class="color-primary">OK</span>' !!}）</th>
        <td class='d-flex justify-content-between p10'>
          <div class="uploader js-uploader text-center w33 mb20">
            <h6 class='uploader__description mb10 bold'>施工後画像①</h6>
        @if( !empty($contact->after_image1))
            <img id='after1' class='w90 mb10' src="{{s3Url()}}images/after/{{$contact->id}}/{{$contact->after_image1}}" />
            <a class="btn btn-primary" href="{{ route('download.image', ['id' => $contact->id, 'path' => 'after', 'file' => $contact->after_image1, 'name' => '施工後画像①']) }}">ダウンロード</a>
        @endif
          </div>
          <div class="uploader js-uploader text-center w33 mb20">
            <h6 class='uploader__description mb10 bold'>施工後画像②</h6>
        @if( !empty($contact->after_image2))
            <img id='after2' class='w90 mb10' src="{{s3Url()}}images/after/{{$contact->id}}/{{$contact->after_image2}}" />
            <a class="btn btn-primary" href="{{ route('download.image', ['id' => $contact->id, 'path' => 'after', 'file' => $contact->after_image2, 'name' => '施工後画像②' ]) }}">ダウンロード</a>
        @endif
          </div>
          <div class="uploader js-uploader text-center w33 mb20">
            <h6 class='uploader__description mb10 bold'>施工後画像③</h6>
        @if( !empty($contact->after_image3))
            <img id='after3' class='w90 mb10' src="{{s3Url()}}images/after/{{$contact->id}}/{{$contact->after_image3}}" />
            <a class="btn btn-primary" href="{{ route('download.image', ['id' => $contact->id, 'path' => 'after', 'file' => $contact->after_image3, 'name' => '施工後画像③' ]) }}">ダウンロード</a>
        @endif
          </div>
        </td>
      </tr>
  @endif
      <tr>
        <th>コメント</th>
        <td class='p10 comment'>{!! nl2br($contact->comment) !!}</td>
      </tr>
      <tr>
        <th>添付資料</th>
        <td class='d-flex flex-wrap flex-column justify-content-between p10'>
          <div class="uploader js-uploader mb20">
              <p class='uploader__description'>資料①：{{ $contact->document1_original_name }}</p>
            @if(isset($contact->document1))
              <a class="btn btn-primary" href="{{ route('download.file', ['id' => $contact->id,'file' => $contact->document1, 'originalName' => $contact->document1_original_name ]) }}">ダウンロード</a>
            @endif
          </div>
          <div class="uploader js-uploader mb20">
              <p class='uploader__description'>資料②：{{ $contact->document2_original_name }}</p>
            @if(isset($contact->document2))
              <a class="btn btn-primary" href="{{ route('download.file', ['id' => $contact->id,'file' => $contact->document2 , 'originalName' => $contact->document2_original_name]) }}">ダウンロード</a>
            @endif
          </div>
          <div class="uploader js-uploader mb20">
              <p class='uploader__description'>資料③：{{ $contact->document3_original_name }}</p>
            @if(isset($contact->document3))
              <a class="btn btn-primary" href="{{ route('download.file', ['id' => $contact->id,'file' => $contact->document3, 'originalName' => $contact->document3_original_name ]) }}">ダウンロード</a>
            @endif
          </div>
          <div class="uploader js-uploader mb20">
              <p class='uploader__description'>資料④：{{ $contact->document4_original_name }}</p>
            @if(isset($contact->document4))
              <a class="btn btn-primary" href="{{ route('download.file', ['id' => $contact->id,'file' => $contact->document4, 'originalName' => $contact->document4_original_name ]) }}">ダウンロード</a>
            @endif
          </div>
          <div class="uploader js-uploader mb20">
              <p class='uploader__description'>資料⑤：{{ $contact->document5_original_name }}</p>
            @if(isset($contact->document5))
              <a class="btn btn-primary" href="{{ route('download.file', ['id' => $contact->id, 'file' => $contact->document5, 'originalName' => $contact->document5_original_name ]) }}">ダウンロード</a>
            @endif
          </div>
        </td>
      </tr>
      <tr>
        <th>施工完了日</th>
        <td>{{ $contact->completed_at ? date('Y年m月d日', strtotime($contact->completed_at)) : '' }}</td>
      </tr>
  @if(isAdmin() && !empty($contact->cancel_step))
      <tr>
        <th>キャンセルタイミング</th>
        <td>{{ stepName($contact->cancel_step) }}</td>
      </tr>  
  @endif
      <tr>
        <th>ステップ<span class='f08'>（ステータス）</span></th>
        <td>{{ $contact->step_name }}</td>
      </tr>  
      <tr>
        <th id='appointment'>アポイントメント日時</th>
        <td>{{ $contact->visit_time ? date('Y年m月d日 G時i分', strtotime($contact->visit_time)) : ''}}</td>
      </tr>
      <!-- FCが自分で送付した場合は sample_send_atに1970−01−01が入る -->
      <!-- そもそもサンプル送付不要案件なら表示しない -->
  @if($contact->free_sample != '不要') 
      <tr>
        <th>サンプル送付日</th>
    @if( isAdmin() )
      @if($contact->sample_send_at == '1970-01-01')
        <td>FCがサンプル送付</td>
      @else
        <td>{{  $contact->sample_send_at ? date('Y年m月d日', strtotime($contact->sample_send_at)) : '未送付' }}</td>
      @endif
    @else
      @if($contact->sample_send_at == '1970-01-01')
        <td>自社でサンプル送付</td>
      @else
        <td>{{ $contact->sample_send_at ? date('Y年m月d日', strtotime($contact->sample_send_at)) . 'に本部がサンプル送付' : '本部がサンプル送付（未送付）' }}</td>
      @endif
    @endif
      </tr>
  @endif
  @if(isAdmin() && !empty($contact->fc_assigned_at))
      <tr>
        <th>FC依頼日<span class='f08'>（GoogleMap経由）</span></th>
        <td>{{ date('Y年m月d日', strtotime($contact->fc_assigned_at)) }}</td>
      </tr>
  @endif
  @if(isAdmin())
      <tr>
        <th>メモ</th>
        <td>{!! nl2br($contact->memo) !!}</td>
      </tr>
      <tr>
        <th>FC案件確認日時</th>
        <td>{{ !empty($contact->fc_confirmed_at) ? date('Y年m月d日 H時i分', strtotime($contact->fc_confirmed_at)) : '未確認'}}</td>
      </tr>
  @endif
  @if(!empty($contact->etc_memo[0]))
    @foreach($contact->etc_memo[0] as $key => $val)
      <tr>
        <th>{{ $key }}</th>
        <td>{{ $val }}</td>
      </tr>
    @endforeach
  @endif
      <tr>
        <th>最終更新</th>
        <td>{{ date('Y年m月d日', strtotime($contact->updated_at)) }}</td>
      </tr>
    </tbody>
  </table>

  <div class="mt-4 d-flex justify-content-between"> 
    <a type="button" class="px-xl-5 btn btn-light" href="{{route('contact.customers')}}">戻る</a>
@if(isAdmin() || $contact->user_id == $user->id)
  @if(isAdmin() || ( isFc() && $contact->own_contact == 1 && $contact->step_id <= 5))
    <form action="{{ route('contact.destroy' , ['id' => $contact->id]) }}" method="post">
      @csrf
      <input type="submit" class="px-xl-5 btn btn-danger" onclick="return confirm('お問い合わせを削除します。よろしいですか？')" value="お問い合わせ削除" dusk='delete'/>
    </form>
  @endif
    {{-- キャンセル案件にはキャンセル案件に変更ボタンを出さない --}}
  @if((isAdmin() && empty($contact->cancel_step))|| (isFc() && is_null($contact->cancel_step) && $contact->step_id <= 5))
      <form action="{{ route('contact.cancel.submit' , ['id' => $contact->id]) }}" method="post">
        @csrf
        <input type="submit" class="px-xl-5 btn btn-secondary" onclick="return confirm('この案件をキャンセル扱いにします。よろしいですか？')" value="キャンセル案件に変更" dusk='cancel'/>
      </form>
  @endif
  @if($switchContactType == true && $contact->step_id <= 5)
    <form action="{{ route('contact.type.update' , ['id' => $contact->id]) }}" method="post" >
      @csrf
      @method('PUT')
      <input type="submit" class="px-xl-5 btn btn-secondary mr-3" onclick="return confirm('図面見積もりを訪問見積もりに変更します。よろしいですか？')" value="訪問見積もりに変更" dusk='switch'/>
    </form>
  @endif
    {{-- キャンセル案件には編集ボタン出さない --}}
  @if(empty($contact->cancel_step))
      <a type="button" href="{{ route('assigned.edit', ['id' => $contact->id ]) }}" class="px-xl-5 edit-buttn btn btn-warning">編集</a>
  @endif
  @if(!empty($contact->cancel_step) && ($contact->user_id === $contact->main_user_id || is_null($contact->main_user_id)))
      <form action="{{ route('contact.restore.cancel' , ['id' => $contact->id]) }}" method="post" >
        @csrf
        @method('PUT')
        <input type="submit" class="px-xl-5 btn btn-warning mr-3" onclick="return confirm('キャンセル案件を復元させます。よろしいですか？')" value="キャンセル案件を復元" dusk='restore-cancel'/>
      </form>
  @endif
@endif
@if($contact->step_id == '6' && ($contact->contact_type_id == '2' || $contact->contact_type_id == '6')  && $contact->quote_details == '材料のみ')
    <form action="{{ route('report.admin.finish') }}" method='POST'>
    @csrf
      <input type='hidden' name='contact_id' value="{{ $contact->id }}">
      <button class="px-xl-5 edit-buttn btn btn-primary" dusk="finish">材料納品完了</button>
    </form>
@endif
  </div>

</div>

<!-- Modal -->
<div class="modal fade" id="select-modal" tabindex="-1" role="dialog" aria-labelledby="selectModalLabel" aria-hidden="true">
  <form action="{{ route('quotations.select.update', ['contactId' => $contact->id] ) }}" method="post">
    @csrf
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
      <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="selectModalLabel">案件No.{{ $contact->id }}の見積もり{{ $contact->step_id=='5' && !empty($quotations[1]) ? '確定' : '確認'}}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body row p30">
      @if(!empty($quotations))
        @foreach($quotations AS $key => $q)
          <div class="form-check col-md-6 mb30">
            <input class="form-check-input" type="radio" name='quotation_id' value="{{ $q->id }}" id="radio{{ $key }}" {{checked($contact->quotation_id == $q->id)}}>
            <label class="form-check-label mb10" for="radio{{ $key }}" dusk="radio{{ $key }}">見積書No.{{ $q['id'] }}：{{ number_format( $q['total'] ) }}円</label>
            <a class='btn btn-info' href="{{ route('quotations.show', ['id' => $q->id]) }}" target='blank'>見積もり書を確認</a>
          </div>
        @endforeach
      @endif
        </div>
        <div class="modal-footer">
        @if($contact->step_id > 5 && !empty($quotations[1]))
          <button type="button" class="btn btn-secondary" data-dismiss="modal">キャンセル</button>
          <button class="btn btn-success" dusk='quotation-submit'>顧客選択見積もりを確定</button>
        @else
          <button type="button" class="btn btn-secondary" data-dismiss="modal">閉じる</button>
        @endif
        </div>
      </div>
    </div>
  </form>
</div>
@endsection

