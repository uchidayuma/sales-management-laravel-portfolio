(window.onload = function () {
  // function initialize() {
  var map;
  var bounds = new google.maps.LatLngBounds();
  var mapOptions = {
    mapTypeId: 'roadmap'
  };

  // Display a map on the page
  map = new google.maps.Map(document.getElementById("map_tuts"), mapOptions);
  map.setTilt(45);

  // Multiple Markers
  var markers = [];
  var infoWindowContents = [];
  var fcs = window.franchises;
  switch (window.distance) {
    case 50:
      var zoom = 12;
      break;
    case 100:
      var zoom = 10;
      break;
    case 200:
      var zoom = 9;
      break;
    case 300:
      var zoom = 8;
      break;
    case 3500:
      var zoom = 5;
      break;
    default:
      var zoom = 10;
      break;
  }

  for (i = 0; i < Object.keys(fcs).length; i++) {
    var fc = [fcs[i].company_name, fcs[i].latitude, fcs[i].longitude, fcs[i]['id']];
    var content =
      ['<div class="info_content">' +
        `<h3 class="info_content__title mb20">${fcs[i].company_name}</h3>` +
        `<p class="info_content__para">${fcs[i].pref + fcs[i].city + fcs[i].street}</p>` +
        `<p class="info_content__para">顧客住所からの距離${fcs[i].distance.toFixed(1)}km</p>` +
        `<p class="info_content__para">年間施工件数${fcs[i]['year_count']}件</p>` +
        `<p class="info_content__para">現在施工件数${fcs[i]['progress_count']}件</p>` +
        '</div>'
      ];
    markers.push(fc);
    infoWindowContents.push(content);
  }

  // Display multiple markers on a map
  var infoWindow = new google.maps.InfoWindow(), marker, i;

  // Loop through our array of markers &amp; place each one on the map  
  for (i = 0; i < markers.length; i++) {
    var position = new google.maps.LatLng(markers[i][1], markers[i][2]);
    bounds.extend(position);
    var marker = new MarkerWithLabel({
      position: position,
      map: map,
      fcid: markers[i][3],
      title: markers[i][0],
      // icon: `http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=''|7ca2d6|7ca2d6`,
      icon: {
        url: `/images/map-icons/numbericon_blue_${i + 1}.png`,
        scaledSize: new google.maps.Size(30, 30)
      },
      labelContent: markers[i][0],                   //ラベル文字
      labelAnchor: new google.maps.Point(38, 10),   //ラベル文字の基点
      labelClass: 'fc-labels',                        //CSSのクラス名
    });

    // Each marker to have an info window    
    google.maps.event.addListener(marker, 'click', (function (marker, i) {
      return function () {
        infoWindow.setContent(infoWindowContents[i][0]);
        infoWindow.open(map, marker);
        console.log('fcid');
        console.log(marker.fcid);
        $(`#${marker.fcid}`).click();
      }
    })(marker, i));

    // Automatically center the map fitting all markers on the screen
    map.fitBounds(bounds);
  }

  // 顧客の住所
  const client = new google.maps.LatLng(window.case.latitude, window.case.longitude);
  mainMarker = new google.maps.Marker({
    position: client,
    map: map,
    title: '顧客住所'
  });
  mainInfoWindow = new google.maps.InfoWindow({ // 吹き出しの追加
    content:
      '<div class="info_content">' +
      `<h3 class="info_content__title mb20">顧客情報</h3>` +
      `<p class="info_content__para">${window.case.pref + window.case.city + window.case.street}</p>` +
      `<p class="info_content__para">${window.case.company_name ? window.case.company_name : '' + ' ' + window.case.surname + window.case.name}</p>` +
      '</div>'

  });
  mainMarker.addListener('click', function () { // マーカーをクリックしたとき
    mainInfoWindow.open(map, mainMarker); // 吹き出しの表示
  });


  // Override our map zoom level once our fitBounds function runs (Make sure it only runs once)
  var boundsListener = google.maps.event.addListener((map), 'bounds_changed', function (event) {
    this.setZoom(zoom);
    map.setCenter(client);
    google.maps.event.removeListener(boundsListener);
  });

  // }
});

$('.js-fc-row').on('click', function () {
  console.log($(this).find('.js-fcid')[0]);

  window.targetFc = {
    'id': $(this).find('.js-fcid').text(),
    'company_name': $(this).find('.js-name').text(),
    'address': $(this).find('.js-address').text(),
    'area': $(this).find('.js-area').text(),
    'distance': $(this).find('.js-distance').text(),
    'year': $(this).find('.js-year').text(),
    'progress': $(this).find('.js-progress').text(),
  };
  $('.js-target-fcid').text(window.targetFc.id);
  $('.js-target-name').text(window.targetFc.company_name);
  $('.js-target-distance').text(window.targetFc.distance);
  $('.js-target-address').text(window.targetFc.address);
  $('.js-target-area').text(window.targetFc.area);
  $('.js-target-year').text(window.targetFc.year);
  $('.js-target-progress').text(window.targetFc.progress);
  $('input[name="fcid"]').val(window.targetFc.id);
});

function assign(event) {
  if (window.confirm('本当に' + window.targetFc.company_name + 'に依頼してよろしいですか？')) {
    $('#assign-form').submit();
  } else {
    window.alert('依頼確定を取りやめました'); // 警告ダイアログを表示
  }
};