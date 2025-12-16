@section('admin-dashboard')
  <div class="card-deck" style="height:50%;">

    <div class="card text-right" style="margin:5px; padding: 2px; width:50%; border: 5px;">
        <ul class="list-group list-group-flush">
            <li class="list-group-item" style="border-style: solid;">
                新規お問い合わせ件数<a class="btn" data-toggle="collapse" href="#collapseExample1" role="button" aria-expanded="false" aria-controls="collapseExample1" dusk="admin-caretdown-1"><i class="fas fa-caret-down"></i></a>
            </li>
        </ul>
      <div class="collapse" id="collapseExample1">
        <div class="card card-body" style="margin:5px;">
        <div class="canvas">
          <canvas id="myChart" width="400" height="400"></canvas>
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

            <p class="card-text">昨日：{{$countYesterday}}件</p>
            <p class="card-text">おとつい：{{$countDayBeforeYesterday}}件</p>
            <p class="card-text">先月：{{$contactLastMonthTotal}}件</p>
            <p class="card-text">平均：{{$avg}}件</p>
            <p class="card-text">合計：{{$contactTotal}}件</p>
        </div>
        </div>
      </div>
    </div>

    <div class="card text-right" style="margin: 5px; padding: 2px; width:50%; border: 5px;">
        <ul class="list-group list-group-flush">
            <li class="list-group-item" style="border-style: solid;">
                新規発注件数<a class="btn" data-toggle="collapse" href="#collapseExample2" role="button" aria-expanded="false" aria-controls="collapseExample1" dusk="admin-caretdown-2"><i class="fas fa-caret-down"></i></a>
            </li>
        </ul>
      <div class="collapse" id="collapseExample2">
        <div class="card card-body" style="margin:5px;">
        <div class="canvas">
          <canvas id="myChart2" width="400" height="400"></canvas>
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
            <p class="card-text">昨日：{{$transactionYesterday}}件</p>
            <p class="card-text">おとつい：{{$transactionDayBeforeYesterday}}件</p>
            <p class="card-text">先月：{{$transactionLastMonthTotal}}件</p>
            <p class="card-text">平均：{{$avgTransaction}}件</p>
            <p class="card-text">合計：{{$transactionTotal}}件</p>
        </div>
        </div>
    </div>
    </div>
  </div>

<!--下段-->
  <div class="card-deck">
    <!--3rd card tabs-->
    <div class="card text-right" style="margin: 5px; padding: 2px; width:100%; border: 5px;">
        <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active btn-primary" id="pills-home-tab" data-toggle="pill" href="#pills-home" role="tab" aria-controls="pills-home" aria-selected="true">訪問見積もりFC未連絡リスト</a>
            </li>
            <li class="nav-item">
                <a class="nav-link btn-info" id="pills-profile-tab" data-toggle="pill" href="#pills-profile" role="tab" aria-controls="pills-profile" aria-selected="false">図面見積もりFC未連絡リスト</a>
            </li>
            <li class="nav-item">
                <a class="nav-link btn-warning" id="pills-contact-tab" data-toggle="pill" href="#pills-contact" role="tab" aria-controls="pills-contact" aria-selected="false">お客様FC未連絡リスト</a>
            </li>
        </ul>

      <div class="tab-content" id="pills-tabContent">
        <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
            <ul class="list-group list-group-flush">
                <li class="list-group-item" style="border-style: solid;">
                    <a class="btn" data-toggle="collapse" href="#collapseExample3" role="button" aria-expanded="false" aria-controls="collapseExample3" dusk="admin-caretdown-3"><i class="fas fa-caret-down"></i></a>
                </li>
            </ul>
        </div>
        <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
            <ul class="list-group list-group-flush">
                <li class="list-group-item" style="border-style: solid;">
                    <a class="btn" data-toggle="collapse" href="#collapseExample4" role="button" aria-expanded="false" aria-controls="collapseExample4" dusk="admin-caretdown-4"><i class="fas fa-caret-down"></i></a>
                </li>
            </ul>
        </div>
        <div class="tab-pane fade" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab">
            <ul class="list-group list-group-flush">
                <li class="list-group-item" style="border-style: solid;">
                    <a class="btn" data-toggle="collapse" href="#collapseExample5" role="button" aria-expanded="false" aria-controls="collapseExample5" dusk="admin-caretdown-5" ><i class="fas fa-caret-down"></i></a>
                </li>
            </ul>
        </div>
      </div>
      <!--3rd card tabs-->

      <!--3rd card content-->
      <div class="collapse" id="collapseExample3">
        <div class="card card-body">
            <div class="tab-content" id="pills-tabContent">
            <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
            <table class="common-table-stripes-row">
            <tbody>
            @if(!empty($visitCustomers))
                @foreach($visitCustomers as $c)
                <tr>
                    <td class="common-table-stripes-row-tbody__td"><a href="{{ route('contact.show', ['id' => $c->id]) }}" dusk="contact-detail">{{ displayContactId($c) }}</a></td>
                    <td class="common-table-stripes-row-tbody__td">{{ date('Y年m月d日', strtotime($c->created_at)) }}</td>
                    <td class="common-table-stripes-row-tbody__td">{{ isCompany($c) ? '法人' : '個人' }}</td>
                    <td class="common-table-stripes-row-tbody__td">{{ customerName($c) }}</td>
                    <td class="common-table-stripes-row-tbody__td">{{$c->pref}}{{$c->city}}{{$c->street}}</td>
                    <td class="common-table-stripes-row-tbody__td d-flex flex-nowrap">{!! !empty($c->sample_send_at) ? sampleSend($c->sample_send_at) : ''!!}{!! returnStepLabel($c->step_id, $c->cancel_step) !!}</td>
                </tr>
                @endforeach
            @endif
            </tbody>
            </table>
            </div>
            </div>
        </div>
      </div>

      <div class="collapse" id="collapseExample4">
        <div class="card card-body">
        <div class="tab-content" id="pills-tabContent">
        <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
        <table class="common-table-stripes-row">
        <tbody>
        @if(!empty($drawingCustomers))
            @foreach($drawingCustomers as $d)
            <tr>
                <td class="common-table-stripes-row-tbody__td"><a href="{{ route('contact.show', ['id' => $d->id]) }}" dusk="contact-detail">{{ displayContactId($d) }}</a></td>
                <td class="common-table-stripes-row-tbody__td">{{ date('Y年m月d日', strtotime($d->created_at)) }}</td>
                <td class="common-table-stripes-row-tbody__td">{{ isCompany($d) ? '法人' : '個人' }}</td>
                <td class="common-table-stripes-row-tbody__td">{{ customerName($d) }}</td>
                <td class="common-table-stripes-row-tbody__td">{{$d->pref}}{{$d->city}}{{$d->street}}</td>
            </tr>
            @endforeach
        @endif
        </tbody>
        </table>
        </div>
        </div>
        </div>
      </div>

      <div class="collapse" id="collapseExample5">
        <div class="card card-body">
        <div class="tab-content" id="pills-tabContent">
        <div class="tab-pane fade" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab">
        <table class="common-table-stripes-row">
        <tbody>
        @if(!empty($contactShippings))
            @foreach($contactShippings as $s)
            <tr>
                <td class="common-table-stripes-row-tbody__td"><a href="{{ route('contact.show', ['id' => $s->id]) }}" dusk="contact-detail">{{ displayContactId($s) }}</a></td>
                <td class="common-table-stripes-row-tbody__td">{{ date('Y年m月d日', strtotime($s->created_at)) }}</td>
                <td class="common-table-stripes-row-tbody__td">{{ isCompany($s) ? '法人' : '個人' }}</td>
                <td class="common-table-stripes-row-tbody__td">{{ customerName($s) }}</td>
                <td class="common-table-stripes-row-tbody__td">{{$s->pref}}{{$s->city}}{{$s->street}}</td>
            </tr>
            @endforeach
        @endif
        </tbody>
        </table>
        </div>
      </div>
      </div>
      </div>
     </div>
    <!--3rd card-->

    <!--4th card-->
    <div class="card text-right" style="margin: 5px; padding: 2px; width:50%; border: 5px;">
        <ul class="list-group list-group-flush">
            <li class="list-group-item" style="border-style: solid;">
                人工芝在庫数<a class="btn" data-toggle="collapse" href="#collapseExample6" role="button" aria-expanded="false" aria-controls="collapseExample6" dusk="admin-caretdown-6"><i class="fas fa-caret-down"></i></a>
            </li>
        </ul>
        <div class="collapse" id="collapseExample6">
        <div class="card card-body text-left" style="margin:5px;">
            <table class="common-table-stripes-row">
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
        </div>
        </div>
  </div> <!--4th card-->

</div>

@endsection