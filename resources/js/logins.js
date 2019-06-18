
$(function () {
    $('.bg').remove();
    $('#login_form').submit(function () {
        $('#login_form input[name="username"]').val($('#edtUserName').val());
        $('#login_form input[name="password"]').val(hex_md5($('#edtPassWord').val()));
        return true;
    })

    function loadScript(url, callback) {

        var script = document.createElement("script");

        script.type = "text/javascript";

        if (typeof(callback) != "undefined") {

            if (script.readyState) {

                script.onreadystatechange = function() {

                    if (script.readyState == "loaded" || script.readyState == "complete") {

                        script.onreadystatechange = null;

                        callback();

                    }

                };

            } else {

                script.onload = function() {

                    callback();

                };

            }

        };

        script.src = url;

        document.body.appendChild(script);

    }




    //初始化添加验证码
    // function addCaptcha(c) {
    //     html = $(c).find('.logininput').html();
    //     html += '<div id="demo-inline-down"></div>';
    //     $(c).find('.logininput').html(html);
    //
    //     $(c).siblings('.pd20').find('#demo-inline-down').remove();
    //
    //     loadScript('https://cdn.dingxiang-inc.com/ctu-group/captcha-ui/index.js',function () {
    //
    //         _dx.Captcha(document.getElementById('demo-inline-down'), {
    //             appId: '5d4252a54b68bca1dc795913e4820154',
    //             style: 'inline',
    //             inlineFloatPosition: 'down',
    //         })
    //     });
    // }
    // addCaptcha('.logins');

});