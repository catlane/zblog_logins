<!DOCTYPE HTML>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge,chrome=1" />
    <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0"/>
    <meta name="renderer" content="webkit" />
    <meta name="robots" content="none" />
    <script src="<?php echo $zbp->host ?>zb_system/script/jquery-2.2.4.min.js" type="text/javascript"></script>
    <title>QQ互联用户绑定 - <?php echo $zbp->name ?></title>
</head>
<body>


<?php

if($zbp->Config('logins')->theme_id == 0) {
    switch ($zbp->Config('logins')->theme0_bg){
        case 0:
            break;
        case 1:
            echo <<<eof
<script src="{$zbp->host}zb_users/plugin/logins/resources/js/love_canvas_bg.js"></script>
<canvas id="love" style="top:0px;position: absolute;z-index: -1;text-align: center;" width="1680" height="341"></canvas>
eof;

            break;
        case 2:
            echo <<<eof
            <script src="{$zbp->host}zb_users/plugin/logins/resources/js/haike_canvas_bg.js"></script>
<canvas id="haike" style="top:0px;position: absolute;z-index: -1;text-align: center;" width="1680" height="341"></canvas>
eof;
            break;
    }
?>
    <script src="<?php echo $zbp->host ?>zb_system/script/zblogphp.js" type="text/javascript"></script>
    <script src="<?php echo $zbp->host ?>zb_system/script/c_html_js_add.php" type="text/javascript"></script>
    <script src="<?php echo $zbp->host ?>zb_users/plugin/logins/resources/js/md5.js" type="text/javascript"></script>
    <link rel="stylesheet" href="<?php echo $zbp->host ?>zb_users/plugin/logins/resources/css/style2.css" type="text/css" />
<div id="loginalert" style=" display: block;">

        <!--绑定用户-->
        <div class="pd20 loginpd logins" style="display: block;">


            <div class="loginwrap">

                <div class="loginh">

                    <div class="fl">

                        绑定网站用户

                    </div>
                    <div class="third-logo">
                        <img src="<?php echo $zbp->host; ?>zb_users/plugin/logins/resources/images/qq1.png"
                             alt="gitHub绑定用户">
                    </div>

                    <div class="clear">

                    </div>

                </div>

                <h3><span class="login_warning">用户名或密码错误</span>

                    <div class="clear">

                    </div>

                </h3>

                <div class="clear">

                </div>

                <form action="<?php echo logins_Event_GetURL( 'bind-account' ) ?>" method="post" id="login_form"
                      onsubmit="return false;">

                    <div class="logininput">

                        <input name="username" id="edtUserName" required type="text" class="loginusername"
                               placeholder="邮箱/用户名" ploc="">

                        <input name="password" type="password" required id="edtPassWord" placeholder="密码">

                    </div>
                    <div class="loginbtn">

                        <div class="loginsubmit fl">

                            <input type="submit" name="btnPost" value="绑定">

                            <div class="loginsubmiting">

                                <div class="loginsubmiting_inner">

                                </div>

                            </div>

                        </div>

                        <div class="fr"><br>

                        </div>

                        <div class="clear">

                        </div>

                    </div>
                </form>
                <div class="login-group">
                    <?php
                    if ( $zbp->Config( 'logins' )->qq_user_auto_create == 1 ) {
                        ?>
                        <div class="login-account-create">
                            还没有账户？点这儿<a href="<?php echo logins_Event_GetURL( 'create-account' ) ?>"
                                        class="login-create">生成账户</a>
                        </div>
                    <?php } ?>
                </div>
            </div>

        </div>
    </div>
<?php
}else if($zbp->Config('logins')->theme_id == 1){

?>
<script src="<?php  echo $zbp->host; ?>zb_users/plugin/logins/resources/theme1/js/supersized.3.2.7.min.js"></script>
<script src="<?php  echo $zbp->host; ?>zb_users/plugin/logins/resources/theme1/js/supersized-init.js"></script>

<script src="<?php  echo $zbp->host; ?>zb_users/plugin/logins/resources/js/md5.js"></script>
<link rel="stylesheet" href="<?php  echo $zbp->host; ?>zb_users/plugin/logins/resources/theme1/css/reset.css">
<link rel="stylesheet" href="<?php  echo $zbp->host; ?>zb_users/plugin/logins/resources/theme1/css/supersized.css">
<link rel="stylesheet" href="<?php  echo $zbp->host; ?>zb_users/plugin/logins/resources/theme1/css/style.css">
<div class="page-container">

    <h1><?php echo $zbp->name; ?>-QQ绑定</h1>

    <form action="<?php echo logins_Event_GetURL( 'bind-account' ) ?>" method="post" id="login_form">

        <input id="edtUserName" name="username" type="text" class="username" placeholder="用户名" required>

        <input id="edtPassWord" type="password" name="password" class="password" placeholder="密码" required>

        <button type="submit">绑定</button>
        <input type="hidden" name="username" value="">
        <input type="hidden" name="password" value="">

        <div class="error"><span>+</span></div>

    </form>
    <div class="login-group">
        <?php
        if ( $zbp->Config( 'logins' )->qq_user_auto_create == 1 ) {
            ?>
            <div class="login-account-create">
                还没有账户？点这儿<a href="<?php echo logins_Event_GetURL( 'create-account' ) ?>"
                            class="login-create">生成账户</a>
            </div>
        <?php } ?>
    </div>
<?php
}
?>
<script src="<?php echo $zbp->host ?>zb_users/plugin/logins/resources/layer/layer.js" type="text/javascript"></script>
<script>

!function() {
    var $form = $('#login_form');
    $form.on("submit", function() {
        var username = $('#login_form input[name="username"]').val();
        var password = $('#login_form input[name="password"]').val();
        if(username == '') {
            $(this).find('.error').fadeOut('fast', function(){
                $(this).css('top', '27px');
            });
            $(this).find('.error').fadeIn('fast', function(){
                $(this).parent().find('.username').focus();
            });
            return false;
        }
        if(password == '' || password.length <=5) {
            $(this).find('.error').fadeOut('fast', function(){
                $(this).css('top', '96px');
            });
            $(this).find('.error').fadeIn('fast', function(){
                $(this).parent().find('.password').focus();
            });
            return false;
        }

        password = hex_md5(password);
        $.ajax({
            type: "post",
            url: $form.attr("action"),
            data: {
                username: username,
                password: password
            },
            dataType: "json",
            success: function(res) {
                if (res.code == 100000) {
                    layer.open({
                        title: "提示",
                        content: "绑定成功",
                        yes: function() {
                            success()
                        },
                        end: function() {
                            success()
                        },
                        time: 3000
                    })
                } else {
                    layer.msg(res.message)
                }
            },
            error: function() {},
            complete: function() {
                layer.closeAll('loading')
            }

        })
        return false;
    });
    $('#login_form .username, #login_form .password').keyup(function(){
        $(this).parent().find('.error').fadeOut('fast');
    });
    var success = function() {
        var url = "<?php echo $zbp->host ?>";
        var sourceUrl = zbp.cookie.get('sourceUrl');
        window.location.href = sourceUrl?sourceUrl:url;
    }
}();
</script>
</body>
</html>
