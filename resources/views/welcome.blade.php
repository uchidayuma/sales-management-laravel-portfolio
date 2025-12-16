@extends('layouts.layout')

@section('css')
<link href="{{ asset('styles/articles/create.min.css') }}" rel="stylesheet" />
<link href="{{ asset('styles/dashboard.min.css') }}" rel="stylesheet" />
@endsection

@section('javascript')
<script src="{{ asset('js/dashboard/admin.js') }}" defer></script>
@endsection

@section('content')
<div class="flex-center position-ref full-height">
  <div class="content pt15">
    {{-- <iframe src="https://docs.google.com/spreadsheets/d/e/2PACX-1vTjmzpmO5n7gKzFCd3pUZ0npxwpyz9qw88lPgptXUTV3n8bEF3O_oAAoeX0CCSzOp6ejWGWjx9OGnlN/pubhtml?widget=true&amp;headers=false" style="width:100%; height:400px"></iframe> --}}
@if(isAdmin())
<!--上段-->
<div class="d-flex">
  <!--3rd card tabs-->
  <div class="card w70 p0" style="margin:5px; border:5px;">
    <ul class="nav nav-tabs nav-justified" id="pills-tab" role="tablist">
      <li class="nav-item">
        <a class="nav-link active btn-primary f10" id="pills-home-tab" data-toggle="pill" href="#pills-visit" role="tab" aria-controls="pills-home" aria-selected="true">訪問見積もり<br>FC未連絡リスト</a>
      </li>
      <li class="nav-item">
        <a class="nav-link btn-info f10" id="pills-profile-tab" data-toggle="pill" href="#pills-drow" role="tab" aria-controls="pills-profile" aria-selected="false">図面見積もり<br>FC未連絡リスト</a>
      </li>
      <li class="nav-item">
        <a class="nav-link btn-warning f10" id="pills-contact-tab" data-toggle="pill" href="#pills-contact" role="tab" aria-controls="pills-contact" aria-selected="false">部材発注<br>未発送リスト</a>
      </li>
      <li class="nav-item">
        <a class="nav-link btn-secondary f10" id="pills-leave-tab" data-toggle="pill" href="#pills-leave" role="tab" aria-controls="pills-leave" aria-selected="false" dusk='leave-alone-list'>案件放置<br>リスト</a>
      </li>
      <li class="nav-item">
        <a class="nav-link btn-danger f10" id="pills-large-tab" data-toggle="pill" href="#pills-large" role="tab" aria-controls="pills-large" aria-selected="false" dusk='large-list'>大規模案件<br>(100m²以上)</a>
      </li>
    </ul>

    <!-- Collapse はいったんコメントアウト -->
              <!-- <a class="btn" data-toggle="collapse" href="#collapseContactList" role="button" aria-expanded="false" aria-controls="collapseExample3" dusk="admin-caretdown-3"><i class="fas fa-caret-down"></i></a> -->

    <div class="collapse show" id="collapseContactList">
      <div class="tab-content" id="pills-tabContent">
        <div class="tab-pane fade show active" id="pills-visit" role="tabpanel" aria-labelledby="pills-home-tab">
            <table class="common-table-stripes-row text-left f10">
              <thead class="common-table-stripes-row-thead f08">
                <tr>
                  <th scope="col">案件no</th>
                  <th scope="col">問い合わせ日時</th>
                  <th scope="col">依頼種別</th>
                  <th scope="col">顧客名</th>
                  <th scope="col">住所</th>
                </tr>
              </thead>
              <tbody>
        @if(!empty($visitcustomers))
            @foreach($visitcustomers as $c)
                <tr class='f09'>
                  <td class="common-table-stripes-row-tbody__td"><a href="{{ route('contact.show', ['id' => $c->id]) }}" dusk="contact-detail">{{ displaycontactid($c) }}</a></td>
                  <td class="common-table-stripes-row-tbody__td">{{ date('m月d日', strtotime($c->created_at)) }}</td>
                  <td class="common-table-stripes-row-tbody__td"><img class="contact-type-label" src="/images/icons/contact-types/{{$c->contact_type_id}}.png"></td>
                  <td class="common-table-stripes-row-tbody__td">{{ customername($c) }}</td>
                  <td class="common-table-stripes-row-tbody__td">{{$c->pref}}{{$c->city}}{{$c->street}}</td>
                </tr>
            @endforeach
        @endif
              </tbody>
            </table>
        </div> <!-- tab 1つめ -->
        <div class="tab-pane fade" id="pills-drow" role="tabpanel" aria-labelledby="pills-profile-tab">
            <table class="common-table-stripes-row">
              <thead class="common-table-stripes-row-thead f08">
                <tr>
                  <th scope="col">案件No</th>
                  <th scope="col">問い合わせ日時</th>
                  <th scope="col">依頼種別</th>
                  <th scope="col">顧客名</th>
                  <th scope="col">住所</th>
                </tr>
                </thead>
              <tbody>
      @if(!empty($drawingCustomers))
          @foreach($drawingCustomers as $d)
                <tr class='f09'>
                  <td class="common-table-stripes-row-tbody__td"><a href="{{ route('contact.show', ['id' => $d->id]) }}" dusk="contact-detail">{{ displayContactId($d) }}</a></td>
                  <td class="common-table-stripes-row-tbody__td">{{ date('m月d日', strtotime($d->created_at)) }}</td>
                  <td class="common-table-stripes-row-tbody__td"><img class="contact-type-label" src="/images/icons/contact-types/{{$d->contact_type_id}}.png"></td>
                  <td class="common-table-stripes-row-tbody__td">{{ customerName($d) }}</td>
                  <td class="common-table-stripes-row-tbody__td">{{$d->pref}}{{$d->city}}{{$d->street}}</td>
                </tr>
          @endforeach
      @endif
              </tbody>
            </table>
        </div> <!-- tab 2つめ -->
        <div class="tab-pane fade" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab">
            <table class="common-table-stripes-row">
              <thead class="common-table-stripes-row-thead f08">
                <tr>
                  <th scope="col">案件No</th>
                  <th scope="col">問い合わせ日時</th>
                  <th scope="col">依頼種別</th>
                  <th scope="col">顧客名</th>
                  <th scope="col">住所</th>
                </tr>
                </thead>
              <tbody>
      @if(!empty($contactShippings))
          @foreach($contactShippings as $s)
                <tr class='f09'>
                  <td class="common-table-stripes-row-tbody__td"><a href="{{ route('contact.show', ['id' => $s->id]) }}" dusk="contact-detail">{{ displayContactId($s) }}</a></td>
                  <td class="common-table-stripes-row-tbody__td">{{ date('m月d日', strtotime($s->created_at)) }}</td>
                  <td class="common-table-stripes-row-tbody__td"><img class="contact-type-label" src="/images/icons/contact-types/{{$s->contact_type_id}}.png"></td>
                  <td class="common-table-stripes-row-tbody__td">{{ customerName($s) }}</td>
                  <td class="common-table-stripes-row-tbody__td">{{$s->pref}}{{$s->city}}{{$s->street}}</td>
                </tr>
          @endforeach
      @endif
              </tbody>
            </table>
        </div> <!-- tab 3つめ -->
        @include('home.leave-alone')<!-- tab4つめ -->
        <div class="tab-pane fade" id="pills-large" role="tabpanel" aria-labelledby="pills-large-tab">
            <table class="common-table-stripes-row">
              <thead class="common-table-stripes-row-thead f08">
                <tr>
                  <th scope="col">案件No</th>
                  <th scope="col">顧客名</th>
                  <th scope="col">見積書No</th>
                  <th scope="col">人工芝の種類</th>
                  <th scope="col">見積もり面積</th>
                  <th scope="col">担当FC</th>
                  {{-- <th scope="col">住所</th> --}}
                </tr>
                </thead>
              <tbody>
                <tr class='f10'>
                  <td class="common-table-stripes-row-tbody__td" colspan="6">過去半年間の案件で施工面積100㎡を超える可能性のある案件をリストアップ</td>
                </tr>
      @if(!empty($large_contacts))
          @foreach($large_contacts as $lc)
                <tr class='f09'>
                  <td class="common-table-stripes-row-tbody__td"><a href="{{ route('contact.show', ['id' => $lc->id]) }}" target="blank">{{ displayContactId($lc) }}</a></td>
                  <td class="common-table-stripes-row-tbody__td">{{ customerName($lc) }}</td>
            @if(!empty($lc->quotation_id))
                  <td class="common-table-stripes-row-tbody__td"><a href="{{ route('quotations.show', ['id' => $lc->quotation_id]) }}" target="blank">{{ $lc->quotation_id }}</td>
            @else
                  <td class="common-table-stripes-row-tbody__td">なし</td>
            @endif
            @if(!empty($lc->most_product))
                  <td class="common-table-stripes-row-tbody__td">{{$lc->most_product['name']}}</td>
            @else
                  <td class="common-table-stripes-row-tbody__td">{{$lc->desired_product}}</td>
            @endif
                  <td class="common-table-stripes-row-tbody__td">{{$lc->area}}㎡</td>
                  <td class="common-table-stripes-row-tbody__td">{{ $lc->user_name }}</td>
                  {{-- <td class="common-table-stripes-row-tbody__td">{{$s->pref}}{{$s->city}}{{$s->street}}</td> --}}
                </tr>
          @endforeach
      @endif
              </tbody>
            </table>
        </div> <!-- tab 5つめ -->
      </div><!-- tab content -->
    </div><!--3rd card tabs-->
  </div><!--3rd card-->

  <!--4th card-->
  <div class="card w30 p0" style="margin: 5px; width:50%; border: 5px;">
    <ul class="list-group list-group-flush">
      <li class="list-group-item f12" style="border-style: solid;">
        人工芝在庫数
        <a class="btn open-trigger" data-toggle="collapse" href="#collapseExample6" role="button" aria-expanded="false" aria-controls="collapseExample6" dusk="admin-caretdown-6"><i class="fas fa-caret-down"></i></a>
      </li>
    </ul>
    <div class="collapse show p5" id="collapseExample6">
      <table class="common-table-stripes-row mb0">
        <tbody>
  @if(!empty($stocks))
    @foreach($stocks as $st)
          <tr>
            <td class="common-table-stripes-row-tbody__td">{{ $st['name']}}</td>
            <td class="common-table-stripes-row-tbody__td js-name">{{ $st['stock'] }}</td>
          </tr>
    @endforeach
  @endif
        </tbody>
      </table>
    </div> <!--4th card-->
  </div> <!-- card desck -->

  </div>
  <div class="d-flex m0" style="height:50%;">
    <div class="card p0" style="margin:5px; width:50%; border: 5px;">
      <ul class="list-group list-group-flush">
        <li class="list-group-item f12" style="border-style: solid;">
          新規お問い合わせ件数
          <a class="btn open-trigger" data-toggle="collapse" href="#collapseExample1" role="button" aria-expanded="false" aria-controls="collapseExample1" dusk="admin-caretdown-1"><i class="fas fa-caret-down"></i></a>
        </li>
      </ul>
      <div class="collapse show p20" id="collapseExample1">
          <!-- <div class="canvas"> -->
        <canvas id="myChart" width="400" height="400"></canvas>
        <div class='chart-label'>
          <p class="card-text">昨日：{{$countYesterday}}件</p>
          <p class="card-text">おとつい：{{$countDayBeforeYesterday}}件</p>
          <p class="card-text">先月：{{$contactLastMonthTotal}}件</p>
          <p class="card-text">平均：{{$avg}}件</p>
          <p class="card-text">合計：{{$contactTotal}}件</p>
        </div>
      </div>
        <!-- </div> -->
    </div>

    <div class="card" style="margin: 5px; padding: 2px; width:50%; border: 5px;">
      <ul class="list-group list-group-flush">
        <li class="list-group-item f12" style="border-style: solid;">
          新規発注件数
          <a class="btn open-trigger" data-toggle="collapse" href="#collapseExample2" role="button" aria-expanded="false" aria-controls="collapseExample1" dusk="admin-caretdown-2"><i class="fas fa-caret-down"></i></a>
        </li>
      </ul>
      <div class="collapse show p20" id="collapseExample2">
        <canvas id="myChart2" width="400" height="400"></canvas>
        <div class='chart-label'>
          <p class="card-text">昨日：{{$transactionYesterday}}件</p>
          <p class="card-text">おとつい：{{$transactionDayBeforeYesterday}}件</p>
          <p class="card-text">先月：{{$transactionLastMonthTotal}}件</p>
          <p class="card-text">平均：{{$avgTransaction}}件</p>
          <p class="card-text">合計：{{$transactionTotal}}件</p>
        </div>
      </div>
    </div>
  </div> <!-- card deck -->
<!--下段-->

@elseif(isFc())
<div class="d-flex">

  <div class="card text-left p0 d-none d-sm-block" style="margin: 5px; width:50%; border: 5px;">
    <ul class="list-group list-group-flush">
      <li class="list-group-item f12" style="border-style: solid;">
          本部 → 図面見積もり依頼リスト
          <a class="btn open-trigger" data-toggle="collapse" href="#collapseExample7" role="button" aria-expanded="false" aria-controls="collapseExample6"><i class="fas fa-caret-down"></i></a>
      </li>
    </ul>
    <div class="collapse show" id="collapseExample7">
      <table class="common-table-stripes-row">
        <thead class="common-table-stripes-row-thead f08">
          <tr>
            <th scope="col">案件No</th>
            <th scope="col">問い合わせ日時</th>
            <th scope="col">依頼種別</th>
            <th scope="col">顧客名</th>
            <th scope="col">住所</th>
          </tr>
        </thead>
        <tbody>
      @if(!empty($drawingCustomers))
          @foreach($drawingCustomers as $d)
            <tr class='f09'>
              <td class="common-table-stripes-row-tbody__td"><a href="{{ route('contact.show', ['id' => $d->id]) }}" dusk="contact-detail">{{ displayContactId($d) }}</a></td>
              <td class="common-table-stripes-row-tbody__td">{{ date('m月d日', strtotime($d->created_at)) }}</td>
              <td class="common-table-stripes-row-tbody__td"><img class="contact-type-label" src="/images/icons/contact-types/{{$d->contact_type_id}}.png"></td>
              <td class="common-table-stripes-row-tbody__td">{{ customerName($d) }}</td>
              <td class="common-table-stripes-row-tbody__td">{{$d->pref}}{{$d->city}}{{$d->street}}</td>
            </tr>
          <!-- <tr>
              <td class="common-table-stripes-row-tbody__td"><a href="{{ route('contact.show', ['id' => $d->id]) }}" dusk="contact-detail">{{$d->id}}</a></td>
              <td class="common-table-stripes-row-tbody__td">{{ date('Y年m月d日', strtotime($d->created_at)) }}</td>
              <td class="common-table-stripes-row-tbody__td">{{ $d->type ? '個人': '法人'}}</td>
              <td class="common-table-stripes-row-tbody__td">{{ customerName($d) }}</td>
              <td class="common-table-stripes-row-tbody__td">{{$d->pref}}{{$d->city}}{{$d->street}}</td>
          </tr> -->
          @endforeach
      @endif
        </tbody>
      </table>
    </div>
  </div> <!--1st card-->

  <!--2nd card-->
  <div class="card text-left p0 d-none d-sm-block" style="margin: 5px; width:50%; border: 5px;">
    <ul class="list-group list-group-flush">
      <li class="list-group-item f12" style="border-style: solid;">
          本部 → 訪問見積もりリスト
          <a class="btn open-trigger" data-toggle="collapse" href="#collapseExample8" role="button" aria-expanded="false" aria-controls="collapseExample6"><i class="fas fa-caret-down"></i></a>
      </li>
    </ul>
    <div class="collapse show" id="collapseExample8">
      <table class="common-table-stripes-row">
        <thead class="common-table-stripes-row-thead f08">
          <tr>
            <th scope="col">案件No</th>
            <th scope="col">問い合わせ日時</th>
            <th scope="col">依頼種別</th>
            <th scope="col">顧客名</th>
            <th scope="col">住所</th>
          </tr>
        </thead>
        <tbody>
  @if(!empty($visitCustomers))
      @foreach($visitCustomers as $v)
          <tr class='f09'>
            <td class="common-table-stripes-row-tbody__td"><a href="{{ route('contact.show', ['id' => $v->id]) }}" dusk="contact-detail">{{ displayContactId($v) }}</a></td>
            <td class="common-table-stripes-row-tbody__td">{{ date('m月d日', strtotime($v->created_at)) }}</td>
            <td class="common-table-stripes-row-tbody__td"><img class="contact-type-label" src="/images/icons/contact-types/{{$v->contact_type_id}}.png"></td>
            <td class="common-table-stripes-row-tbody__td">{{ customerName($v) }}</td>
            <td class="common-table-stripes-row-tbody__td">{{$v->pref}}{{$v->city}}{{$v->street}}</td>
          </tr>
    @endforeach
  @endif
        </tbody>
      </table>
    </div>
  </div> <!--2nd card-->
</div><!--card deck-->

<div class="d-flex">
  <!--3rd card-->
  <div class="card text-left p0 d-none d-sm-block" style="margin: 5px; width:50%; border: 5px;">
    <ul class="list-group list-group-flush">
      <li class="list-group-item f12" style="border-style: solid;">
        今月売上件数
        <a class="btn open-trigger" data-toggle="collapse" href="#collapseExample9" role="button" aria-expanded="false" aria-controls="collapseExample6"><i class="fas fa-caret-down"></i></a>
      </li>
    </ul>
    <div class="collapse show p10" id="collapseExample9">
      <table class="table table-bordered">
        <thead>
          <tr>
            <th class='w60' scope="col">今月の売上</th>
            <th scope="col">順位</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class='w60' scope="row">{{ number_format($sales_this_month) }}円</td>
            <td>{{ $find_fc_order }}位</td>
          </tr>
        </tbody>
      </table>
      <table class="table table-bordered">
        <thead>
          <tr>
            <th class='w60' scope="col">今月の施工数</th>
            <th scope="col">順位</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class='w60' scope="row">合計 {{ !empty($works['count']) ? $works['count'] : 0 }}件</td>
            <td>{{ !empty($works['rank']) ? $works['rank'].'位' : '順位不明' }}</td>
          </tr>
        </tbody>
      </table>
    @if(!empty($rankingList))
        {{ $rankingList->links() }}
    @endif
    </div>
  </div> <!--3rd card-->

  <!--4th card-->
  <div class="card text-left p0 d-none d-sm-block" style="margin: 5px; width:50%; border: 5px;">
    <ul class="list-group list-group-flush">
      <li class="list-group-item f12" style="border-style: solid;">
        お知らせ未確認一覧
        <a class="btn open-trigger" data-toggle="collapse" href="#collapseExample10" role="button" aria-expanded="false" aria-controls="collapseExample6"><i class="fas fa-caret-down"></i></a>
      </li>
    </ul>
    <div class="collapse show" id="collapseExample10">
      <div id="myTabContent" class="tab-content mt-0">
        <div id="home" class="tab-pane active" role="tabpanel" aria-labelledby="home-tab">
          <table class='blog-table'>
        @foreach($articles AS $a)
            <tr class="blog-table-tr">
              <td class='p5'>{{ date('Y年m月d日', strtotime($a->published_at)) }}</td>
              <td class="w50 p5">{{ $a->title }}</td>
              <td class="text-right"><a href="{{ route('articles.show', ['article' => $a->id]) }}" class='btn btn-info px-3'>詳細を見る</a></td>
            </tr>
        @endforeach
          </table>
        </div>
      </div>
    </div>
  </div> <!--4th card-->
</div><!--card deck-->
<!-- スマホサイズ FCページ ここから -->
<div class="d-block d-sm-none">
  <a class="d-flex flex-column p30 mt30 mrl2 text-center f15 text-white bg-info" href="{{ route('before.report') }}">現場確認報告</a>
  <a class="d-flex flex-column p30 mt30 mrl2 text-center f15 text-white bg-info" href="{{ route('report.pending') }}">施工完了報告</a>
  <a class="d-flex flex-column p30 mt30 mrl2 text-center f15 text-white bg-info" href="{{ route('dispatched.list') }}">発送連絡一覧</a>
</div>
<!-- スマホサイズ FCページ ここまで -->
@endif

    </div>
</div>
<!-- <div class="flex-center position-ref full-height">
  <div class="content"> の閉じタグ-->
@if(isAdmin())
<!-- チャートJS -->
  <script>
      var newContacts = {!! json_encode($completeArray, JSON_HEX_TAG) !!};
              var ctx = document.getElementById('myChart').getContext('2d');
              var myChart = new Chart(ctx, {
                  type: 'line',
                  data: {
                      labels: ['1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','25','26','27','28','29','30','31'],
                      //labels: ['1','5','10','15','20','25','31'],
                      datasets: [{
                          label: '新規お問い合わせ件数',
                          //data: [12, 19, 3, 5, 2, 3],
                          data: newContacts,
                          fill: false,
                      }]
                  },
                  options: {
                      scales: {
                          yAxes: [{
                              ticks: {
                                  beginAtZero: true
                              }
                          }]
                      }
                  }
              });
  </script>
  <script>
      var newTransactions = {!! json_encode($completeTransactionArray, JSON_HEX_TAG) !!};
      var ctx = document.getElementById('myChart2').getContext('2d');
      var myChart = new Chart(ctx, {
          type: 'line',
          data: {
              labels: ['1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','25','26','27','28','29','30','31'],
              //labels: ['1','5','10','15','20','25','31'],
              datasets: [{
                  label: '新規発注件数',
                  //data: [12, 19, 3, 5, 2, 3],
                  data: newTransactions,
                  fill: false,
              }]
          },
          options: {
              scales: {
                  yAxes: [{
                      ticks: {
                          beginAtZero: true
                      }
                  }]
              },
          }
      });
  </script>
@endif
@endsection
