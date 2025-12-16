window.onload = function () {
  $('.select-fc').select2({
    placeholder: "FCを絞り込み（複数可）",
    allowClear: true
  });
  $('#select-pref').select2({
    placeholder: "都道府県を絞り込み（複数可）",
    allowClear: true
  });
  $('#filtering-form').removeClass("d-none");
  $('input[type=month]').MonthPicker({ Button: false });
  var header = [ 
    {name:"エリア", type:"text", align: "center", width: '80px'}, 
    {name:"社名", type:"text", align: "center", width: '170px'}
  ];
    console.log(window.data);
  // 年月か年かを分岐
  if( window.queryString.display === 'year'){
    header.push.apply( header, addYear(window.queryString.startyear, window.queryString.endyear));
  }else{
    header.push.apply( header, addMonth(window.data['start'], window.data['period']) );
  }
  if( window.queryString.type === '2' ){
    header.push.apply( header, [ {name: `依頼<br/>合計`, type: "number", align: "center", width: "60px"} ]);
    header.push.apply( header, [ {name: `自己<br/>合計`, type: "number", align: "center", width: "60px"} ]);
    header.push.apply( header, [ {name: '依頼<br/>受注率', type: "number", align: "center", width: "60px"}] );
    header.push.apply( header, [ {name: '自己<br/>受注率', type: "number", align: "center", width: "60px"}] );
  }else{
  header.push.apply( header, [ {name: `合計`, type: "number", align: "center", width: "60px"} ]);
  }
  var contents = [];
  var prevUserId = 0;
  // 各列の合計行を先に作る
  contents.push( window.queryString.type === '2' ? createTransactionSumRow(window.data.users.sums) : createSumRow(window.data.users.sums));
  Object.keys(window.data.users).forEach(userId => {
    if( window.data.users[userId].user_status != 2 && userId != 'sums' && window.queryString.type != '2'){
      contents.push(createRow(window.data.users[userId], prevUserId));
      prevUserId = userId;
    }else if(window.data.users[userId].user_status != 2 && userId != 'sums' && window.queryString.type === '2'){
      contents.push(createTransactionRow(window.data.users[userId], prevUserId));
      prevUserId = userId;
    }
  });
  // 退会済みFCは下にまとめて出す
  Object.keys(window.data.users).forEach(userId => {
    if( window.data.users[userId].user_status === 2 && userId != 'sums' && window.queryString.type === '2'){
      contents.push(createTransactionRow(window.data.users[userId], prevUserId));
      prevUserId = userId;
    }else if(window.data.users[userId].user_status === 2 && userId != 'sums'){
      contents.push(createRow(window.data.users[userId], prevUserId));
      prevUserId = userId;
    }
  });
  // 合計行の受注率
  contents[0]['受注率'] = Math.round(roundFloatNumber( window.totalTransactionRate / (Object.keys(window.data.users).length - 2), 3) * 1) + '%';
  $("#result").jsGrid({
      width: 'auto',
      height: "60vh",
      sorting: false,
      heading: true,
      data: contents,
      fields: header,
      // FCステータスごとにクラスを出し分け
      rowClass: function (item, itemIndex){
        switch (item.user_status) {
          case 1:
            return 'active';
          case 2:
            return 'inactive d-none';
          case 3:
            return 'traning';
          case 4:
            return 'pause';
        }
      },
  });
  $('[data-toggle="tooltip"]').tooltip()
} // window.on.load

function addMonth(start, length){
  console.log(window.queryString);
  results = [];
  var startMonth = dayjs(start);
  results.push({name:startMonth.format('YY年MM月') + (window.queryString.type == "2" ? "<br/>依頼" : ""), type: "number", align: "center", width: "42"});
  if( window.queryString.type == "2"){
    results.push({name:startMonth.format('YY年MM月') + "<br/>自己", type: "number", align: "center", width: "42"});
  }
  // results.push({name:startMonth.format('YY年MM月') + '依頼', type: "number", align: "center", width: "42"});
  for (let i = 1; i < length; i++) {
      const string = startMonth.add(i, 'month').format('YY年MM月') + (window.queryString.type == "2" ? "<br/>依頼" : "");
      results.push({name: string, type: "number", align: "center", width: "42",});
      if( window.queryString.type == "2"){
        results.push({name:startMonth.add(i, 'month').format('YY年MM月') + "<br/>自己", type: "number", align: "center", width: "42"});
        // results.push({name: string, type: "number", align: "center", width: "45"});
      }
  }
  return results;
}

function addYear(start, end){
  results = [];
  for (let i = parseInt(start); i <= parseInt(end); i++) {
    if(window.queryString.type === '2'){
      results.push( {name: i + '年依頼', type: "string", align: "center", width: "80"});
      results.push( {name: i + '年自己', type: "string", align: "center", width: "80"});
    }else{
      results.push( {name: i + '年', type: "string", align: "center", width: "80"});
    }
  }
  return results;
}

function createRow(userData = [], prevUserId=0){
  var total = 0
  var row = {};
  // 同じエリア名は出さない
  console.log(userData);
  if( prevUserId === 0){
    row["エリア"] = userData.prefecture_name;
  } else if( userData.user_status === 4){
    row["エリア"] = "休止";
  } else if( userData.user_status === 3){
    row["エリア"] = "研修中";
  } else if( userData.user_status === 2){
    row["エリア"] = "退会";
  } else if( window.data.users[prevUserId].prefecture_name === userData.prefecture_name){
    row["エリア"] = "";
  }else{
    row["エリア"] = userData.prefecture_name;
  }
  row["社名"] = userData.company_name;
  row["user_status"] = userData.user_status;
  Object.keys(userData.counts).forEach((count, index, array) => {
    // 年月か年かを分岐
    const year = window.queryString.display === 'year' ? array[index] : array[index].slice(2,4);
    var month = array[index].slice(5);
    if( window.queryString.display === 'year'){
      row[year+'年'] = userData.counts[count];
    }else{
      row[year+'年' + month + '月'] = userData.counts[count];
    }
    row["type"] = "checkbox";
    // 行の色出し分け用（user_statusはレンダリングしない）
    total += parseInt(userData.counts[count]);
  });
  row[`合計`] = total === 0 ? null : total;
  // row['自己獲得数'] = userData.own_contacts;

  return row;
}

function createTransactionRow(userData = [], prevUserId=0){
  console.log(userData);
  var row = {};
  // 同じエリア名は出さない
  if( prevUserId === 0){
    row["エリア"] = userData.prefecture_name;
  } else if( userData.user_status === 4){
    row["エリア"] = "休止";
  } else if( userData.user_status === 3){
    row["エリア"] = "研修中";
  } else if( userData.user_status === 2){
    row["エリア"] = "退会";
  } else if( window.data.users[prevUserId].prefecture_name === userData.prefecture_name){
    row["エリア"] = "";
  }else{
    row["エリア"] = userData.prefecture_name;
  }
  row["社名"] = userData.company_name;
  row["user_status"] = userData.user_status;
  // もしデータがないFCならば空行を返す
  if( !dig(userData, 'counts')){ return row; }
  if( userData.counts.introduce.length < 1 && userData.counts.myself.length < 1){ return row; }
  var introduceTotal = 0
  Object.keys(userData.counts.introduce).forEach((count, index, array) => {
    const year = window.queryString.display === 'year' ? array[index] : array[index].slice(2,4);
    var month = array[index].slice(5);
    if( window.queryString.display === 'year'){
      row[year+'年依頼'] = userData.counts['introduce'][count];
    }else{
      row[year+'年' + month + '月<br/>依頼'] = userData.counts['introduce'][count];
    }
    introduceTotal += !isNaN(parseInt(userData.counts['introduce'][count])) ? parseInt(userData.counts['introduce'][count]) : 0;
    console.log(introduceTotal);
  });
  // 自己獲得案件がないFCがもいるため分岐
  if( dig(userData.counts.myself)){
    var myselfTotal = 0
    Object.keys(userData.counts.myself).forEach((count, index, array) => {
      // 年月か年かを分岐
      const year = window.queryString.display === 'year' ? array[index] : array[index].slice(2,4);
      var month = array[index].slice(5);
      if( window.queryString.display === 'year'){
        row[year+'年自己'] = userData.counts['myself'][count];
      }else{
        row[year+'年' + month + '月<br/>自己'] = userData.counts['myself'][count];
      }
      myselfTotal += !isNaN(parseInt(userData.counts['myself'][count])) ? parseInt(userData.counts['myself'][count]) : 0;
    });
  }
  row[`依頼<br/>合計`] = introduceTotal;
  row[`自己<br/>合計`] = myselfTotal;
  // row['自己獲得数'] = userData.own_contacts;
  if( window.queryString.type === '2' ){
    if( userData.total_contacts === null){
      row['依頼<br/>受注率'] = '算出<br/>不可';
      row['自己<br/>受注率'] = '算出<br/>不可';
    }else{
      // 本部だけ依頼受注率を 商品発送まで行った案件 / トータルの本部案件で算出
      if( userData.user_id === 1){
        row['依頼<br/>受注率'] = Math.round(roundFloatNumber( userData.success_contacts / (userData.total_contacts ), 3) * 100) + '%';
      }else{
        row['依頼<br/>受注率'] = userData != 0 || introduceTotal != 0 ? Math.round(roundFloatNumber( introduceTotal / (userData.total_contacts ), 3) * 100) + '%' : 0 + '%';
      }
      if( userData.own_contacts === null || userData.myself_only_transaction_count === null){
        row['自己<br/>受注率'] = '算出<br/>不可';
      }else{
        row['自己<br/>受注率'] = userData != 0 || userData.own_contacts != null || (userData.myself_only_transaction_count != 0 && userData.own_contacts != 0) ? Math.round(roundFloatNumber( userData.myself_only_transaction_count / (userData.own_contacts ), 3) * 100) + '%' : 0 + '%';
      }
    }
  }
  return row;
}

function createSumRow(data = []){
  var row = {};
  row["エリア"] = '';
  row["社名"] = '合計';
  Object.keys(data).forEach((count, index, array) => {
    // 年月か年かを分岐
    const year = window.queryString.display === 'year' ? array[index] : array[index].slice(2,4);
    var month = array[index].slice(5);
    // month = month == 10 ? 10 : month.replace('0', '');
    if( window.queryString.display === 'year'){
      row[year+'年'] = data[count];
    }else{
      row[year+'年' + month + '月'] = data[count];
    }
    row["type"] = "checkbox";
  });
  row[`合計`] = data['total'];
  return row;
}

function createTransactionSumRow(data = []){
  console.log(data);
  var row = {};
  row["エリア"] = '';
  row["社名"] = '合計';
  Object.keys(data.introduces).forEach((count, index, array) => {
    const year = window.queryString.display === 'year' ? array[index] : array[index].slice(2,4);
    var month = array[index].slice(5);
    if( window.queryString.display === 'year'){
      row[year+'年依頼'] = data['introduces'][count];
    }else{
      row[year+'年' + month + '月<br/>依頼'] = data['introduces'][count];
    }
  });
  if( dig(data.myselfs) ){
    Object.keys(data.myselfs).forEach((count, index, array) => {
      const year = window.queryString.display === 'year' ? array[index] : array[index].slice(2,4);
      var month = array[index].slice(5);
      if( window.queryString.display === 'year'){
        row[year+'年自己'] = data['myselfs'][count];
      }else{
        row[year+'年' + month + '月<br/>自己'] = data['myselfs'][count];
      }
    });
  }
  row[`依頼<br/>合計`] = parseInt(data['introduce']);
  row[`自己<br/>合計`] = parseInt(data['myself']);

  return row;
}

$(document).on('change', 'input[name="display"]', function() {
  const selectedType = $(this).val();
  if( selectedType==='yearmonth' ){
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

$(document).on('click', '#toggle-hidden-fc', function () {
  $('.inactive').toggleClass('d-none'); 
  $(this).text('退会済みFCを隠す')
})