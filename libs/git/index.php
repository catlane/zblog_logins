<?php
if (empty($zbp)) {
    echo '管理员已经关闭了登录';
    die();
} else if ($zbp->Config('logins')->gitActive != '1') {
    echo '管理员已经关闭了gitHub登录';
    die();
}

session_start();

include 'gitHub.php';

$git_client_id = $zbp->Config('logins')->gitAppid;
$git_client_secret = $zbp->Config('logins')->gitAppkey;


$o = new SaeTOAuthV2($git_client_id, $git_client_secret);

$code_url = $o->getAuthorizeURL(logins_Event_GetURL('gitCallback'));


$code_url .= '&scope=user:email';
// 转向到github登录
Redirect($code_url);
