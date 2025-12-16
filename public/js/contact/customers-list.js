// ページが読み込まれたら１行ずつ同一顧客と発注スキップがあるかチェック
$(window).on('load', function() {
    $(".same-customer").each(function(index){
        var contactId = $(this).attr('contactId')
        // 同一顧客チェック
        if (contactId != addSameId){
            var jqxhr;
            jqxhr = $.get(`/contact/ajax/Button/${contactId}`,
            function (res) {
                if(res === 'true'){
                    $(`[contactId="${contactId}"]`).removeClass("btn-secondary");
                    $(`[contactId="${contactId}"]`).addClass("btn-info");
                    $(`[contactId="${contactId}"]`).removeClass("not-load");
                } else {
                    $(`[contactId="${contactId}"]`).removeClass("not-load");
                }
            })
            $('.js-submit-modal-open').click(function(){
                jqxhr.abort();
            });
        }
    })
    $(".js-contact").each(function(index, elem){
        const contactId = $(elem).attr('id');
        console.log(parseInt($(elem).attr('stepid')) > 6);
        console.log($(elem).attr('stepid') != "99");
        // 発注スキップチェック
        if(parseInt($(elem).attr('stepid')) > 6 && $(elem).attr('stepid') != "99"){
        console.log($(elem).attr('id'));
            $.ajax({ headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                url: `/transactions/ajax/isskip/${contactId}`,
                type: 'GET',
            })
            .then( function (res) {
                console.log('contactId : ' + contactId);
                console.log(res);
                if(res.result){
                    const target = $(elem);
                    $(target).find('.step_labels').append('<img class="step__label mb5 mr5" src="/images/icons/steps/skip.png">')
                }
            }).catch(function(error){
                console.trace(error);
                // alert('メール送信ステータスを更新できませんでした。時間を置いて実行するか、開発者にご連絡ください。')
            });
        }
    })
});

$(".remodal-close").on("click", function () {
    $(".not-load").each(function(index){
        var contactId = $(this).attr('contactId')
        if (contactId != addSameId){
            var jqxhr;
            jqxhr = $.get(`/contact/ajax/Button/${contactId}`,
            function (res) {
                if(res === 'true'){
                    $(`[contactId="${contactId}"]`).removeClass("btn-secondary");
                    $(`[contactId="${contactId}"]`).addClass("btn-info");
                    $(`[contactId="${contactId}"]`).removeClass("not-load");
                } else {
                    $(`[contactId="${contactId}"]`).removeClass("not-load");
                }
            })
            $('.js-submit-modal-open').click(function(){
                jqxhr.abort();
            });
        }
    });
});

$("#submit_select").change(function(){
    $("#submit_form").submit();
});

// 同一顧客一覧を表示させる
$(document).on('click', '.js-submit-modal-open', function () {
    var contactId = $(this).attr('contactId')
    $("#addSameContactId").val(contactId);
    // 一覧初期化
    $(".js-same-customer").empty();
    
    $.get(`/contact/ajax/${contactId}`,
        function (res) {
        $('.js-same-customer').empty();
        if (res.length > 0) {
            res.forEach(elem => {
            if(elem.own_contact == '1' && !elem.user_id != '1'){
                var displayContactId = elem.user_id + "-" + elem.id;
            } else {
                var displayContactId = elem.id;
            }
            // 電話番号にハイフンを付けて表示
            var formatTelNumber = getFormatPhone(elem.tel);
            // 電話番号の桁数が不正な場合そのまま表示
            if (!formatTelNumber){
                formatTelNumber = elem.tel;
            }
            $('.js-same-customer').append(`
                <div class="text-left mb-4 d-flex justify-content-between js-same-customer-div" id="sameListID${elem.id}">

                    <div class="h6">
                        <div>
                            <span class="font-weight-bold inline-block w6r">案件ID</span>
                            <span class="d-inline">：${displayContactId}<i class="ml10 color-link pointer fas fa-external-link-alt" onclick="window.open('../${elem.id}','subwindow','width=700','height=700');"></i></span>
                        </div>
                        <div>
                            <span class="font-weight-bold inline-block w6r">顧客名</span>
                            <span class="d-inline">：${elem.surname} ${elem.name} </span>
                        </div>
                        <div>
                            <span class="font-weight-bold inline-block w6r">顧客住所</span>
                            <span class="d-inline w-100">：${elem.pref}${elem.city}${elem.street}</span>
                        </div>
                        <div>
                            <span class="font-weight-bold inline-block w6r">顧客電話番号</span>
                            <span class="d-inline">：${formatTelNumber}</span>
                        </div>
                    </div>
                    
                    <div>
                        <button id="delete_button" type="submit" class="mx-4 mt-4 btn btn-danger" dusk='same-delete-${elem.id}' contactId="${contactId}" selectId="${elem.id}" value="${elem.id}">同一顧客解除</button>
                    </div>
                </div>
            `);
        });
        } else {
            $('.js-same-customer').append('<p class="f13 bold" id="null-contact-massage">この案件には同一顧客は存在しません</p>');
        }
    })
});

// 同一顧客の登録を解除する
$(document).on('click', '#delete_button', function (responce) {

    var contactId = $(this).attr('contactid');
    var selectId = $(this).attr('selectid');
    var selectIdValue = $(this).val();

    // 二重送信防止
    var button = $(this);
    button.attr("disabled", true);

    // 
    if (selectId === selectIdValue){
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: `/contact/ajaxSameCustomerDestroy`,
            type: 'POST',
            data: {
                contact_id : contactId,
                select_id  : selectId
            }
        })// ajax
        .then(
            // 通信成功時のコールバック
            function (res) {
                $("#sameListID"+res.select_id).remove();
                alert("同一顧客を解除しました。");
                // 二重送信防止解除
                button.attr('disabled', false);
            },
            // 通信失敗時のコールバック
            function () {
                alert("同一顧客解除できませんでした。\n時間を置いてもう一度お試しください。");
        });
    }//if
});//this

// 同一顧客を案件IDで追加する。
$(document).on('click', '#same-add-button', function (responce) {
    var contactId = $('#addSameContactId').val();
    var addSameId = $('#addSameId').val();

    // 二重送信防止
    var button = $(this);
    button.attr("disabled", true);

    // 123-45678 といった案件IDを入力された場合 "-" 以降の文字列のみに変換(123-45678 -> 45678)
	var index = addSameId.indexOf('-');
    if (index != -1){
        addSameId = addSameId.slice(index + 1);
    }
    
    // 自分の案件を同一顧客として登録しようとした場合alert
    if(contactId != addSameId){
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: `/contact/ajaxSameCustomerAdd`,
            type: 'POST',
            data: {
                contact_id : contactId,
                add_same_id  : addSameId
            }
        })// ajax
        .then(
            // 通信成功時のコールバック
            function (res) {
                // 登録しようとした顧客案件が存在するかしないかの処理
                if(res != 'null-contact'){
                    alert("同一顧客を追加しました。");
                    // 二重送信防止解除
                    button.attr('disabled', false);
                    // 案件が存在しませんのメッセージがあった場合削除
                    $("#null-contact-massage").remove();
                    // 返り値をもとにlistに追加
                    if (res.length > 0) {
                        res.forEach(elem => {
                        if(elem.own_contact == '1' && !elem.user_id != '1'){
                            var displayContactId = elem.user_id + "-" + elem.id;
                        } else {
                            var displayContactId = elem.id;
                        }
                        // 電話番号にハイフンを付けて表示
                        var formatTelNumber = getFormatPhone(elem.tel);
                        // 電話番号の桁数が不正な場合そのまま表示
                        if (!formatTelNumber){
                            formatTelNumber = elem.tel;
                        }
                        $('.js-same-customer').append(`
                            <div class="text-left mb-4 d-flex justify-content-between" id="sameListID${elem.id}">

                                <div class="h6">
                                    <div>
                                        <span class="font-weight-bold inline-block w6r">案件ID</span>
                                        <span class="d-inline">：${displayContactId}<i class="ml10 color-link pointer fas fa-external-link-alt" onclick="window.open('../${elem.id}','subwindow','width=700','height=700');"></i></span>
                                    </div>
                                    <div>
                                        <span class="font-weight-bold inline-block w6r">顧客名</span>
                                        <span class="d-inline">：${elem.surname} ${elem.name} </span>
                                    </div>
                                    <div>
                                        <span class="font-weight-bold inline-block w6r">顧客住所</span>
                                        <span class="d-inline">：${elem.pref}${elem.city}${elem.street}</span>
                                    </div>
                                    <div>
                                        <span class="font-weight-bold inline-block w6r">顧客電話番号</span>
                                        <span class="d-inline">：${formatTelNumber}</span>
                                    </div>
                                </div>
                                
                                <div>
                                    <button id="delete_button" type="submit" class="mx-4 mt-4 btn btn-danger" dusk='same-delete-${elem.id}' contactId="${contactId}" selectId="${elem.id}" value="${elem.id}">同一顧客解除</button>
                                </div>
                            </div>
                            `);
                        });
                    }
                } else {
                    alert("存在しない案件IDです。\n正しい案件IDを入力して、もう一度お試してください。");
                    // 二重送信防止解除
                    button.attr('disabled', false);
                }
            },
            // 通信失敗時のコールバック
            function () {
                alert("同一顧客追加できませんでした。\n時間を置いてもう一度お試しください。");
                // 二重送信防止解除
                button.attr('disabled', false);
        });
    } else { 
        alert("ただ今、同一顧客として登録されようとしたIDは、現在の案件のIDです。");
        // 二重送信防止解除
        button.attr('disabled', false);
    }
});//this

// 電話番号を整形する(ハイフンを付ける)
var getFormatPhone = function($INPUT, $STRICT){
	$STRICT = $STRICT || false;
	// 市外局番のグループ定義
	// データは http://www.soumu.go.jp/main_sosiki/joho_tsusin/top/tel_number/number_shitei.html より入手後、整形
	var group = {
		5 : {
			"01267" : 1, "01372" : 1, "01374" : 1, "01377" : 1, "01392" : 1, "01397" : 1, "01398" : 1, "01456" : 1, "01457" : 1, "01466" : 1, "01547" : 1,
			"01558" : 1, "01564" : 1, "01586" : 1, "01587" : 1, "01632" : 1, "01634" : 1, "01635" : 1, "01648" : 1, "01654" : 1, "01655" : 1, "01656" : 1,
			"01658" : 1, "04992" : 1, "04994" : 1, "04996" : 1, "04998" : 1, "05769" : 1, "05979" : 1, "07468" : 1, "08387" : 1, "08388" : 1, "08396" : 1,
			"08477" : 1, "08512" : 1, "08514" : 1, "09496" : 1, "09802" : 1, "09912" : 1, "09913" : 1, "09969" : 1,
		},
		4 : {
			"0123" : 2, "0124" : 2, "0125" : 2, "0126" : 2, "0133" : 2, "0134" : 2, "0135" : 2, "0136" : 2, "0137" : 2, "0138" : 2, "0139" : 2, "0142" : 2,
			"0143" : 2, "0144" : 2, "0145" : 2, "0146" : 2, "0152" : 2, "0153" : 2, "0154" : 2, "0155" : 2, "0156" : 2, "0157" : 2, "0158" : 2, "0162" : 2,
			"0163" : 2, "0164" : 2, "0165" : 2, "0166" : 2, "0167" : 2, "0172" : 2, "0173" : 2, "0174" : 2, "0175" : 2, "0176" : 2, "0178" : 2, "0179" : 2,
			"0182" : 2, "0183" : 2, "0184" : 2, "0185" : 2, "0186" : 2, "0187" : 2, "0191" : 2, "0192" : 2, "0193" : 2, "0194" : 2, "0195" : 2, "0197" : 2,
			"0198" : 2, "0220" : 2, "0223" : 2, "0224" : 2, "0225" : 2, "0226" : 2, "0228" : 2, "0229" : 2, "0233" : 2, "0234" : 2, "0235" : 2, "0237" : 2,
			"0238" : 2, "0240" : 2, "0241" : 2, "0242" : 2, "0243" : 2, "0244" : 2, "0246" : 2, "0247" : 2, "0248" : 2, "0250" : 2, "0254" : 2, "0255" : 2,
			"0256" : 2, "0257" : 2, "0258" : 2, "0259" : 2, "0260" : 2, "0261" : 2, "0263" : 2, "0264" : 2, "0265" : 2, "0266" : 2, "0267" : 2, "0268" : 2,
			"0269" : 2, "0270" : 2, "0274" : 2, "0276" : 2, "0277" : 2, "0278" : 2, "0279" : 2, "0280" : 2, "0282" : 2, "0283" : 2, "0284" : 2, "0285" : 2,
			"0287" : 2, "0288" : 2, "0289" : 2, "0291" : 2, "0293" : 2, "0294" : 2, "0295" : 2, "0296" : 2, "0297" : 2, "0299" : 2, "0422" : 2, "0428" : 2,
			"0436" : 2, "0438" : 2, "0439" : 2, "0460" : 2, "0463" : 2, "0465" : 2, "0466" : 2, "0467" : 2, "0470" : 2, "0475" : 2, "0476" : 2, "0478" : 2,
			"0479" : 2, "0480" : 2, "0493" : 2, "0494" : 2, "0495" : 2, "0531" : 2, "0532" : 2, "0533" : 2, "0536" : 2, "0537" : 2, "0538" : 2, "0539" : 2,
			"0544" : 2, "0545" : 2, "0547" : 2, "0548" : 2, "0550" : 2, "0551" : 2, "0553" : 2, "0554" : 2, "0555" : 2, "0556" : 2, "0557" : 2, "0558" : 2,
			"0561" : 2, "0562" : 2, "0563" : 2, "0564" : 2, "0565" : 2, "0566" : 2, "0567" : 2, "0568" : 2, "0569" : 2, "0572" : 2, "0573" : 2, "0574" : 2,
			"0575" : 2, "0576" : 2, "0577" : 2, "0578" : 2, "0581" : 2, "0584" : 2, "0585" : 2, "0586" : 2, "0587" : 2, "0594" : 2, "0595" : 2, "0596" : 2,
			"0597" : 2, "0598" : 2, "0599" : 2, "0721" : 2, "0725" : 2, "0735" : 2, "0736" : 2, "0737" : 2, "0738" : 2, "0739" : 2, "0740" : 2, "0742" : 2,
			"0743" : 2, "0744" : 2, "0745" : 2, "0746" : 2, "0747" : 2, "0748" : 2, "0749" : 2, "0761" : 2, "0763" : 2, "0765" : 2, "0766" : 2, "0767" : 2,
			"0768" : 2, "0770" : 2, "0771" : 2, "0772" : 2, "0773" : 2, "0774" : 2, "0776" : 2, "0778" : 2, "0779" : 2, "0790" : 2, "0791" : 2, "0794" : 2,
			"0795" : 2, "0796" : 2, "0797" : 2, "0798" : 2, "0799" : 2, "0820" : 2, "0823" : 2, "0824" : 2, "0826" : 2, "0827" : 2, "0829" : 2, "0833" : 2,
			"0834" : 2, "0835" : 2, "0836" : 2, "0837" : 2, "0838" : 2, "0845" : 2, "0846" : 2, "0847" : 2, "0848" : 2, "0852" : 2, "0853" : 2, "0854" : 2,
			"0855" : 2, "0856" : 2, "0857" : 2, "0858" : 2, "0859" : 2, "0863" : 2, "0865" : 2, "0866" : 2, "0867" : 2, "0868" : 2, "0869" : 2, "0875" : 2,
			"0877" : 2, "0879" : 2, "0880" : 2, "0883" : 2, "0884" : 2, "0885" : 2, "0887" : 2, "0889" : 2, "0892" : 2, "0893" : 2, "0894" : 2, "0895" : 2,
			"0896" : 2, "0897" : 2, "0898" : 2, "0920" : 2, "0930" : 2, "0940" : 2, "0942" : 2, "0943" : 2, "0944" : 2, "0946" : 2, "0947" : 2, "0948" : 2,
			"0949" : 2, "0950" : 2, "0952" : 2, "0954" : 2, "0955" : 2, "0956" : 2, "0957" : 2, "0959" : 2, "0964" : 2, "0965" : 2, "0966" : 2, "0967" : 2,
			"0968" : 2, "0969" : 2, "0972" : 2, "0973" : 2, "0974" : 2, "0977" : 2, "0978" : 2, "0979" : 2, "0980" : 2, "0982" : 2, "0983" : 2, "0984" : 2,
			"0985" : 2, "0986" : 2, "0987" : 2, "0993" : 2, "0994" : 2, "0995" : 2, "0996" : 2, "0997" : 2,
			"0180" : 3, "0570" : 3, "0800" : 3, "0990" : 3, "0120" : 3,
		},
		3 : {
			"011" : 3, "015" : 3, "017" : 3, "018" : 3, "019" : 3, "022" : 3, "023" : 3, "024" : 3, "025" : 3, "026" : 3, "027" : 3, "028" : 3, "029" : 3,
			"042" : 3, "043" : 3, "044" : 3, "045" : 3, "046" : 3, "047" : 3, "048" : 3, "049" : 3, "052" : 3, "053" : 3, "054" : 3, "055" : 3, "058" : 3,
			"059" : 3, "072" : 3, "073" : 3, "075" : 3, "076" : 3, "077" : 3, "078" : 3, "079" : 3, "082" : 3, "083" : 3, "084" : 3, "086" : 3, "087" : 3,
			"088" : 3, "089" : 3, "092" : 3, "093" : 3, "095" : 3, "096" : 3, "097" : 3, "098" : 3, "099" : 3,
			"050" : 4, "020" : $STRICT ? 3 : 4, "070" : $STRICT ? 3 : 4, "080" : $STRICT ? 3 : 4, "090" : $STRICT ? 3 : 4,
		},
		2 : {
			"03" : 4, "04" : 4, "06" : 4,
		}
	};
	// 市外局番の桁数を取得して降順に並べ替える
	var code = [];
	for(num in group){
		code.push(num * 1);
	}
	code.sort(function($a, $b){ return ($b - $a); });
	// 入力文字から数字以外を削除してnumber変数に格納する
	var number = String($INPUT).replace(/[０-９]/g, function($s){
	                  return String.fromCharCode($s.charCodeAt(0) - 65248);
	              }).replace(/\D/g, "");
	// 電話番号が10～11桁じゃなかったらfalseを返して終了する
	if(number.length < 10 || number.length > 11){
		return false;
	}
	// 市外局番がどのグループに属するか確認していく
	for(var i = 0, n = code.length; i < n; i++){
		var leng = code[i];
		var area = number.substr(0, leng);
		var city = group[leng][area];
		// 一致する市外局番を見付けたら整形して整形後の電話番号を返す
		if(city){
			return area + "-"
			         + number.substr(leng, city)
			           + (number.substr(leng + city) !== "" ?
			              "-" + number.substr(leng + city) : "");
		}
	}
};

// 選択式CSVダウンロード window on load

window.addEventListener('load', function() {
  $('#contact-types').multiselect({
    includeSelectAllOption: true,
    selectAllText: '全て選択',
  });
  $('#prefectures').multiselect({
    maxHeight: 300,
    includeSelectAllOption: true,
    selectAllText: '全て選択',
  });
  $('#steps').multiselect({
    includeSelectAllOption: true,
    selectAllText: '全て選択',
  });
  $('#custom-all-check').on('click', function(){
    document.querySelectorAll('input[name="export[]"]').forEach(function(checkbox) {
      checkbox.checked = true;
    });
  });
  $('#custom-all-out').on('click', function(){
    document.querySelectorAll('input[name="export[]"]').forEach(function(checkbox) {
      checkbox.checked = false;
    });
  });

    $('.js-custom-start-date, .js-custom-end-date').on('change', function() {
    // 日付の取得
        var startDate = new Date(document.querySelector('.js-custom-start-date').value);
        var endDate = new Date(document.querySelector('.js-custom-end-date').value);

        console.log(startDate, endDate);
        // 差分の計算
        var diffTime = Math.abs(endDate - startDate);
        var diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)); 

        // 差分が一年以内かどうかのチェック
        if( startDate != 'InvalidDate' && endDate != 'InvalidDate' && diffDays > 365) {
            console.log("差分は一年以上です");
            alert('出力期間は1年間以内で設定してください。')
            // this input value delete
            $(this).val('');
        } else {
            console.log("差分は一年以内です");
        }
    });
});