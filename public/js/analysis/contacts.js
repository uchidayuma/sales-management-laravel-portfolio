window.onload = function () {
  $('#filtering-form').removeClass("d-none");
  $('input[type=month]').MonthPicker({ Button: false });
  var header = [ 
    {name:"エリア", type:"string", align: "center", width: '80px'}, 
  ];
    console.log(window.data);
  // sums=最下部の合計行
  var sums = {};
  var tmpSums = [];
  // 年月か年かを分岐
  if( window.queryString.display === 'year' || window.queryString.display === 'year6'){
    header.push.apply( header, addYear(window.queryString.startyear, window.queryString.endyear));
    tmpSums.push.apply( tmpSums, addYear(window.queryString.startyear, window.queryString.endyear));
  }else{
    header.push.apply( header, addMonth(window.data['start'], window.data['period']) );
    tmpSums.push.apply( tmpSums, addMonth(window.data['start'], window.data['period']) );
  }
  header.push.apply( header, [ {name: `合計`, type: "number", align: "center", width: "60px"} ]);
  console.log(header);
  // 最下部の合計行の箱だけ先に作っておく
  sums['エリア'] = '合計';
  tmpSums.forEach(ts => {
    sums[ts.name] = 0;
  })
  sums['合計'] = 0;
  var contents = [];
  window.data.prefectures.forEach(p => {
    contents.push(createRow(p, sums));
  })
  console.log(contents);
  // 最下部に合計の合計
  contents.push(sums);
  $("#result").jsGrid({
      width: 'auto',
      height: "60vh",
      sorting: true,
      heading: true,
      data: contents,
      fields: header,
      shrinkToFit:false ,
  });
} // window.on.load

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

function createRow(pref = "", sums = {}){
  var total = 0
  var row = {};
  row["エリア"] = pref;
  const results = window.data.contacts.filter(function(c) {
    return c.pref == pref;
  });
  results.forEach((r, index, array) => {
    const year = (window.queryString.display === 'year' || window.queryString.display === 'year6') ? array[index].year : array[index].month.slice(2,4);
    const month = window.queryString.display === 'yearmonth' ? array[index].month.slice(5) : '';
    if( window.queryString.display === 'year' || window.queryString.display === 'year6'){
      row[year+'年'] = r.contact_count;
      sums[year+'年'] += r.contact_count;
    }else{
      row[year+'年' + month + '月'] = r.contact_count;
      sums[year+'年' + month + '月'] += r.contact_count;
    }
    row["type"] = "checkbox";
    // 行の色出し分け用（user_statusはレンダリングしない）
    total += r.contact_count;
    // 最下部の合計行
    sums['合計'] += r.contact_count;
  });
  row[`合計`] = total === 0 ? null : total;

  return row;
}

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
})