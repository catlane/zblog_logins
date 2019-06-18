<?php
if (empty($zbp)) {
    echo '管理员已经关闭了登录';
    die();
} else if ($zbp->Config('logins')->sinaActive != '1') {
    echo '管理员已经关闭了新浪微博登录';
    die();
}

session_start();

include 'saetv2.ex.class.php';

$weibo_appkey = $zbp->Config('logins')->sinaAppid;
$weibo_appsecret = $zbp->Config('logins')->sinaAppkey;

$o = new SaeTOAuthV2($weibo_appkey, $weibo_appsecret);

$code_url = $o->getAuthorizeURL(logins_Event_GetURL('sinaCallback'));

// 转向到新浪微博登录
Redirect($code_url);
