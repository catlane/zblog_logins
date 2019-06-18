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
    <script src="<?php echo $zbp->host ?>zb_users/plugin/logins/resources/js/md5.js" type="text/javascript"></script>



    <script src="<?php echo $zbp->host ?>zb_users/plugin/logins/resources/theme1/js/supersized.3.2.7.min.js"></script>

    <script src="<?php echo $zbp->host ?>zb_users/plugin/logins/resources/theme1/js/supersized-init.js"></script>

    <script src="<?php echo $zbp->host ?>zb_users/plugin/logins/resources/theme1/js/scripts.js"></script>

    <link rel="stylesheet" href="<?php echo $zbp->host ?>zb_users/plugin/logins/resources/theme1/css/reset.css">

    <link rel="stylesheet" href="<?php echo $zbp->host ?>zb_users/plugin/logins/resources/theme1/css/supersized.css">

    <link rel="stylesheet" href="<?php echo $zbp->host ?>zb_users/plugin/logins/resources/theme1/css/style.css">


    <title> - 主题2演示 <?php echo $zbp->name ?></title>
</head>
<body>


<?php

//switch (intval($_GET['theme0_bg'])){
//    case 0:
//        break;
//    case 1:
//        echo <<<eof
//<script src="{$zbp->host}zb_users/plugin/logins/resources/js/love_canvas_bg.js"></script>
//<canvas id="love" style="top:0px;position: absolute;z-index: -1;text-align: center;" width="1680" height="341"></canvas>
//eof;
//
//        break;
//    case 2:
//        echo <<<eof
//        <script src="{$zbp->host}zb_users/plugin/logins/resources/js/haike_canvas_bg.js"></script>
//<canvas id="haike" style="top:0px;position: absolute;z-index: -1;text-align: center;" width="1680" height="341"></canvas>
//eof;
//        break;
//}
?>

<div class="page-container">

    <h1><?php echo $zbp->name;?>-登录</h1>

    <form action="" method="post">

        <input type="text" name="username" class="username" placeholder="Username(仅为演示)" readonly>

        <input type="password" name="password" class="password" placeholder="Password(仅为演示)" readonly>

        <input type="checkbox" id="bcdl"><label for="bcdl">保持登录</label>
        <span id="forget"><a href="javascript:;">找回密码</a></span>
        <button type="submit">登录</button>


        <div class="error"><span>+</span></div>

    </form>

    <div class="connect">

        <p>第三方登录:</p>

        <p>

            <a href="javascript:;" style="width: 33%">
                <img src="/zb_users/plugin/logins/resources/theme1/icon/qq.png" alt="qq登录">
            </a>

            <a href="javascript:;" style="width: 33%">
                <img src="/zb_users/plugin/logins/resources/theme1/icon/sina.png" alt="微博登录">
            </a>
            <a href="javascript:;" style="width: 33%">
                <img src="/zb_users/plugin/logins/resources/theme1/icon/git.png" alt="gitHub登录">
            </a>
        </p>

    </div>

</div>
</body>
</html>
