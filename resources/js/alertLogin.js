$ ( function () {


    /**
     * 加载文件
     */
    dynamicLoadJs ( '/zb_users/plugin/logins/resources/js/md5.js');
    dynamicLoadCss('/zb_users/plugin/logins/resources/theme3/css/reset.css');
    dynamicLoadCss('/zb_users/plugin/logins/resources/theme3/css/common.css');

    /**
     * 调节背景图宽和高,初始化
     * @type {jQuery|HTMLElement}
     */

    function setInit (  ) {
        // $ ( 'body' ).css ( 'overflow-y' , 'hidden' );
        // $ ( '.login_wrap' ).css ('height', $ ( window ).height () );
        // $ ( '.login_wrap' ).css ('width', $ ( window ).width () );
        // $ ( '.login_wrap' ).show ();
        // $ ( '.login_wrap' ).animate ( { 'top': 0 } , 500 );
        $('.login_box').css('left',($ ( window ).width () / 2) - 250 )
        $('.login_box').css('top',($ ( window ).height () / 2) - 180 )
    }

    setInit ();
    $(window).resize(function () {
        setInit ();
    })

    //首先找到所有的a标签
    var allA = $ ( 'a' );
    var allALength = allA.length;
    for ( var i = 0 ; i < allALength ; i++ ) {
        var aHtml = $(allA[i]).html();
        if(aHtml.indexOf('登录') != '-1'){
            //然后获取到登录这个a事件
            $(allA[i]).click(function () {
                $('.login_box').css('left',($ ( window ).width () / 2) - 250 );
                $('.login_box').css('top',($ ( window ).height () / 2) - 180 );
                $('.login_wrap').fadeIn(100);//登录框
                $('.login_box').slideDown(100);//最外边的框
                $ ( '.login_box' ).css ( 'overflow' , 'unset' );
                $ ( 'body' ).css ( 'overflow-y' , 'hidden' );
                //然后，弹出登录按钮
                return false;
            })
        }
    }

    //关闭按钮
    $('.login_title .close').click(function () {
        $ ( 'body' ).css ( 'overflow-y' , 'scroll' );
        $('.login_wrap').fadeOut(100);
        $('.login_box').slideUp(200);
    })


    $('#login_form').submit(function () {
        $('.ececk_warning').html('<span></span>');
        $('#login_form input[name="username"]').val($('#edtUserName').val());
        $('#login_form input[name="password"]').val(hex_md5($('#edtPassWord').val()));

        if($('#edtUserName').val() == ''){
            $('.ececk_warning').html('<span>用户名为空</span>');
            return false;
        }
        if($('#edtPassWord').val() == '' || $("#edtPassWord").val() <= 6){
            $('.ececk_warning').html('<span>密码为空或太短</span>');
            return false;
        }
        //然后ajax提交数据
        $.ajax ( {
            url: 'zb_system/cmd.php?act=verify' ,
            data: $ ( this ).serialize (),
            type: 'post' ,
            success: function ( res ) {
                //然后关闭页面,
                window.location.reload ();
            } ,
            error: function ( res ) {
                //开启开发者模式
                if(res.responseText.indexOf('8, __FILE__, __LINE__')){
                    $('.ececk_warning').html('<span>用户名或者密码错误</span>');
                }else if(res.responseText.indexOf('正确的用户名和密码')){
                    $('.ececk_warning').html('<span>用户名或者密码错误</span>');
                }else{
                    $('.ececk_warning').html('<span>登录失败</span>');
                }
            }
        } );

        return false;
    })


    $("#edtUserName").focus(function () {
        $('.ececk_warning').html('<span></span>');
    })
    $("#edtPassWord").focus(function () {
        $('.ececk_warning').html('<span></span>');
    })

    $('.other_right a').click(function () {
        var url = $ ( this ).attr ('href');
        window.location.href = url;
        // window.top.location.href = url
    })

} )

/**
 * 动态加载JS
 * @param {string} url 脚本地址
 * @param {function} callback  回调函数
 */
function dynamicLoadJs(url, callback) {
    var head = document.getElementsByTagName('head')[0];
    var script = document.createElement('script');
    script.type = 'text/javascript';
    script.src = url;
    if(typeof(callback)=='function'){
        script.onload = script.onreadystatechange = function () {
            if (!this.readyState || this.readyState === "loaded" || this.readyState === "complete"){
                callback();
                script.onload = script.onreadystatechange = null;
            }
        };
    }
    head.appendChild(script);
}


/**
 * 动态加载CSS
 * @param {string} url 样式地址
 */
function dynamicLoadCss(url) {
    var head = document.getElementsByTagName('head')[0];
    var link = document.createElement('link');
    link.type='text/css';
    link.rel = 'stylesheet';
    link.href = url;
    head.appendChild(link);
}


