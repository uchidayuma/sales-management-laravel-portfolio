window.onload = function () {
  // get query string
  initilazing(window.queryString.type, window.queryString.display, window.queryString.start, window.queryString.end);
  $('#filtering-form').removeClass("d-none");
  $('input[type=month]').MonthPicker({ Button: false });
    console.log(window.data);
  // sums=最下部の合計行
  var sums = {};
  var tmpSums = [];
  // header.push.apply( header, [ {name: `合計`, type: "number", align: "center", width: "100px"} ]);
  // console.log(header);
  // 最下部の合計行の箱だけ先に作っておく
  // sums['エリア'] = '合計';
  // tmpSums.forEach(ts => {
  //   sums[ts.name] = 0;
  // })
  // sums['合計'] = 0;
  // var contents = [];
  // 最下部に合計の合計
  // contents.push(sums);
} // window.on.load

function initilazing(type, display, start, end){
  $('#result').empty();
  $('#result').append('<div class="text-center"><i class="fas fa-spinner fa-spin fa-3x"></i></div>')
  // ajaxでデータを取得して、成功したらcreateRowでデータを作成する
  console.log(type, display, start, end);
  $.ajax({
    type: 'GET',
    url: '/analysis/contactdetail/ajaxGet',
    data: { type: type, start: start, end: end, display: display },
    dataType: 'json'
  }).done(function (data) {
    console.log(data);
    var contents = [];
    var heading = returnHeading(type)
    var header = [ 
      {name:heading, type:"string", align: "center", width: '120px'}, 
    ];
    if( window.queryString.display === 'year' || window.queryString.display === 'year6'){
      header.push.apply( header, addYear(window.queryString.startyear, window.queryString.endyear));
    }else{
      header.push.apply( header, addMonth(window.data['start'], window.data['period']) );
    }
    console.log(data.contacts);
    Object.entries(data.contacts).forEach(([key, value]) => {
      contents.push(createRow(key, value, null, heading));
    });
    $("#result").jsGrid({
        width: 'auto',
        height: "60vh",
        sorting: true,
        heading: true,
        data: contents,
        fields: header,
        shrinkToFit:false ,
    });
  }).fail(function (data) {
    alert('データの取得に失敗しました。');
    console.log(data);
  });
}

function addMonth(start, length){
  console.log(window.queryString);
  results = [];
  var startMonth = dayjs(start);
  results.push({name:startMonth.format('YY年MM月') + (window.queryString.type == "2" ? "<br/>依頼" : ""), type: "number", align: "center", width: "42"});
  for (let i = 1; i < length; i++) {
    const string = startMonth.add(i, 'month').format('YY年MM月');
    results.push({name: string, type: "text", align: "center", width: "42",});
  }
  return results;
}

function addYear(start, end){
  results = [];
  for (let i = parseInt(start); i <= parseInt(end); i++) {
    results.push( {name: i + '年', type: "text", align: "center", width: "80"});
  }
  return results;
}

function createRow(heading = "", values = [], sums = {}, headerHeading = ""){
  var total = 0
  var row = {};
  var mainHeading = returnHeading(window.queryString.type);
  row[headerHeading] = heading;
  console.log(values);
  values.forEach((r, index, array) => {
    const year = (window.queryString.display === 'year' || window.queryString.display === 'year6') ? array[index].year : array[index].month.slice(2,4);
    const month = window.queryString.display === 'yearmonth' ? array[index].month.slice(5) : '';
    if( window.queryString.display === 'year' || window.queryString.display === 'year6'){
      row[year+'年'] = r.count;
      // sums[year+'年'] += r.count;
    }else{
      // console.log(row[year+'年' + month + '月']);
      // if( row[year+'年' + month + '月'] ))
      row[year+'年' + month + '月'] = r.count;
      // sums[year+'年' + month + '月'] += r.count;
    }
    row["type"] = "checkbox";
    // 行の色出し分け用（user_statusはレンダリングしない）
    total += r.count;
    // 最下部の合計行
    // sums['合計'Float32Array;
  });
  // row[`合計`] = total === 0 ? null : total;

  return row;
}

function returnHeading(type = 'contact_detail_ages'){
  switch (type) {
    case 'contact_detail_ages':
      return '年代' 
      break;
    case 'contact_detail_turf_purpose':
      return '使用目的' 
      break;
    case 'contact_detail_where_find':
      return '見つけたきっかけ' 
      break;
    case 'contact_detail_sns':
      return '利用中のSNS'
      break;
    default:
      return '年代' 
      break;
  }
}

$(document).on('change', 'input[name="type"], input[name="display"]', function() {
  const q = getQueryString();
  console.log(q);
  initilazing(q.type, q.display, q.start, q.end);
})

$(document).on('change', 'input[name="display"]', function() {
  const selectedType = $(this).val();
  if( selectedType==='yearmonth'){
    $('#start-year').addClass('d-none');
    $('#end-year').addClass('d-none');
    $('#start-yearmonth').removeClass('d-none');
    $('#end-yearmonth').removeClass('d-none');
  }else{
    $('#start-year').removeClass('d-none');
    $('#end-year').removeClass('d-none');
    $('#start-yearmonth').addClass('d-none');
    $('#end-yearmonth').addClass('d-none');
  }
  initilazing(getQueryString());
})

function getQueryString(){
  var params = {};
  params.type = $('input[name="type"]:checked').val();
  params.display = $('input[name="display"]:checked').val();
  params.start = $('#start-yearmonth').val();
  params.end = $('#end-yearmonth').val();
  params.startyear = $('#start-year').val();
  params.endyear =  $('#end-year').val();

  window.queryString = params;
  console.log(params);

  return params;
}

function exampleArray(){
  return [
    {age: '1920', count: 1, month: '2022-01'},
    {age: '1920', count: 1, month: '2022-02'},
    {age: '1920', count: 1, month: '2022-03'},
    {age: '1920', count: 1, month: '2022-04'},
    {age: '1920', count: 1, month: '2022-05'},
    {age: '1920', count: 1, month: '2022-06'},
    {age: '1920', count: 1, month: '2022-07'},
    {age: '1920', count: 1, month: '2022-08'},
    {age: '1920', count: 1, month: '2022-09'},
    {age: '1920', count: 1, month: '2022-10'},
    {age: '1920', count: 1, month: '2022-11'},
    {age: '1920', count: 1, month: '2022-12'},
    {age: '1930', count: 3, month: '2022-02'},
    {age: '1930', count: 3, month: '2022-03'},
    {age: '1930', count: 3, month: '2022-04'},
    {age: '1930', count: 3, month: '2022-05'},
    {age: '1940', count: 4, month: '2022-06'},
    {age: '1960', count: 8, month: '2022-07'},
    {age: '1960', count: 8, month: '2022-08'},
    {age: '1960', count: 8, month: '2022-09'},
    {age: '1960', count: 8, month: '2022-10'},
    {age: '1960', count: 8, month: '2022-11'}
  ]
}