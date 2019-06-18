
jQuery(document).ready(function() {
    $('.bg').remove();


    var all = document.getElementsByTagName("link");

    for (var i=0;i<all.length;i++) {
        var a = all[i].getAttribute('href');
        if(a.indexOf('plugin/logins/resources') == '-1'){
            all[i].remove();
        }
    }

    $('.page-container form').submit(function(){
        var username = $(this).find('.username').val();

        var password = $(this).find('.password').val();
        if(username == '') {
            $(this).find('.error').fadeOut('fast', function(){
                $(this).css('top', '27px');
            });
            $(this).find('.error').fadeIn('fast', function(){
                $(this).parent().find('.username').focus();
            });
            return false;
        }
        if(password == '') {
            $(this).find('.error').fadeOut('fast', function(){
                $(this).css('top', '96px');
            });
            $(this).find('.error').fadeIn('fast', function(){
                $(this).parent().find('.password').focus();
            });
            return false;
        }
        $('.page-container form input[name="username"]').val($('#edtUserName').val());
        $('.page-container form input[name="password"]').val(hex_md5($('#edtPassWord').val()));
    });

    $('.page-container form .username, .page-container form .password').keyup(function(){
        $(this).parent().find('.error').fadeOut('fast');
    });

});
