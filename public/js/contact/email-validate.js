$(document).ready(function () {
    console.log(window.emailValidateEndpoint, window.emailValidateApiKey)
    $('input[name="c[email]"]').on('blur', function () {
        if ($(this)[0].value) {
            $.get(`${window.emailValidateEndpoint}?api_key=${window.emailValidateApiKey}&email=${$(this)[0].value}`,
                function (res) {
                    console.log(res);
                    if (res.is_valid_format.value === false) {
                        alert(`${res.email}は不正なメールアドレスである可能性があります。`)
                    }
                })
        }
    })
});