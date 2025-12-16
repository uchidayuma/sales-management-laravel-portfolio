// 同一顧客を案件IDで追加する。
$(document).on('click', '#addCsvForm', function (responce) {
    var csvFormData = $('#addScvFormData').val();

    console.log(csvFormData);

    // 二重送信防止
    var button = $(this);
    button.attr("disabled", true);
    // 自分の案件を同一顧客として登録しようとした場合alert
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: `/settings/add_csv_export_options`,
        type: 'POST',
        data: {
            csv_form_data : csvFormData
        }
    })// ajax
    .then(
        // 通信成功時のコールバック
        function (res) {
            alert("フォーム名を追加しました。");
            // 二重送信防止解除
            button.attr('disabled', false);
            location.reload();
        },
        // 通信失敗時のコールバック
        function () {
            alert("フォーム名を追加できませんでした。\n時間を置いてもう一度お試しください。");
            // 二重送信防止解除
            button.attr('disabled', false);
        }
    );
});//this

$(document).on('click', '#deleteCsvForm', function (responce) {
    var csvOptionId = $(this).attr('csv_option_id');

    // 二重送信防止
    var button = $(this);
    button.attr("disabled", true);
    // 自分の案件を同一顧客として登録しようとした場合alert
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: `/settings/delete_csv_export_options`,
        type: 'POST',
        data: {
            csv_form_id : csvOptionId
        }
    })// ajax
    .then(
        // 通信成功時のコールバック
        function (res) {
            alert("フォーム名を削除しました。");
            // 二重送信防止解除
            button.attr('disabled', false);
            // 削除した列を表示上からも削除
            location.reload();
        },
        // 通信失敗時のコールバック
        function () {
            alert("フォーム名を追削除できませんでした。\n時間を置いてもう一度お試しください。");
            // 二重送信防止解除
            button.attr('disabled', false);
        }
    );
});//this