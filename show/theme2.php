<?php
require '../../../../zb_system/function/c_system_base.php';
require '../../../../zb_system/function/c_system_admin.php';
$zbp->Load();
?>
<!DOCTYPE HTML>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge,chrome=1" />
    <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0"/>
    <meta name="renderer" content="webkit" />
    <meta name="robots" content="none" />
    <script src="<?php echo $zbp->host ?>zb_system/script/jquery-2.2.4.min.js" type="text/javascript"></script>
    <script src="/zb_users/plugin/logins/resources/js/alertLogin.js"></script>

    <title> - 主题2演示 <?php echo $zbp->name ?></title>
</head>
<body>

<a href="javascript:;">登录</a>

<div class="wrap login_wrap"></div>
<div class="login_box">
    <div class="login_title">
        登录
        <span class="close"></span>
    </div>
    <form action="javascript:;" method="post" id="login_form">

        <div class="form_text_ipt">
            <input id="edtUserName" type="text" placeholder="手机号/邮箱" readonly>
        </div>
        <div class="form_text_ipt">
            <input id="edtPassWord" type="password" placeholder="密码" readonly>
        </div>
        <div class="ececk_warning"><span></span></div>
        <div class="form_check_ipt">
            <div class="left check_left">
                <label><input name="checkbox" type="checkbox"> 保持登录</label>
            </div>
            <div class="right check_right">
                <a href="#">忘记密码</a>
            </div>
        </div>
        <div class="form_btn">
            <button type="submit">登录</button>
        </div>
        <input type="hidden" name="savedate" value="30">
        <input type="hidden" name="username">
        <input type="hidden" name="password" >
    </form>
    <div class="other_login">
        <div class="left other_left">
            <span>其它登录方式</span>
        </div>
        <div class="right other_right">
            <a href="javascript:;">QQ登录</a>
        </div>
    </div>
</div>

</body>
</html>
