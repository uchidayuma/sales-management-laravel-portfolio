$(window).on('load', function() {
  // Load the Visualization API and the corechart package.
  // Callback that creates and populates a data table,
  // instantiates the pie chart, passes in the data and
  // draws it.
  // initialize();
  console.log('test');
  google.charts.load('current', {'packages':['corechart', 'bar']});

  // Set a callback to run when the Google Visualization API is loaded.
  google.charts.setOnLoadCallback(initialize);
})



function initialize(type = 'city-price', start = null, end = null) {
  // 顧客を期間で絞り込み
  const customers = filterCustomers(window.customers, start, end)
  var dataArray = [];
  var data;
  var options = {};
  const series =  {
    0:{color:'#53BF9D'},
    1:{color:'#F94C66'},
    2:{color:'#BD4291'},
    3:{color:'#FFC54D'},
    4:{color:'#FFFFDE'},
    5:{color:'#FF7396'},
    6:{color:'#C499BA'},
    7:{color:'#06283D'},
    8:{color:'#34B3F1'},
    9:{color:'#4B5D67'},
    10:{color:'#243A73'},
    11:{color:'#C70A80'},
  }
  switch (type) {
    case 'city-price':
      dataArray = cityPrice(customers);
      console.log(dataArray);
      dataArray['prices'].unshift('市町村');
      dataArray['values'].unshift(dataArray['prices']);
      data = google.visualization.arrayToDataTable(dataArray['values']);
      var options = {
        chart: { title: '   市町村・価格帯グラフ', subtitle: '   市町村別成約金額の傾向' },
        bars: 'vertical',
        legend: { position: 'left', alignment: 'center', top: 20 },
        axes: {
          y: {
            0: { side: 'top', label: '件数'}
          }
        },
        vAxis: {format: '#', showTextEvery:1 },
        width: dataArray['values'].length * 100,
        height: 800,
        chartArea: { left:20,top:0, width: "80%", height: "80%" },
        isStacked: true,
      };
      options.series = series;
      break;
    case 'city-find':
      dataArray = cityFind(customers);
      console.log(dataArray);
      dataArray['cities'].unshift('市町村');
      dataArray['labels'].unshift('認知経路');
      dataArray['values'].unshift(dataArray['labels']);
      data = google.visualization.arrayToDataTable(dataArray['values']);
      var options = {
        chart: { title: '   市町村・認知経路グラフ',subtitle: '   市町村別認知経路の傾向' },
        bars: 'vertical',
        legend: { position: 'left', alignment: 'center', top: 20 },
        axes: {
          y: {
            0: { side: 'top', label: '問い合わせ件数'}
          }
        },
        // hAxis: {format: 'none', showTextEvery:1 },
        vAxis: {format: '#', showTextEvery:1 },
        width: dataArray['values'].length * 100,
        height: 800,
        chartArea: { left:20,top:0, width: "80%", height: "80%" },
        isStacked: true,
      };
      options.series = series;
    default:
      break;
  }

  var chart = new google.charts.Bar(document.getElementById('stage'));

  chart.draw(data, google.charts.Bar.convertOptions(options));

  /*
  var resultsX = []
  var resultsY = []
  var labels = [];
  var data = [];
  switch (x) {
    case 'city':
      resultsX = cityContacts(customers);
      break;
    case 'age':
      resultsX = ageContacts(customers);
      break;
    case 'cognitive':
      resultsX = cognitiveContacts(customers);
      break;
    case 'turf':
      resultsX = turfContacts(customers);
      break;
    case 'price':
      resultsX = priceContacts(customers);
      break;
  
    default:
      break;
  }
  switch (y) {
    case 'contacts':
      resultsY = cityContacts(customers);
      break;
    case 'age':
      resultsY = ageContacts(customers);
      break;
    case 'cognitive':
      resultsY = cognitiveContacts(customers);
      break;
    case 'turf':
      resultsY = turfContacts(customers);
      break;
    case 'price':
      resultsY = priceContacts(customers);
      break;
  
    default:
      break;
  }
  // デフォルトは市町村と問い合わせ件数の棒グラフ
  const barArea = document.getElementById('bar');
  const lineArea = document.getElementById('line');
  const area = document.getElementById('canvases');
  const outerArea = document.getElementById('article');
  const form = document.getElementById('data-form');

  barArea.width = area.clientWidth / 2;
  barArea.height = outerArea.clientHeight - form.clientHeight - 50;
  lineArea.width = area.clientWidth / 2;
  lineArea.height = outerArea.clientHeight - form.clientHeight - 50;
  const barChart = new Chart(barArea, {
      type: 'bar',
      data: {
          labels: resultsX['labels'],
          datasets: [{
              // label: '# of Votes',
              label: '棒グラフのデータセット',
              data: resultsY['values'],
              backgroundColor: [
                  'rgba(255, 99, 132, 0.2)',
                  'rgba(54, 162, 235, 0.2)',
                  'rgba(255, 206, 86, 0.2)',
                  'rgba(75, 192, 192, 0.2)',
                  'rgba(153, 102, 255, 0.2)',
                  'rgba(255, 159, 64, 0.2)'
              ],
              borderColor: [
                  'rgba(255, 99, 132, 1)',
                  'rgba(54, 162, 235, 1)',
                  'rgba(255, 206, 86, 1)',
                  'rgba(75, 192, 192, 1)',
                  'rgba(153, 102, 255, 1)',
                  'rgba(255, 159, 64, 1)'
              ],
              borderWidth: 1
            
            },
            {
              label: '折れ線グラフのデータセット',
              data: resultsY['values'],
              type: 'line',
              borderColor: [
                  'rgba(54, 162, 235, 1)',
                  'rgba(54, 162, 235, 1)',
                  'rgba(54, 162, 235, 1)',
                  'rgba(54, 162, 235, 1)',
                  'rgba(54, 162, 235, 1)',
                  'rgba(54, 162, 235, 1)',
              ],
            }
          ]
      },
      options: {
          scales: {
              y: {
                  beginAtZero: true
              }
          }
      }
  })
  const lineChart = new Chart(lineArea, {
      type: 'line',
      data: {
          labels: resultsX['labels'],
          datasets: [{
              data: resultsY['values'],
              borderWidth: 1
          }]
      },
      options: {
          scales: {
              y: {
                  beginAtZero: true
              }
          }
      }
  })
  */
}

// パネルの内容が変わったら
$('form').on('change', function(){
  console.log('change');
  const type = $('input:radio[name="type"]:checked').val();
  const start = $('input[name="start"]').val();
  const end = $('input[name="end"]').val();
  console.log(type,start,end);
  initialize(type, start, end);
})
