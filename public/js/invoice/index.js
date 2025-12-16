$(document).ready(function () {
  $('#month').on('change',function () {
    var year = $('select[name="year"]').val();
    var month = $('select[name="month"]').val();
    window.location = `/invoices?year=${year}&month=${month}`
  })
});