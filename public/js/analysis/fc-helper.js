function switchData(){
  const xSelectedId = $('input:radio[name="x"]:checked').attr('id');
  const nextYselectedId = xSelectedId.replace('x-', 'y-')
  const ySelectedId = $('input:radio[name="y"]:checked').attr('id');
  const nextXselectedId = ySelectedId.replace('y-', 'x-')
  $("input[name='x']").attr("checked", false);
  $("input[name='y']").attr("checked", false);
  $(`#${nextXselectedId}`).attr("checked", true);
  $(`#${nextYselectedId}`).attr("checked", true);
  const start = $('input[name="start"]').val();
  const end = $('input[name="end"]').val();
  const xSelected = $('input:radio[name="x"]:checked').val();
  const ySelected = $('input:radio[name="y"]:checked').val();
  initialize(xSelected, ySelected, start, end);
}

function filterCustomers( customers = [], start = null, end = null ){
  if(!start && !end){
    return customers;
  }
  const startMonth = dayjs(start);
  const endMonth = dayjs(end);

  var array = [];
  customers.forEach( c => {
    // 両方入力されていたらBetween
    if(start && end){
      if(startMonth.isBefore(dayjs(c.created_at)) && endMonth.isAfter(dayjs(c.created_at))){
        array.push(c);
      }
    }else{
      if(startMonth.isBefore(dayjs(c.created_at)) || endMonth.isAfter(dayjs(c.created_at))){
        array.push(c);
      }
    }
  })

  return array;
}

function cityPrice( customers = []){
  customers = customers.filter(customer => customer.sub_total != null);
  var values = [];
  var array = [];
  array.labels = [];
  array.values = [];
  // 5万円刻みで最低価格〜最高価格まで配列化
  // const prices = lowAndHighPrice(customers);
  // const prices = [100000,200000,300000,400000,500000,600000,99999999];
  const prices = [200000,300000,400000,500000,600000,700000,800000,900000,1000000,99999999];
  customers.forEach( item => {
    // 重複しなけばpush
    if (array.labels.indexOf(item.city) == -1) {
      array.labels.push(item.city);
      // ラベルの分だけ配列作成
      array.values[array.values.length] = [];
      array.values[array.values.length - 1].push(item.city);
    }
  });
  array.values.forEach( v => {
    // 市町村の分だけループ
    for (let i = 0; i < array.values.length; i++) {
      // 市町村別に値が入るキーを作成
      for (let p = 1; p <= prices.length; p++) {
        array.values[i][p] = 0;
        array.values[i][p+1] = 'aaaaa';
      }
    }
  })
  // ラベル別に集計
  customers.forEach( item => {
    const cityIndex = array.values.findIndex((labels) => {
      return labels.some((l) => {
        return l === item.city;
      });
    });
    const insertIndex = priceAllocationIndex(prices, item, array['values']);
    array.values[cityIndex][insertIndex] += 1;
  });

  // dataの整形
  // array['prices'] = ['10万円以下','～10万円台','～20万円台','～30万円台','～40万円台','～50万円台','60万円以上'];
  array['prices'] = ['20万円以下','～20万円台','～30万円台','～40万円台','～50万円台','～60万円台','〜70万円台','〜80万円台','〜90万円台','100万円以上', { role: 'annotation' }];
  // for(var i=0; i<prices.length; i++){
  //   array['prices'][i] = '〜' + prices[i].toString();
  // }
  Object.keys(values).map( key => array.values.push(values[key]));

  return array

}

function cityFind( customers = []){
  var array = [];
  array.cities = [];
  array.labels = [];
  array.values = [];
  customers.forEach( item => {
    // 市町村の整形
    if (array.cities.indexOf(item.city) == -1) {
      array.cities.push(item.city);
      // 市町村の分だけ配列作成
      array.values[array.values.length] = [];
      array.values[array.values.length - 1].push(item.city);
    }
    // 認知経路の整形
    const itemFinds = !item.where_find ? [] : item.where_find.split(',');
    itemFinds.forEach( itemFind => {
      // 重複しなけばpush
      if (array.labels.indexOf(itemFind) == -1 && itemFind != null && itemFind != '') {
        // カンマを排除
        const find = itemFind.replace(',','');
        array.labels.push(find);
      }
    });
  });
  array.values.forEach( (v, index) => {
    // 認知経路の分だけループ
    for (let p = 1; p <= array.labels.length; p++) {
      array.values[index][p] = 0;
    }
  })
  // ラベル別に集計(複数対応)
  customers.forEach( c => {
    if(c.where_find != '' && c.where_find != null ){
      // cityIndex == 集計する市町村のindex
      const cityIndex = array.cities.findIndex((city) => city === c.city );
      // 認知経路を分解
      const finds = c.where_find.split(',');
      // 認知経路ごとに集計
      finds.forEach( f => {
        if(f != '' && f != null ){
          // 発見経路が配列の行くつ目になるか調べる
          const insertIndex = array.labels.findIndex((label) => label.indexOf(f) != -1 );
          array.values[cityIndex][insertIndex] += 1;
        }
      });
    }
  });

  return array

}

// 市町村別に価格帯から振り分ける
function priceAllocationIndex(prices = [], item = {}, array = [] ){
  var insertIndex = 1;
  prices.forEach( (value, index) =>{
    if(index === 0 && item.sub_total < value){
      insertIndex = 1;
    }
    if(index != 0 && prices[index-1] < item.sub_total && item.sub_total < value){
      insertIndex = index+1;
    }
  })
  return insertIndex;
}

function cityContacts( customers = []){
  var values = [];
  var array = [];
  array.labels = [];
  array.values = [];
  customers.forEach( item => {
    // 重複しなけばpush
    if (array.labels.indexOf(item.city) == -1) {
      array.labels.push(item.city)
      if(values[item.city] === undefined){
        values[item.city] = 1;
      }
    }else{
      // 既にlabelが存在すれば数を+1
      if(values[item.city] != 'NaN'){
        values[item.city] += 1;
      }
    }
  });
  // dataの整形
  Object.keys(values).map( key => array.values.push(values[key]));

  return array
}

/*
function lowAndHighPrice(customers = []){
  if(customers.length < 1){
    return [];
  }
  var prices = [];
  var low = customers[0]['sub_total'];
  var high = customers[0]['sub_total'];
  customers.forEach( item => {
    console.log(item['sub_total']);
    if(item['sub_total'] < low){
      low = item['sub_total'];
    }
    if(item['sub_total'] > high){
      high = item['sub_total'];
    }
  });
  const length = Math.floor((high - low) / 50000) + 1;
  const startPrice = Math.floor((low / 50000)) * 50000;
  console.log(low, high, length, startPrice);
  for(var i=0; i<=length; i++){
    prices[i] = startPrice * (i+1);
  }

  return prices;
}

function ageContacts( customers = []){
  var values = [];
  var array = [];
  array.labels = [];
  array.values = [];
  customers.forEach( item => {
    // 重複しなけばpush
    if (array.labels.indexOf(item.age) == -1) {
      array.labels.push(item.age)
      if(values[item.age] === undefined){
        values[item.age] = 1;
      }
    }else{
      // 既にlabelが存在すれば数を+1
      if(values[item.age] != 'NaN'){
        values[item.age] += 1;
      }
    }
  });
  // dataの整形
  Object.keys(values).map( key => array.values.push(values[key]));

  return array
}

function turfContacts( customers = []){
  var values = [];
  var array = [];
  array.labels = [];
  array.values = [];
  console.log(customers);
  // 成約した案件だけに絞る
  customers.forEach( item => {
    // 重複しなけばpush
    if (array.labels.indexOf(item.product_name) == -1) {
      array.labels.push(item.product_name)
      if(item.step_id > 5){
        array.labels.push(item.product_name);
        values[item.product_name] = 1;
      }
    }else{
      // 既にlabelが存在すれば数を+1
      if(values[item.product_name] != 'NaN'){
        values[item.product_name] += 1;
      }
    }
    // TODO優先順位付け
  });
  console.log(array);
  // dataの整形
  Object.keys(values).map( key => array.values.push(values[key]));

  return array
}

function cognitiveContacts( customers = []){
  var values = [];
  var array = [];
  array.labels = [];
  array.values = [];
  console.log(customers);
  // 成約した案件だけに絞る
  customers.forEach( item => {
    // 重複しなけばpush
    if (array.labels.indexOf(item.product_name) == -1) {
      array.labels.push(item.product_name)
      if(item.step_id > 5){
        array.labels.push(item.product_name);
        values[item.product_name] = 1;
      }
    }else{
      // 既にlabelが存在すれば数を+1
      if(values[item.product_name] != 'NaN'){
        values[item.product_name] += 1;
      }
    }
    // TODO優先順位付け
  });
  console.log(array);
  // dataの整形
  Object.keys(values).map( key => array.values.push(values[key]));

  return array
}

function priceContacts( customers = []){
  var values = [];
  var array = [];
  array.labels = [];
  array.values = [];
  console.log(customers);
  // 成約した案件だけに絞る
  customers.forEach( item => {
    // 重複しなけばpush
    if (array.labels.indexOf(item.product_name) == -1) {
      array.labels.push(item.product_name)
      if(item.step_id > 5){
        array.labels.push(item.product_name);
        values[item.product_name] = 1;
      }
    }else{
      // 既にlabelが存在すれば数を+1
      if(values[item.product_name] != 'NaN'){
        values[item.product_name] += 1;
      }
    }
    // TODO優先順位付け
  });
  console.log(array);
  // dataの整形
  Object.keys(values).map( key => array.values.push(values[key]));

  return array
}
*/
