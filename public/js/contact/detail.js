$(document).ready(function () {
    $(".datepicker").flatpickr({
        enableTime: true,
        dateFormat: "Y-m-d H:i",
        locale: "ja"
    });

    $("#submit").click(function(){
        if(!confirm('本当にアポ日時を確定しますか？')){
            //キャンセル時の処理
            return false;
        }
        });

})