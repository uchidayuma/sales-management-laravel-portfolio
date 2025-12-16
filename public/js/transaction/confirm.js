$('#post-transaction').on('click', function (e) {
  e.preventDefault();
  $(this).prop('disabled', true);
  localStorage.removeItem('transaction');
  $('#transactionForm').submit();
})

window.onload = function(){
  //HTML内に画像を表示
  if(window.appEnv !== 'circleci'){
    html2canvas(document.querySelector("#canvas"), '2d').then(canvas => {
      document.body.appendChild(canvas)
      var canvasData = canvas.toDataURL("image/jpeg").replace(new RegExp("data:image/png;base64,"),"");
      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
          "Content-Type": "application/json; charset=utf-8"
        }
      });
      $.ajax({
        url: '/transactions/canvas/ajax/upload',
        type: 'POST',
        data: JSON.stringify({ 'image' : canvasData}), 
      }) // ajax
    });
  }
}
