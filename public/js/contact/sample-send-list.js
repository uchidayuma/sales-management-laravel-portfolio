$(function() {
    $('#all').on("click",function(){
      $('.list').prop("checked", $(this).prop("checked"));
    });
});