<?php
session_start();

include 'gitHub.php';

$git_client_id = $zbp->Config('logins')->gitAppid;
$git_client_secret = $zbp->Config('logins')->gitAppkey;



$o = new SaeTOAuthV2($git_client_id, $git_client_secret);

// code换算token
if (isset($_REQUEST['code'])) {
	$keys = array();
	$keys['code'] = $_REQUEST['code'];
	$keys['redirect_uri'] = logins_Event_GetURL('gitCallback');
	$token = $o->getAccessToken('code', $keys);
} else {
	echo '系统异常 1';
	exit;
}
if ($token) {
	$_SESSION['git_token'] = $token;
	setcookie('git_js_'.$o->client_id, http_build_query($token));
} else {
	echo '系统异常 2';
	exit;
}
$git_token = $token['access_token'];

//然后获取用户信息
$userInfo = $o->getUserInfo( $git_token );

$git_uid = $userInfo['id'];
$_SESSION['git_uid'] = $git_uid; // 用户开发ID
$_SESSION['git_login'] = $userInfo[ 'login' ];
$_SESSION['git_avatar'] = $userInfo[ 'avatar_url' ];

if($userInfo['email']){
    $_SESSION[ 'git_email' ] = $userInfo[ 'email' ];
}
// 第一步 查询绑定状态
$status = logins_git_Event_GetThirdInfo($git_uid);
// 已绑定
if ($status) {
    // 执行第三方登录
    logins_git_Event_ThirdLogin($git_uid, $git_token);
} else {
    // 未绑定 再判断是否登录 如果登录就直接绑定
    if ($zbp->user->ID > 0) {
        // 执行绑定方法
        logins_git_Event_ThirdBind($git_uid, $git_token);
    } else {
        if (!session_id()) {
            session_start();
        }
		$_SESSION['git_token'] = $git_token; // 用户识别
		$_SESSION['git_uid'] = $git_uid; // 用户开发ID
        Redirect(logins_Event_GetURL('gitBind'));
    }
}

// 方法执行完毕后 回到对应页面
$sourceUrl = GetVars('sourceUrl', 'COOKIE');
if (empty($sourceUrl)) {
    $sourceUrl = $zbp->host;
}
Redirect($sourceUrl);
