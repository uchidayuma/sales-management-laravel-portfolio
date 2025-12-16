$(document).ready(function () {
    const redirectUrl = getQueryParam('redirecturl');
    console.log(redirectUrl);
    console.log(redirectUrl != null || redirectUrl != undefined);
    setTimeout(() => {
        if (redirectUrl != null || redirectUrl != undefined) {
            window.open(redirectUrl, '_blank');
        }
    }, 1000);
        //var newContacts = {!! json_encode($completeArray, JSON_HEX_TAG) !!};
        //console.log(newContacts);

/*
        var ctx = document.getElementById('myChart').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','25','26','27','28','29','30'],
                datasets: [{
                    label: '新規お問い合わせ件数',
                    //data: [12, 19, 3, 5, 2, 3],
                    data: test,
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });
*/

/*
        var ctx = document.getElementById('myChart2').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','25','26','27','28','29','30','31'],
                datasets: [{
                    label: '新規発注件数',
                    //data: [12, 19, 3, 5, 2, 3],
                    data: test2,
                    backgroundColor: 'rgba(140, 162, 235, 0.2)',
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });
*/

})