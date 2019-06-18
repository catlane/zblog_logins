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
    <link rel="stylesheet" href="<?php echo $zbp->host ?>zb_users/plugin/logins/resources/css/style2.css" type="text/css" />
    <title>主题1演示 - <?php echo $zbp->name ?></title>
</head>
<body>


<?php

switch (intval($_GET['theme0_bg'])){
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

    <div id="loginalert" style=" display: block;">

        <!--绑定用户-->
        <div class="pd20 loginpd logins" style="display: block;">


            <div class="loginwrap">

                <div class="loginh">

                    <div class="fl">

                        会员登录

                    </div>

                    <div class="fr">

                        还没有账号<a id="sigup_now" href="javascript:;" onclick="return false;">第三方登录咯</a>

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

                <form action="javascript:;" method="post" id="login_form">

                    <div class="logininput">

                        <input id="edtUserName" readonly type="text" class="loginusername" placeholder="邮箱/用户名(仅为展示)">

                        <input type="password" readonly id="edtPassWord" placeholder="密码(仅为展示)">

                    </div>



                    <div class="loginbtn">

                        <div class="loginsubmit fl">

                            <input type="submit" name="btnPost" value="登录">

                            <div class="loginsubmiting">

                                <div class="loginsubmiting_inner">

                                </div>

                            </div>

                        </div>

                        <div class="logcheckbox fl">
                            <input id="bcdl" type="checkbox" name="chkRemember">
                            <label for="bcdl">保持登录</label>
                        </div>

                        <div class="fr"><a href="javascript:;" class="forget_pwd">忘记密码?</a>

                        </div>

                        <div class="clear">

                        </div>

                    </div>
                    <input type="hidden" name="savedate" value="30">
                    <input type="hidden" name="username" value="">
                    <input type="hidden" name="password" value="">
                </form>

            </div>

        </div>
        <!--第三方登录-->
        <div class="thirdlogin">

            <div class="pd50">

                <h4>用第三方帐号直接登录(登录之后绑定本站账号)</h4>

                <ul>

                    <li style="width:33%;">
                        <a href="javascript:;">
                            <img src="/zb_users/plugin/logins/resources/images/qq1.png" alt="qq登录">
                        </a>
                    </li><li style="width:33%;">
                        <a href="javascript:;">
                            <img src="/zb_users/plugin/logins/resources/images/sina.png" alt="微博登录">
                        </a>
                    </li><li style="width:33%;">
                        <a href="javascript:;">
                            <img src="/zb_users/plugin/logins/resources/images/git.png" alt="gitHub登录">
                        </a>
                    </li>

                    <div class="clear">

                    </div>

                </ul>

                <div class="clear">

                </div>

            </div>

        </div>
    </div>
</body>
</html>
